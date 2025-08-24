<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\SessionConfigModel;

class SessionConfigService
{
    private SessionConfigModel $model;

    public function __construct()
    {
        $this->model = new SessionConfigModel();
    }

    private function resp(bool $value, string $message = '', $data = null, int $status = 200): array
    {
        return ['value' => $value, 'message' => $message, 'data' => $data, 'status' => $status];
    }

    public function getConfig(): array
    {
        try {
            $config = $this->model->getConfig();
            return $this->resp(true, '', $config);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }

    public function update(array $data): array
    {
        $timeout = isset($data['timeout_minutes']) ? (int)$data['timeout_minutes'] : 0;

        if ($timeout <= 0) {
            return $this->resp(false, 'Invalid timeout value.', null, 400);
        }

        try {
            $success = $this->model->updateTimeout($timeout);
            return $this->resp((bool)$success, $success ? 'Session timeout updated.' : 'Update failed.', null, $success ? 200 : 400);
        } catch (\Throwable $e) {
            return $this->resp(false, $e->getMessage(), null, 500);
        }
    }
}
