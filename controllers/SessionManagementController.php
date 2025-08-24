<?php
namespace App\Controllers;

use App\Services\SessionManagementService;

class SessionManagementController
{
    private SessionManagementService $service;

    public function __construct()
    {
        $this->service = new SessionManagementService();
    }

    private function getJsonInput(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode(['value' => $value, 'message' => $message, 'data' => $data]);
        exit;
    }

    /* ===== Delegaciones al servicio ===== */

    // Obtener todas las sesiones
    public function showAll()
    {
        $r = $this->service->listAll();
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function kick()
    {
        $input = $this->getJsonInput();
        $r = $this->service->kick(
            $input['session_id'] ?? null,
            isset($input['inactivity_duration']) ? (string)$input['inactivity_duration'] : null,
            $input['status'] ?? null
        );
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function storeStatus()
    {
        $input = $this->getJsonInput();
        $r = $this->service->storeStatus(
            $input['session_status'] ?? null,
            isset($input['inactivity_duration']) ? (string)$input['inactivity_duration'] : null
        );
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function checkStatus()
    {
        $sessionId = $_SESSION['session_id'] ?? null;
        $r = $this->service->checkStatus($sessionId);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    // Obtener una sesión por ID
    public function showById($params)
    {
        $r = $this->service->getById($params['id'] ?? null);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    // Registrar una nueva sesión (desde login)
    public function create()
    {
        $data = $this->getJsonInput();
        $r = $this->service->create($data);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function export()
    {
        $r = $this->service->export();
        // Si el modelo ya envió headers/archivo y salió, no llegaremos aquí.
        // En caso contrario, devolvemos JSON.
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }
}
