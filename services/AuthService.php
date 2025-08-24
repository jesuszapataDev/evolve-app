<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\SessionManagementModel;
use App\Models\UserModel;
use App\Models\AuthModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once APP_ROOT . '/vendor/phpmailer/phpmailer/src/Exception.php';
require_once APP_ROOT . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once APP_ROOT . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once APP_ROOT . '/helpers/login_helpers.php';

class AuthService
{
    private AuthModel $authModel;
    private UserModel $userModel;
    private SessionManagementModel $sessionModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->userModel = new UserModel();
        $this->sessionModel = new SessionManagementModel();
    }

    /* =================
     * Helpers comunes
     * ================= */
    private function resp(bool $value, string $message = '', $data = null, int $status = 200): array
    {
        return [
            'value' => $value,
            'message' => $message,
            'data' => $data,
            'status' => $status,
        ];
    }

    private function loadLang(string $langCode): array
    {
        $code = strtoupper($langCode ?: 'EN');
        $file = APP_ROOT . "/lang/{$code}.php";
        if (!is_file($file)) {
            $file = APP_ROOT . "/lang/EN.php";
        }
        return include $file;
    }

    private function normalizeRole(?string $rol): string
    {
        $normalized = ucfirst(strtolower($rol ?? 'User'));
        return in_array($normalized, ['User', 'Administrator'], true) ? $normalized : 'User';
    }

    /* =================
     *  Casos de uso
     * ================= */

    /**
     * Login con auditoría, bloqueo por intentos y manejo de sesión.
     * $meta proviene de server/env: user_agent, remote_addr, etc.
     */
    public function login(array $input, array $meta): array
    {
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $language = strtoupper((string) ($input['language'] ?? 'EN'));

        $deviceId = $input['device_id'] ?? null;
        $deviceType = $input['device_type'] ?? null;
        $userAgent = $input['user_agent'] ?? ($meta['HTTP_USER_AGENT'] ?? 'Unknown');
        $ipAddress = $meta['REMOTE_ADDR'] ?? 'unknown';
        $userType = 'user';

        $lang = $this->loadLang($language);

        // Protección por intentos
        $maxAttempts = 3;
        $lockoutTime = 60; // seg
        $attemptKey = 'login_attempts_' . md5($email);
        $now = time();
        $attemptData = $_SESSION[$attemptKey] ?? ['count' => 0, 'last_attempt' => 0, 'locked_until' => 0];

        if ($attemptData['locked_until'] > $now) {
            $wait = (int) ceil(($attemptData['locked_until'] - $now) / 60);
            $details = \getFailureDetails('too_many_attempts');

            $usuario = null;
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $usuario = $this->userModel->getUserByEmail($email);
                } catch (\Throwable $t) {
                }
            }

            // Auditoría
            $this->sessionModel->create(
                $usuario['user_id'] ?? null,
                $userType,
                $deviceId,
                $deviceType,
                false,
                $details['reason']
            );

            return $this->resp(false, $lang['failure_' . $details['code']] ?? $details['reason'], [
                'code' => $details['code'],
                'wait_minutes' => $wait
            ], 429);
        }

        // Validaciones iniciales
        if ($email === '' || $password === '') {
            $details = \getFailureDetails('missing_fields');
            $this->sessionModel->create(null, $userType, $deviceId, $deviceType, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->resp(false, $lang['failure_' . $details['code']] ?? $details['reason'], ['code' => $details['code']], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $details = \getFailureDetails('invalid_email_format');
            $this->sessionModel->create(null, $userType, $deviceId, $deviceType, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->resp(false, $lang['failure_' . $details['code']] ?? $details['reason'], ['code' => $details['code']], 400);
        }

        // Autenticación
        try {
            $usuario = $this->userModel->getUserByEmail($email);
            $failureCode = null;

            if (!$usuario) {
                $failureCode = 'user_not_found';
            } elseif ((int) ($usuario['status'] ?? 0) === 0) {
                $failureCode = 'user_blocked';
            } elseif (!password_verify($password, (string) $usuario['password'])) {
                $failureCode = 'invalid_password';
            }

            if ($failureCode) {
                $details = \getFailureDetails($failureCode);

                $this->sessionModel->create(
                    $usuario['user_id'] ?? null,
                    $userType,
                    $deviceId,
                    $deviceType,
                    false,
                    $details['reason']
                );

                $attemptData['count']++;
                $attemptData['last_attempt'] = $now;

                if ($attemptData['count'] >= $maxAttempts && isset($usuario['user_id'])) {
                    error_log("⛔ Intentos fallidos alcanzados para usuario: " . $usuario['user_id']);
                    $bloqueado = $this->userModel->updateStatus(['user_id' => $usuario['user_id'], 'status' => 0]);
                    if (!$bloqueado) {
                        error_log("❌ Falló el bloqueo del usuario con ID: " . $usuario['user_id']);
                    } else {
                        error_log("✅ Usuario bloqueado correctamente: " . $usuario['user_id']);
                    }
                    $failureCode = 'user_blocked';
                    $details = \getFailureDetails($failureCode);
                }

                if ($attemptData['count'] >= $maxAttempts) {
                    $attemptData['locked_until'] = $now + $lockoutTime;
                }
                $_SESSION[$attemptKey] = $attemptData;

                return $this->resp(false, $lang['failure_' . $details['code']] ?? $details['reason'], ['code' => $details['code']], 401);
            }

            // ✅ LOGIN EXITOSO → set sesión
            $_SESSION['idioma'] = $language;
            $_SESSION['user_id'] = $usuario['user_id'];
            $_SESSION['roles_user'] = $this->normalizeRole($usuario['rol'] ?? 'User');
            $_SESSION['sex'] = $usuario['sex'] ?? null;
            $_SESSION['timezone'] = $usuario['timezone'] ?? null;
            $_SESSION['user_name'] = trim(($usuario['first_name'] ?? '') . ' ' . ($usuario['last_name'] ?? ''));
            $_SESSION['logged_in'] = true;
            $_SESSION['user_type'] = $userType;

            unset($_SESSION[$attemptKey]);

            $sessionId = $this->sessionModel->create(
                $usuario['user_id'],
                $userType,
                $deviceId,
                $deviceType,
                true,
                null
            );
            $_SESSION['session_id'] = $sessionId;

            return $this->resp(true, $lang['login_success_yes'] ?? 'Login successful', ['redirect' => '/dashboard'], 200);

        } catch (Exception $e) {
            error_log("⚠️ Excepción en login: " . $e->getMessage());
            $this->sessionModel->create(
                $usuario['user_id'] ?? null,
                $userType,
                $deviceId,
                $deviceType,
                false,
                'Excepción: ' . $e->getMessage()
            );
            return $this->resp(false, $lang['login_success_no'] ?? 'Login failed', [
                'code' => 'unknown_failure',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function registrar(array $data): array
    {
        $language = strtoupper($data['language'] ?? 'EN');
        $lang = $this->loadLang($language);

        // Requeridos según nueva estructura
        $required = ['first_name', 'last_name', 'email', 'password', 'telephone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->resp(false, $lang["field_{$field}_required"] ?? "Field {$field} is required.", $data, 400);
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->resp(false, $lang['invalid_email_format'] ?? 'Invalid email format', $data, 400);
        }

        if (strlen((string) $data['password']) < 8) {
            return $this->resp(false, $lang['password_too_short'] ?? 'Password too short', $data, 400);
        }

        $data['password'] = password_hash((string) $data['password'], PASSWORD_BCRYPT);

        try {
            if ($this->userModel->getUserByEmail((string) $data['email'])) {
                return $this->resp(false, $lang['email_already_registered'] ?? 'Email already registered', $data, 409);
            }

            // registerUser maneja defaults y auditoría en tu modelo
            $this->authModel->registerUser($data);

            // Email de bienvenida (branding genérico del sistema)
            $this->sendWelcomeEmail((string) $data['email'], $language);

            return $this->resp(true, $lang['registration_successful'] ?? 'Registration successful', [
                'redirect' => '/login'
            ], 200);
        } catch (\Throwable $e) {
            return $this->resp(false, $lang['registration_failed'] ?? 'Registration failed', ['error' => $e->getMessage()], 500);
        }
    }

    private function sendWelcomeEmail(string $email, string $lang = 'EN'): void
    {
        $host = defined('APP_URL') ? APP_URL : 'http://localhost/';
        $logoUrl = rtrim($host, '/') . '/public/assets/images/logo-index.png';
        $appName = defined('APP_NAME') ? APP_NAME : 'AB System Essentials';

        $subject = $lang === 'ES' ? "Bienvenido a {$appName}" : "Welcome to {$appName}";

        if ($lang === 'ES') {
            $body = "
                <div style='font-family: sans-serif; color: black;'>
                    <div style='text-align: right;'>
                        <img src='{$logoUrl}' alt='{$appName}' style='height: 50px;' />
                    </div>
                    <p><strong>¡Bienvenido a {$appName}!</strong></p>
                    <p>Tu cuenta ha sido creada exitosamente.</p>
                    <p>Puedes acceder a tu panel aquí:<br>
                    <a href='{$host}'>{$host}</a></p>
                    <p>Si no creaste esta cuenta, puedes ignorar este mensaje.</p>
                </div>";
        } else {
            $body = "
                <div style='font-family: sans-serif; color: black;'>
                    <div style='text-align: right;'>
                        <img src='{$logoUrl}' alt='{$appName}' style='height: 50px;' />
                    </div>
                    <p><strong>Welcome to {$appName}!</strong></p>
                    <p>Your account has been successfully created.</p>
                    <p>Access your dashboard here:<br>
                    <a href='{$host}'>{$host}</a></p>
                    <p>If you did not create this account, you can safely ignore this email.</p>
                </div>";
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? 'localhost';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int) ($_ENV['MAIL_PORT'] ?? 587);

            $mail->setFrom($_ENV['MAIL_FROM'] ?? 'noreply@example.com', $_ENV['MAIL_FROM_NAME'] ?? $appName);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
        } catch (Exception $e) {
            error_log("Welcome email error: " . $mail->ErrorInfo);
        }
    }

    public function checkUserImage(?string $userId): array
    {
        if (!$userId) {
            return $this->resp(false, 'Missing user ID.', null, 400);
        }

        $relativePath = "uploads/users/user_{$userId}.jpg";

        try {
            $exists = $this->authModel->checkImageExists($relativePath);
            return $this->resp(true, 'Image check completed.', ['exists' => $exists], 200);
        } catch (\Throwable $e) {
            return $this->resp(false, 'Error checking image.', ['error' => $e->getMessage()], 500);
        }
    }

    public function logout(array $session): array
    {
        $userRole = $session['roles_user'] ?? 'User';
        $sessionId = $session['session_id'] ?? null;
        $status = $session['session_status'] ?? '';

        $baseUrl = match ($userRole) {
            default => ($_ENV['APP_URL'] ?? 'http://localhost/')
        };

        if ($sessionId && !in_array($status, ['expired', 'kicked'], true)) {
            $this->sessionModel->logoutSession($sessionId, null, 'closed');
        }

        return $this->resp(true, 'Logged out', ['redirect' => $baseUrl], 200);
    }
}
