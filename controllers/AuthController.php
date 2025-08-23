<?php

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/SecurityQuestionsModel.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/src/Exception.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/phpmailer/phpmailer/src/SMTP.php';

class AuthController
{
    private $authModel;
    private $userModel;
    private $securityQuestionsModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->userModel = new UserModel();
        $this->securityQuestionsModel = new SecurityQuestionsModel();
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse($value, $message = '', $data = null, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => $data
        ]);
        exit;
    }

    public function login()
    {
        $input    = $this->getJsonInput();

        $email    = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $language = strtoupper($input['language'] ?? 'EN');

        // Auditoría
        $deviceId    = $input['device_id']   ?? null;
        $deviceType  = $input['device_type'] ?? null; // <-- reemplaza is_mobile
        $userAgent   = $input['user_agent']  ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
        $ipAddress   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userType    = 'user';

        // Cargar idioma
        $langFile = $_SERVER['DOCUMENT_ROOT'] . "/lang/{$language}.php";
        if (!file_exists($langFile)) {
            $langFile = $_SERVER['DOCUMENT_ROOT'] . "/lang/EN.php";
        }
        $lang = require $langFile;

        // Helpers y modelos
        require_once __DIR__ . '/../helpers/login_helpers.php';
        require_once __DIR__ . '/../models/SessionManagementModel.php';
        $SessionManagementModel = new SessionManagementModel();

        $failureCode = null;
        $usuario     = null;

        // ──────── BLOQUEO POR SESIÓN ────────
        $maxAttempts = 3;
        $lockoutTime = 60; // segundos
        $attemptKey  = 'login_attempts_' . md5($email);
        $now         = time();
        $attemptData = $_SESSION[$attemptKey] ?? ['count' => 0, 'last_attempt' => 0, 'locked_until' => 0];

        if ($attemptData['locked_until'] > $now) {
            $wait    = ceil(($attemptData['locked_until'] - $now) / 60);
            $details = getFailureDetails('too_many_attempts');

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try { $usuario = $this->userModel->getUserByEmail($email); } catch (\Throwable $t) {}
            }

            // create(user_id, userType, deviceId, deviceType, success, reason)
            $SessionManagementModel->create(
                $usuario['user_id'] ?? null,
                $userType,
                $deviceId,
                $deviceType,
                false,
                $details['reason']
            );

            return $this->jsonResponse(false, $lang['failure_' . $details['code']] ?? $details['reason'], [
                'code' => $details['code'],
                'wait_minutes' => $wait
            ], 429);
        }

        // ──────── VALIDACIONES INICIALES ────────
        if (empty($email) || empty($password)) {
            $details = getFailureDetails('missing_fields');
            $SessionManagementModel->create(null, $userType, $deviceId, $deviceType, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $details = getFailureDetails('invalid_email_format');
            $SessionManagementModel->create(null, $userType, $deviceId, $deviceType, false, $details['reason']);

            $attemptData['count']++;
            $attemptData['last_attempt'] = $now;
            if ($attemptData['count'] >= $maxAttempts) {
                $attemptData['locked_until'] = $now + $lockoutTime;
            }
            $_SESSION[$attemptKey] = $attemptData;

            return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 400);
        }

        // ──────── AUTENTICACIÓN ────────
        try {
            $usuario = $this->userModel->getUserByEmail($email);

            if (!$usuario) {
                $failureCode = 'user_not_found';
            } elseif ((int)$usuario['status'] === 0) {
                $failureCode = 'user_blocked';
            } elseif (!password_verify($password, $usuario['password'])) {
                $failureCode = 'invalid_password';
            }

            if ($failureCode) {
                $details = getFailureDetails($failureCode);

                $SessionManagementModel->create(
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

                    $bloqueado = $this->userModel->updateStatus([
                        'user_id' => $usuario['user_id'],
                        'status'  => 0
                    ]);

                    if (!$bloqueado) {
                        error_log("❌ Falló el bloqueo del usuario con ID: " . $usuario['user_id']);
                    } else {
                        error_log("✅ Usuario bloqueado correctamente: " . $usuario['user_id']);
                    }

                    $failureCode = 'user_blocked';
                    $details = getFailureDetails($failureCode);
                }

                if ($attemptData['count'] >= $maxAttempts) {
                    $attemptData['locked_until'] = $now + $lockoutTime;
                }
                $_SESSION[$attemptKey] = $attemptData;

                return $this->jsonResponse(false, $lang['failure_' . $details['code']], ['code' => $details['code']], 401);
            }

            // ✅ LOGIN EXITOSO
            $_SESSION['idioma']     = $language;
            $_SESSION['user_id']    = $usuario['user_id'];

            // Normaliza rol a 'User' | 'Administrator'
            $normalizedRole         = ucfirst(strtolower($usuario['rol'] ?? 'User'));
            $_SESSION['roles_user'] = in_array($normalizedRole, ['User','Administrator'], true) ? $normalizedRole : 'User';

            $_SESSION['sex']        = $usuario['sex'] ?? null;
            $_SESSION['timezone']   = $usuario['timezone'] ?? null;
            $_SESSION['user_name']  = trim(($usuario['first_name'] ?? '') . ' ' . ($usuario['last_name'] ?? ''));
            $_SESSION['logged_in']  = true;
            $_SESSION['user_type']  = $userType;

            unset($_SESSION[$attemptKey]);

            $sessionId = $SessionManagementModel->create(
                $usuario['user_id'],
                $userType,
                $deviceId,
                $deviceType,
                true,
                null
            );

            $_SESSION['session_id'] = $sessionId;

            return $this->jsonResponse(true, $lang['login_success_yes'], ['redirect' => '/dashboard']);

        } catch (Exception $e) {
            error_log("⚠️ Excepción en login: " . $e->getMessage());

            $SessionManagementModel->create(
                $usuario['user_id'] ?? null,
                $userType,
                $deviceId,
                $deviceType,
                false,
                'Excepción inesperada: ' . $e->getMessage()
            );
            return $this->jsonResponse(false, $lang['login_success_no'], [
                'code'  => 'unknown_failure',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function registrar()
    {
        $data     = $this->getJsonInput();

        // Campos requeridos según nueva estructura (sin birthday; con timezone)
        $required = ['first_name', 'last_name', 'sex', 'email', 'password', 'telephone', 'timezone'];

        $language = strtoupper($data['language'] ?? 'EN');
        $langFile = $_SERVER['DOCUMENT_ROOT'] . "/lang/{$language}.php";
        if (!file_exists($langFile)) {
            $langFile = $_SERVER['DOCUMENT_ROOT'] . "/lang/EN.php";
        }
        $lang = require $langFile;

        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->jsonResponse(false, $lang["field_{$field}_required"] ?? "Field {$field} is required.", $data, 400);
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse(false, $lang['invalid_email_format'], $data, 400);
        }

        if (strlen($data['password']) < 8) {
            return $this->jsonResponse(false, $lang['password_too_short'], $data, 400);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        try {
            if ($this->userModel->getUserByEmail($data['email'])) {
                return $this->jsonResponse(false, $lang['email_already_registered'], $data, 409);
            }

            // `registerUser` ya maneja status/rol por defecto y created_at/created_by
            $this->authModel->registerUser($data);

            // Enviar correo de bienvenida
            $this->sendWelcomeEmail($data['email'], $language);

            return $this->jsonResponse(true, $lang['registration_successful'], [
                'redirect' => '/login'
            ]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $lang['registration_failed'], ['error' => $e->getMessage()], 500);
        }
    }

    private function sendWelcomeEmail($email, $lang = 'EN')
    {
        $host    = defined('APP_URL') ? APP_URL : 'http://localhost/';
        $logoUrl = rtrim($host, '/') . '/public/assets/images/logo-index.png';
        $subject = $lang === 'ES' ? 'Bienvenido a VITAKEE' : 'Welcome to VITAKEE';

        if ($lang === 'ES') {
            $body = "
                <div style='font-family: sans-serif; color: black;'>
                    <div style='text-align: right;'>
                        <img src='$logoUrl' alt='VITAKEE' style='height: 50px;' />
                    </div>
                    <p><strong>Bienvenido a VITAKEE,</strong></p>
                    <p>Tu cuenta ha sido creada exitosamente.</p>
                    <p>Ahora puedes comenzar a registrar y organizar tus datos de salud personal en un solo lugar, de forma segura.</p>
                    <p><strong>Aquí tienes lo que puedes hacer a continuación:</strong></p>
                    <ul>
                        <li>Iniciar sesión en tu panel para comenzar a registrar tus biomarcadores</li>
                        <li>Configurar alertas personalizadas para valores fuera de rango</li>
                        <li>Exportar reportes y hacer seguimiento de tus tendencias a lo largo del tiempo</li>
                    </ul>
                    <p>Accede a tu cuenta aquí:<br>
                    <a href='$host'>$host</a></p>
                    <p>Si no creaste esta cuenta, puedes ignorar este mensaje.</p>
                    <hr>
                    <p style='font-size: 12px;'>
                        <strong>VITAKEE</strong><br>
                        Tu salud, tus datos — siempre accesibles.<br>
                        <a href='$host'>$host</a><br>
                        1065 Aurora Ln, Corona, CA 92881
                    </p>
                </div>
            ";
        } else {
            $body = "
                <div style='font-family: sans-serif; color: black;'>
                    <div style='text-align: right;'>
                        <img src='$logoUrl' alt='VITAKEE' style='height: 50px;' />
                    </div>
                    <p><strong>Welcome to VITAKEE,</strong></p>
                    <p>Your account has been successfully created.</p>
                    <p>You can now start logging and organizing your personal health data securely in one place.</p>
                    <p><strong>Here’s what you can do next:</strong></p>
                    <ul>
                        <li>Log in to your dashboard to start recording your biomarkers</li>
                        <li>Set up custom alerts for out-of-range values</li>
                        <li>Export reports and track trends over time</li>
                    </ul>
                    <p>Access your account here:<br>
                    <a href='$host'>$host</a></p>
                    <p>If you did not create this account, you can safely ignore this message.</p>
                    <hr>
                    <p style='font-size: 12px;'>
                        <strong>VITAKEE</strong><br>
                        Your health, your data — always accessible.<br>
                        <a href='$host'>$host</a><br>
                        1065 Aurora Ln, Corona, CA 92881
                    </p>
                </div>
            ";
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'] ?? 'localhost';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password   = $_ENV['MAIL_PASSWORD'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $_ENV['MAIL_PORT'] ?? 587;

            $mail->setFrom($_ENV['MAIL_FROM'] ?? 'noreply@example.com', $_ENV['MAIL_FROM_NAME'] ?? 'Vitakee');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
        } catch (Exception $e) {
            error_log("Welcome email error: " . $mail->ErrorInfo);
        }
    }

    public function checkUserImage()
    {
        $input  = $this->getJsonInput();
        $userId = $input['user_id'] ?? null;

        if (!$userId) {
            return $this->jsonResponse(false, 'Missing user ID.', null, 400);
        }

        $relativePath = "uploads/users/user_{$userId}.jpg";

        try {
            $exists = $this->authModel->checkImageExists($relativePath);
            return $this->jsonResponse(true, 'Image check completed.', ['exists' => $exists]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error checking image.', ['error' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        $userRole  = $_SESSION['roles_user'] ?? 'User';
        $sessionId = $_SESSION['session_id'] ?? null;
        $inactivity = $_SESSION['inactivity_duration'] ?? null;
        $status     = $_SESSION['session_status'] ?? '';

        // Solo User / Administrator
        $baseUrl = match ($userRole) {
            default         => ($_ENV['APP_URL'] ?? 'http://localhost/')
        };

        if ($sessionId && !in_array($status, ['expired', 'kicked'], true)) {
            require_once __DIR__ . '/../models/SessionManagementModel.php';
            $sessionModel = new SessionManagementModel();
            $sessionModel->logoutSession($sessionId, null, 'closed');
        }

        session_unset();
        session_destroy();

        header("Location: " . $baseUrl);
        exit;
    }

    // Ejemplo de guardado de preguntas de seguridad (comentado)
    /*
    public function saveSecurityQuestions()
    {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['user_id'])) {
            return $this->jsonResponse(false, 'Unauthorized. Please log in first.', null, 401);
        }

        $input    = $this->getJsonInput();
        $userId   = $_SESSION['user_id'];
        $question1 = $input['question1'] ?? '';
        $answer1   = $input['answer1'] ?? '';
        $question2 = $input['question2'] ?? '';
        $answer2   = $input['answer2'] ?? '';

        if (empty($question1) || empty($answer1) || empty($question2) || empty($answer2)) {
            return $this->jsonResponse(false, 'All security questions and answers are required.', $input, 400);
        }

        $hashed_answer1 = password_hash($answer1, PASSWORD_BCRYPT);
        $hashed_answer2 = password_hash($answer2, PASSWORD_BCRYPT);

        $dataToSave = [
            'user_id'   => $userId,
            'question1' => $question1,
            'answer1'   => $hashed_answer1,
            'question2' => $question2,
            'answer2'   => $hashed_answer2
        ];

        try {
            $result = $this->securityQuestionsModel->create($dataToSave);

            if ($result['value'] === true) {
                return $this->jsonResponse(true, $result['message'], ['redirect' => '/dashboard']);
            } else {
                return $this->jsonResponse(false, $result['message'], null, 500);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error saving security questions: ' . $e->getMessage(), null, 500);
        }
    }
    */
}
