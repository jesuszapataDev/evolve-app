<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\SessionManagementModel;

class SessionManagementService
{
    private SessionManagementModel $model;

    public function __construct()
    {
        $this->model = new SessionManagementModel();
    }

    private function resp(bool $value, string $message = '', $data = null, int $status = 200): array
    {
        return ['value' => $value, 'message' => $message, 'data' => $data, 'status' => $status];
    }

    /* ===== Listar todas ===== */
    public function listAll(): array
    {
        try {
            $items = $this->model->getAll();
            return $this->resp(true, '', $items);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    /* ===== Finalizar/kick sesión ===== */
    public function kick(?string $sessionId, ?string $inactivityDuration = null, ?string $status = null): array
    {
        if (!$sessionId) {
            return $this->resp(false, 'Missing session ID', null, 400);
        }

        $hasValidInactivity = is_numeric($inactivityDuration);
        $finalStatus = $status ?? ($hasValidInactivity ? 'expired' : 'kicked');

        try {
            $this->model->logoutSession($sessionId, $inactivityDuration, $finalStatus);
            return $this->resp(true, 'Session terminated successfully');
        } catch (\Throwable $e) {
            return $this->resp(false, 'Error terminating session', ['error' => $e->getMessage()], 500);
        }
    }

    /* ===== Guardar estado en sesión (server-side) ===== */
    public function storeStatus(?string $status, ?string $inactivityDuration): array
    {
        if (!$status || !in_array($status, ['expired', 'kicked'], true)) {
            return $this->resp(false, 'Invalid or missing session status', null, 400);
        }

        try {
            $_SESSION['session_status'] = $status;
            if ($inactivityDuration !== null && $inactivityDuration !== '') {
                $_SESSION['inactivity_duration'] = (string)$inactivityDuration;
            }
            return $this->resp(true, 'Session status stored successfully');
        } catch (\Throwable $e) {
            return $this->resp(false, 'Error storing session status', ['error' => $e->getMessage()], 500);
        }
    }

    /* ===== Verificar estado actual ===== */
    public function checkStatus(?string $sessionId): array
    {
        if (!$sessionId) {
            return $this->resp(false, 'No active session', null, 400);
        }

        try {
            $status = $this->model->getStatusBySessionId($sessionId);
            if (!$status) {
                return $this->resp(false, 'Session not found', null, 404);
            }
            return $this->resp(true, 'Session status OK', ['status' => $status]);
        } catch (\Throwable $e) {
            return $this->resp(false, 'Error checking session', ['error' => $e->getMessage()], 500);
        }
    }

    /* ===== Obtener por ID ===== */
    public function getById(?string $id): array
    {
        try {
            $item = $this->model->getById($id ?? '');
            return $this->resp((bool)$item, $item ? '' : 'Not found', $item, $item ? 200 : 404);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    /* ===== Crear registro de sesión (auditoría) ===== */
    public function create(array $data): array
    {
        if (empty($data['user_id']) || empty($data['user_type'])) {
            return $this->resp(false, 'Missing user_id or user_type.', $data, 400);
        }

        // Campos opcionales si el modelo los soporta
        $userId       = (string)$data['user_id'];
        $userType     = (string)$data['user_type'];
        $deviceId     = $data['device_id']     ?? null;
        $deviceType   = $data['device_type']   ?? null;
        $success      = $data['success']       ?? null;
        $failureReason= $data['reason']        ?? null;

        try {
            // Llamado amplio (si tu modelo acepta los extras, los usará; si no, ignora nulls)
            $sessionId = $this->model->create($userId, $userType, $deviceId, $deviceType, $success, $failureReason);
            return $this->resp(true, 'Session audit created', ['session_id' => $sessionId], 201);
        } catch (\ArgumentCountError $e) {
            // Fallback a firma corta (compatibilidad)
            try {
                $sessionId = $this->model->create($userId, $userType);
                return $this->resp(true, 'Session audit created', ['session_id' => $sessionId], 201);
            } catch (\Throwable $t) {
                return $this->resp(false, $t->getMessage(), null, 500);
            }
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    /* ===== Exportar a CSV ===== */
    public function export(): array
    {
        try {
            // Si el modelo hace el stream/descarga, no devolvemos JSON aquí.
            $this->model->exportToCSV();
            return $this->resp(true, 'Export started'); // Por si el modelo no hace exit;
        } catch (\Throwable $e) {
            return $this->resp(false, 'Error exporting: ' . $e->getMessage(), null, 500);
        }
    }
}
