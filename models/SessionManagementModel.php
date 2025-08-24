<?php

namespace App\Models;

use App\Config\ClientEnvironmentInfo;
use App\Config\Database;
use App\Config\TimezoneManager;

require_once __DIR__ . '/../helpers/session_timezone_helper.php';

class SessionManagementModel
{
    private $db;
    private $table = 'session_management';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY login_time DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($sessionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE session_id = ?");
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function countFailedAttemptsByIp(string $ipAddress, string $userType, int $withinMinutes = 1): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS failed_attempts
            FROM {$this->table}
            WHERE ip_address = ?
              AND user_type = ?
              AND login_success = 0
              AND created_at > (NOW() - INTERVAL ? MINUTE)
        ");
        if (!$stmt) {
            throw new \Exception("Failed to prepare IP attempt query: " . $this->db->error);
        }

        $stmt->bind_param("ssi", $ipAddress, $userType, $withinMinutes);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int) ($result['failed_attempts'] ?? 0);
    }

    /**
     * Crea un registro de sesión.
     * @param string|null $userId
     * @param string $userType 'administrator' | 'user'
     * @param string|null $deviceId
     * @param string|null $deviceType  // reemplaza is_mobile
     * @param bool $loginSuccess
     * @param string|null $failureReason
     * @return string session_id
     * @throws \mysqli_sql_exception
     */
    public function create($userId, string $userType, $deviceId, $deviceType, $loginSuccess = true, $failureReason = null): string
    {
        $this->db->begin_transaction();
        try {
            $sessionId = $this->generateUUIDv4();

            // Inicializar entorno de auditoría y zona horaria
            $env = new ClientEnvironmentInfo(APP_ROOT . '/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            // Fecha/hora ajustada a la TZ del usuario
            $geo = $env->getGeoInfo();
            $createdAt = getNowInUserLocalTime(
                $geo['client_country'] ?? '',
                $geo['client_region'] ?? '',
                $geo['client_city'] ?? ''
            );
            $loginTime = $createdAt;

            $logoutTime = null;
            $inactivityDuration = null;

            // Info del cliente
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
            $hostname = $env->getClientHostname();
            $os = $env->getClientOs();
            $browser = $env->getClientBrowser();

            // Normalizar userType
            $userType = strtolower((string) $userType);
            $fullName = 'UNKNOWN';
            $username = 'UNKNOWN';

            // Obtener nombre/username según tipo (solo administrator / user)
            if ($userType === 'administrator') {
                $stmt = $this->db->prepare("
                    SELECT CONCAT(first_name, ' ', last_name) AS full_name, email AS username
                    FROM administrators
                    WHERE administrator_id = ?
                ");
            } elseif ($userType === 'user') {
                $stmt = $this->db->prepare("
                    SELECT CONCAT(first_name, ' ', last_name) AS full_name, email AS username
                    FROM users
                    WHERE user_id = ?
                ");
            } else {
                $stmt = null; // Tipo no reconocido, dejar UNKNOWN
            }

            if ($stmt) {
                $stmt->bind_param("s", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $fullName = $row['full_name'] ?? $fullName;
                    $username = $row['username'] ?? $username;
                }
                $stmt->close();
            }

            // Estado de la sesión
            $sessionStatus = $loginSuccess ? 'active' : 'failed';

            // Insertar registro (nota: device_type en lugar de is_mobile)
            $stmtInsert = $this->db->prepare("INSERT INTO {$this->table} (
                session_id, user_id, user_name, user_type, full_name,
                login_time, logout_time, inactivity_duration,
                login_success, failure_reason, session_status,
                ip_address, city, region, country, zipcode, coordinates,
                hostname, os, browser, user_agent,
                device_id, device_type, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmtInsert) {
                throw new mysqli_sql_exception("Error preparando la inserción: " . $this->db->error);
            }

            $stmtInsert->bind_param(
                'ssssssssisssssssssssssss',
                $sessionId,
                $userId,
                $username,
                $userType,
                $fullName,
                $loginTime,
                $logoutTime,
                $inactivityDuration,
                $loginSuccess,
                $failureReason,
                $sessionStatus,
                $ip,
                $geo['client_city'],
                $geo['client_region'],
                $geo['client_country'],
                $geo['client_zipcode'],
                $geo['client_coordinates'],
                $hostname,
                $os,
                $browser,
                $userAgent,
                $deviceId,
                $deviceType,
                $createdAt
            );

            $stmtInsert->execute();
            $stmtInsert->close();

            $this->db->commit();
            return $sessionId;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function logoutSession(string $sessionId, ?string $inactivityDuration = null, string $status = ''): bool
    {
        try {
            // 1) Obtener user_id
            $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE session_id = ?");
            $stmt->bind_param("s", $sessionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if (!$row || empty($row['user_id'])) {
                throw new Exception("No se pudo encontrar el user_id de la sesión.");
            }

            $userId = $row['user_id'];

            // 2) Inicializar entorno
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $geo = $env->getGeoInfo();
            $logoutTime = getNowInUserLocalTime(
                $geo['client_country'] ?? '',
                $geo['client_region'] ?? '',
                $geo['client_city'] ?? ''
            );

            // 3) Validar estado
            if (empty($status)) {
                $status = $inactivityDuration !== null && $inactivityDuration !== '' ? 'expired' : 'closed';
            } elseif (!in_array($status, ['expired', 'closed', 'kicked'], true)) {
                throw new Exception("Estado de sesión no válido: $status");
            }

            // 4) Si status=expired y no hay duración, obtener de config
            if ($status === 'expired' && ($inactivityDuration === null || $inactivityDuration === '')) {
                require_once $_SERVER['DOCUMENT_ROOT'] . '/app/models/SessionConfigModel.php';
                $configModel = new SessionConfigModel();
                $config = $configModel->getConfig();
                $timeoutMinutes = (int) ($config['timeout_minutes'] ?? 5);
                $inactivityDuration = (string) ($timeoutMinutes * 60);
            }

            // 5) UPDATE
            $query = "UPDATE {$this->table}
                      SET logout_time = ?, session_status = ?, inactivity_duration = ?
                      WHERE session_id = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando logoutSession: " . $this->db->error);
            }

            $stmt->bind_param('ssss', $logoutTime, $status, $inactivityDuration, $sessionId);
            $stmt->execute();
            $stmt->close();

            return true;
        } catch (Exception | mysqli_sql_exception $e) {
            return false;
        }
    }

    public function getStatusBySessionId(string $sessionId): ?string
    {
        $stmt = $this->db->prepare("SELECT session_status FROM {$this->table} WHERE session_id = ?");
        $stmt->bind_param('s', $sessionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['session_status'] ?? null;
    }

    public function exportToCSV(): void
    {
        $filename = 'session_audit_export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Session ID',
            'User ID',
            'User Type',
            'Full Name',
            'Login Time',
            'IP Address',
            'User Agent',
            'Created At'
        ]);

        $sql = "SELECT session_id, user_id, user_type, full_name, login_time, ip_address, user_agent, created_at
                FROM {$this->table}
                ORDER BY login_time DESC";
        $result = $this->db->query($sql);

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['session_id'],
                $row['user_id'],
                ucfirst($row['user_type']),
                $row['full_name'],
                $row['login_time'],
                $row['ip_address'],
                $row['user_agent'],
                $row['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    private function generateUUIDv4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
