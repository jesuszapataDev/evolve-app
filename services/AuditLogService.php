<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLogModel;

class AuditLogService
{
    private AuditLogModel $model;

    public function __construct()
    {
        $this->model = new AuditLogModel();
    }

    private function resp(bool $value, string $message = '', $data = null, int $status = 200): array
    {
        return ['value' => $value, 'message' => $message, 'data' => $data, 'status' => $status];
    }

    public function listAll(): array
    {
        try {
            $logs = $this->model->getAll();
            return $this->resp(true, '', $logs);
        } catch (\Throwable $e) {
            return $this->resp(false, "Error al listar los registros de auditoría: " . $e->getMessage(), null, 500);
        }
    }

    public function getById(?string $id): array
    {
        try {
            $log = $this->model->getById($id);
            if ($log) {
                return $this->resp(true, '', $log);
            }
            return $this->resp(false, "Registro de auditoría no encontrado", null, 404);
        } catch (\Throwable $e) {
            return $this->resp(false, "Error al obtener el registro de auditoría: " . $e->getMessage(), null, 500);
        }
    }

    public function exportCSV(): array
    {
        try {
            // Si el modelo hace el streaming del CSV y termina el script, no se ejecutará lo siguiente.
            $this->model->exportAllToCSV();
            return $this->resp(true, 'Export started');
        } catch (\Throwable $e) {
            return $this->resp(false, "Error al exportar auditoría: " . $e->getMessage(), null, 500);
        }
    }
}
