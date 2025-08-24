<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuditLogService;

class AuditLogController
{
    private AuditLogService $service;

    public function __construct()
    {
        $this->service = new AuditLogService();
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ]);
        exit;
    }

    public function getAll()
    {
        $r = $this->service->listAll();
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function getById($params)
    {
        $r = $this->service->getById($params['id'] ?? null);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function exportCSV()
    {
        $r = $this->service->exportCSV();
        // Si el modelo ya envió el archivo (stream + exit), no llegaremos aquí.
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    /* (Opcional) Si aún usas vistas en otra ruta, puedes mantener este helper: */
    protected function view($vista, $data = [])
    {
        $rutaVista = APP_ROOT . '/Views/' . $vista . '.php';
        if (is_file($rutaVista)) {
            extract($data);
            include $rutaVista;
        } else {
            $this->jsonResponse(false, "Error interno del servidor: Vista no encontrada.", null, 500);
        }
    }
}
