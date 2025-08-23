<?php

require_once __DIR__ . '/../models/AuditLogModel.php';

class AuditLogController
{
    private $auditLogModel;

    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ];

        echo json_encode($response);
        exit;
    }

    public function getAll()
    {
        try {
            $logs = $this->auditLogModel->getAll();
            return $this->jsonResponse(true, '', $logs);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error al listar los registros de auditoría: " . $e->getMessage());
        }
    }

    public function getById($params)
    {
        $id = $params['id'] ?? null;
        try {
            $log = $this->auditLogModel->getById($id);
            if ($log) {
                return $this->jsonResponse(true, '', $log);
            } else {
                return $this->jsonResponse(false, "Registro de auditoría no encontrado");
            }
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error al obtener el registro de auditoría: " . $e->getMessage());
        }
    }

    public function exportCSV()
    {
        try {
            $this->auditLogModel->exportAllToCSV();
        } catch (Exception $e) {
            $this->errorResponse(500, "Error al exportar auditoría: " . $e->getMessage());
        }
    }

    protected function view($vista, $data = [])
    {
        $rutaVista = __DIR__ . '/../Views/' . $vista . '.php';
        if (file_exists($rutaVista)) {
            extract($data);
            include $rutaVista;
        } else {
            $this->errorResponse(500, "Error interno del servidor: Vista no encontrada.");
        }
    }

    private function errorResponse($http_code, $message)
    {
        http_response_code($http_code);
        echo json_encode([
            'value' => false,
            'message' => $message
        ]);
        exit;
    }
}
