<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\CountryModel;

class CountryService
{
    private CountryModel $model;

    public function __construct()
    {
        $this->model = new CountryModel();
    }

    private function resp(bool $value, string $message = '', $data = null, int $status = 200): array
    {
        return ['value' => $value, 'message' => $message, 'data' => $data, 'status' => $status];
    }

    public function listAll(): array
    {
        try {
            $items = $this->model->getAll();
            return $this->resp(true, '', $items);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    public function getById($id): array
    {
        try {
            $item = $this->model->getById($id ?? 0);
            return $this->resp((bool)$item, $item ? '' : 'Not found', $item, $item ? 200 : 404);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    public function create(array $data): array
    {
        if (empty($data)) {
            return $this->resp(false, 'Payload is empty', null, 400);
        }
        try {
            $ok = $this->model->create($data);
            return $this->resp((bool)$ok, $ok ? 'Created successfully' : 'Error creating', null, $ok ? 201 : 400);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    public function update($id, array $data): array
    {
        if (!$id) {
            return $this->resp(false, 'Missing ID', null, 400);
        }
        if (empty($data)) {
            return $this->resp(false, 'Payload is empty', null, 400);
        }
        try {
            $ok = $this->model->update($id, $data);
            return $this->resp((bool)$ok, $ok ? 'Updated successfully' : 'Error updating', null, $ok ? 200 : 400);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    public function delete($id): array
    {
        if (!$id) {
            return $this->resp(false, 'Missing ID', null, 400);
        }
        try {
            $ok = $this->model->delete($id);
            return $this->resp((bool)$ok, $ok ? 'Deleted successfully' : 'Error deleting', null, $ok ? 200 : 400);
        } catch (\mysqli_sql_exception $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        } catch (\Throwable $e) {
            return $this->resp(false, 'Unexpected error: ' . $e->getMessage(), null, 500);
        }
    }

    public function export(): array
    {
        try {
            // El modelo puede hacer streaming del CSV y finalizar el script.
            $this->model->exportAllCountriesToCSV();
            return $this->resp(true, 'Export started');
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }
}
