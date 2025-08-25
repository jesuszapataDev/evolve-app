<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\UserModel;
use App\Config\Database;
use App\Config\ClientEnvironmentInfo;
use App\Config\TimezoneManager;
require_once APP_ROOT . '/helpers/mailHelper.php';

class PasswordRecoveryService
{
    private UserModel $userModel;
    private $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        // Asumimos que tienes un singleton Database que retorna mysqli
        $this->db = Database::getInstance();
    }

    private function resp(bool $value, string $message = '', $data = null, int $status = 200): array
    {
        return ['value' => $value, 'message' => $message, 'data' => $data, 'status' => $status];
    }

    private function loadLang(string $lang): array
    {
        $code = strtoupper($lang ?: 'EN');
        $file = APP_ROOT . "/lang/{$code}.php";
        if (!is_file($file)) $file = APP_ROOT . "/lang/EN.php";
        /** @var array $t */
        $t = include $file;
        return $t ?? [];
    }

    /** Obtiene created_at del último reset de un email (o null si no existe) */
    private function getLastResetAt(string $email): ?int
    {
        $stmt = $this->db->prepare("SELECT created_at FROM password_resets WHERE email = ?");
        if (!$stmt) return null;
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc() ?: null;
        $stmt->close();
        return $row ? strtotime((string)$row['created_at']) : null;
    }

    /** Inserta/actualiza token de reset y devuelve el token generado */
    private function upsertResetToken(string $email): ?string
    {
        $token = bin2hex(random_bytes(32));
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $this->db->prepare(
            "INSERT INTO password_resets (email, token, created_at)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE token = VALUES(token), created_at = VALUES(created_at)"
        );
        if (!$stmt) return null;
        $stmt->bind_param("sss", $email, $token, $createdAt);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok ? $token : null;
    }

    /** Elimina el reset token de un email (p. ej., tras usarlo o expirar) */
    private function deleteResetByEmail(string $email): void
    {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
        if (!$stmt) return;
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
    }

    /** Busca email por token; retorna ['email'=>string,'created_at'=>int] o null */
    private function getResetByToken(string $token): ?array
    {
        $stmt = $this->db->prepare("SELECT email, created_at FROM password_resets WHERE token = ?");
        if (!$stmt) return null;
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc() ?: null;
        $stmt->close();
        if (!$row) return null;
        return ['email' => (string)$row['email'], 'created_at' => strtotime((string)$row['created_at'])];
    }

    private function sendPasswordResetEmail(string $email, string $lang): bool
    {
        $token = $this->upsertResetToken($email);
        if (!$token) return false;

        $host    = rtrim((string)($_ENV['APP_URL'] ?? 'https://localhost'), '/');
        $logoUrl = $host . '/public/assets/images/logo-index.png';
        $langSeg = strtoupper($lang) === 'ES' ? '/es' : '';
        $resetLink = "{$host}/recovery_password{$langSeg}?token={$token}";

        $subject = strtoupper($lang) === 'ES'
            ? 'Restablece tu contraseña'
            : 'Reset your password';

        $body = strtoupper($lang) === 'ES'
            ? "
                <div style='font-family:sans-serif;color:#000'>
                  <div style='text-align:right'><img src='{$logoUrl}' alt='Logo' style='height:50px'/></div>
                  <h2>¿Restablecer tu contraseña?</h2>
                  <p>Recibimos una solicitud para restablecer tu contraseña.</p>
                  <p><a href='{$resetLink}' style='background:#254a7e;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none'>Restablecer contraseña</a></p>
                  <p>O copia este enlace:</p>
                  <p style='word-break:break-all'><a href='{$resetLink}'>{$resetLink}</a></p>
                  <p>Este enlace expira en <strong>10 minutos</strong>.</p>
                </div>
              "
            : "
                <div style='font-family:sans-serif;color:#000'>
                  <div style='text-align:right'><img src='{$logoUrl}' alt='Logo' style='height:50px'/></div>
                  <h2>Reset your password?</h2>
                  <p>We received a request to reset your password.</p>
                  <p><a href='{$resetLink}' style='background:#254a7e;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none'>Reset password</a></p>
                  <p>Or copy this link:</p>
                  <p style='word-break:break-all'><a href='{$resetLink}'>{$resetLink}</a></p>
                  <p>This link expires in <strong>10 minutes</strong>.</p>
                </div>
              ";

        $mailer = new \MailHelper();
        return $mailer->sendMail($email, $subject, $body);
    }

    /** POST /verifyEmail → valida email y envía link si el usuario existe (con throttle 10m) */
    public function verifyEmail(?string $email, string $lang = 'EN'): array
    {
        $t = $this->loadLang($lang);
        $email = trim((string)$email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->resp(false, $t['invalid_email_format'] ?? 'Invalid email format.', null, 400);
        }

        $user = $this->userModel->getUserByEmail($email);
        if (!$user) {
            return $this->resp(false, $t['email_not_found'] ?? 'Email not found.', null, 404);
        }

        // Throttle: 10 minutos
        $last = $this->getLastResetAt($email);
        if ($last && (time() - $last) < 600) {
            return $this->resp(false,
                $t['reset_email_recently_sent'] ?? 'A reset email was already sent recently. Please wait a few minutes.',
                null, 429
            );
        }

        $sent = $this->sendPasswordResetEmail($email, $lang);
        if ($sent) {
            return $this->resp(true,
                $t['reset_email_sent'] ?? 'A reset link has been sent to your email.'
            );
        }
        return $this->resp(false,
            $t['reset_email_failed'] ?? 'Failed to send the reset email.',
            null, 500
        );
    }

    /** POST /updatePassword → actualiza con userId o token (enforce expiración 10m) */
    public function updatePassword(string $newPassword, ?string $userId, ?string $token, string $lang = 'EN'): array
    {
        $t = $this->loadLang($lang);
        $newPassword = trim($newPassword);

        if (strlen($newPassword) < 6) {
            return $this->resp(false, $t['password_too_short'] ?? 'Password must be at least 6 characters.', null, 400);
        }

        $targetUserId = $userId ? (string)$userId : null;
        $email = null;

        // Resolver por token si no viene userId
        if (!$targetUserId && $token) {
            $reset = $this->getResetByToken($token);
            if (!$reset) {
                return $this->resp(false, $t['token_expired'] ?? 'The recovery link has expired. Please request a new one.', null, 400);
            }
            // Expiración 10m
            if ((time() - (int)$reset['created_at']) > 600) {
                $this->deleteResetByEmail($reset['email']);
                return $this->resp(false, $t['token_expired'] ?? 'The recovery link has expired. Please request a new one.', null, 400);
            }
            $email = $reset['email'];
            $u = $this->userModel->getUserByEmail($email);
            $targetUserId = $u['user_id'] ?? null;
            if (!$targetUserId) {
                return $this->resp(false, $t['email_not_found'] ?? 'Email not found.', null, 404);
            }
        }

        if (!$targetUserId && !$token) {
            return $this->resp(false, $t['missing_user_or_token'] ?? 'Missing token or user ID.', null, 400);
        }

        // Update
        $ok = $this->userModel->updatePassword([
            'newPassword' => $newPassword,
            'userId'      => $targetUserId,
            'token'       => $token
        ]);

        if (!$ok) {
            return $this->resp(false, $t['password_update_failed'] ?? 'Failed to update the password. Please try again.', null, 500);
        }

        // Invalidar token si conocemos el email
        if (!$email && $targetUserId) {
            $u = $this->userModel->getById($targetUserId);
            $email = $u['email'] ?? null;
        }
        if ($email) {
            $this->deleteResetByEmail($email);
            $this->sendPasswordChangedNotice($email, $lang);
        }

        return $this->resp(true, $t['password_update_success'] ?? 'Password updated successfully. You will be redirected to login.');
    }

    private function sendPasswordChangedNotice(string $email, string $lang): void
    {
        $host    = rtrim((string)($_ENV['APP_URL'] ?? 'https://localhost'), '/');
        $logoUrl = $host . '/public/assets/images/logo-index.png';
        $login   = $host . '/login';

        $subject = strtoupper($lang) === 'ES'
            ? 'Tu contraseña ha sido actualizada'
            : 'Your password has been changed';

        $body = strtoupper($lang) === 'ES'
            ? "
                <div style='font-family:sans-serif;color:#000'>
                  <div style='text-align:right'><img src='{$logoUrl}' alt='Logo' style='height:50px'/></div>
                  <h2>Tu contraseña fue actualizada</h2>
                  <p>Si no fuiste tú, por favor cambia tu contraseña nuevamente y contacta a soporte.</p>
                  <p><a href='{$login}'>Acceder</a></p>
                </div>
              "
            : "
                <div style='font-family:sans-serif;color:#000'>
                  <div style='text-align:right'><img src='{$logoUrl}' alt='Logo' style='height:50px'/></div>
                  <h2>Your password was updated</h2>
                  <p>If this wasn’t you, please reset it again and contact support.</p>
                  <p><a href='{$login}'>Sign in</a></p>
                </div>
              ";

        $mailer = new \MailHelper();
        try { $mailer->sendMail($email, $subject, $body); } catch (\Throwable $e) { /* log opcional */ }
    }

    /** Utilidad usada en registros/validaciones */
    public function checkEmailAvailability(?string $email, string $lang = 'EN'): array
    {
        $t = $this->loadLang($lang);
        $email = strtolower(trim((string)$email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->resp(false, $t['invalid_email'] ?? 'Invalid email address.', null, 400);
        }
        $user = $this->userModel->getUserByEmail($email);
        return $user
            ? $this->resp(false, $t['email_already_registered'] ?? 'This email has already been used.')
            : $this->resp(true, $t['email_available'] ?? 'Email is available.');
    }

    public function checkTelephoneAvailability(?string $telephone, string $lang = 'EN'): array
    {
        $t = $this->loadLang($lang);
        $clean = preg_replace('/\D/', '', (string)$telephone);
        if ($clean === '') {
            return $this->resp(false, $t['invalid_telephone'] ?? 'Invalid telephone number.', null, 400);
        }
        $user = $this->userModel->getUserByTelephone($clean);
        return $user
            ? $this->resp(false, $t['telephone_already_used'] ?? 'This telephone has already been used.')
            : $this->resp(true, $t['telephone_available'] ?? 'Telephone is available.');
    }
}
