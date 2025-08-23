<?php

require_once __DIR__ . '/../models/SessionManagementModel.php';

class SessionManagementController
{
    private $model;

    public function __construct()
    {
        $this->model = new SessionManagementModel();
    }

    private function getJsonInput(): array
    {
        return json_decode(file_get_contents("php://input"), true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');
        echo json_encode([
            'value' => $value,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    // Obtener todas las sesiones
    public function showAll()
    {
        try {
            $items = $this->model->getAll();
            $this->jsonResponse(true, '', $items);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
public function kick()
{
    $input = json_decode(file_get_contents('php://input'), true);
    $sessionId = $input['session_id'] ?? null;
    $inactivityDuration = isset($input['inactivity_duration']) ? (string)$input['inactivity_duration'] : null;
    $hasValidInactivity = is_numeric($inactivityDuration);
    $status = $input['status'] ?? ($hasValidInactivity ? 'expired' : 'kicked');

    if (!$sessionId) {
        return $this->jsonResponse(false, 'Missing session ID');
    }

    try {
        $this->model->logoutSession($sessionId, $inactivityDuration, $status);
        return $this->jsonResponse(true, 'Session terminated successfully');
    } catch (Exception $e) {
        return $this->jsonResponse(false, 'Error terminating session', ['error' => $e->getMessage()]);
    }
}


public function storeStatus()
{

    $input = json_decode(file_get_contents('php://input'), true);
    $status = $input['session_status'] ?? null;
    $inactivityDuration = isset($input['inactivity_duration']) ? (string)$input['inactivity_duration'] : null;

    if (!$status || !in_array($status, ['expired', 'kicked'], true)) {
        return $this->jsonResponse(false, 'Invalid or missing session status');
    }

    try {
        $_SESSION['session_status'] = $status;

        if ($inactivityDuration !== null && $inactivityDuration !== '') {
            $_SESSION['inactivity_duration'] = $inactivityDuration;
        }

        return $this->jsonResponse(true, 'Session status stored successfully');
    } catch (Exception $e) {
        return $this->jsonResponse(false, 'Error storing session status', ['error' => $e->getMessage()]);
    }
}




public function checkStatus()
{

    $sessionId = $_SESSION['session_id'] ?? null;

    if (!$sessionId) {
        return $this->jsonResponse(false, 'No active session');
    }

    $status = $this->model->getStatusBySessionId($sessionId);

    if (!$status) {
        return $this->jsonResponse(false, 'Session not found');
    }

    return $this->jsonResponse(true, 'Session status OK', ['status' => $status]);
}



    // Obtener una sesión por ID
    public function showById($params)
    {
        try {
            $item = $this->model->getById($params['id'] ?? '');
            $this->jsonResponse((bool) $item, $item ? '' : 'Not found', $item);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    // Registrar una nueva sesión (desde login)
    public function create()
    {
        try {
            $data = $this->getJsonInput();

            if (empty($data['user_id']) || empty($data['user_type'])) {
                return $this->jsonResponse(false, 'Missing user_id or user_type.');
            }

            $sessionId = $this->model->create($data['user_id'], $data['user_type']);
            $this->jsonResponse(true, 'Session audit created', ['session_id' => $sessionId]);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function export()
{
    try {
        $this->model->exportToCSV();
    } catch (Exception $e) {
        $this->jsonResponse(false, 'Error exporting: ' . $e->getMessage());
    }
}

}
