<?php

require_once __DIR__ . '/../models/SessionConfigModel.php';

class SessionConfigController
{
    private $model;

    public function __construct()
    {
        $this->model = new SessionConfigModel();
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

    public function show()
    {
        try {
            $config = $this->model->getConfig();
            $this->jsonResponse(true, '', $config);
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }

    public function update()
    {
        $data = $this->getJsonInput();
        $timeout = isset($data['timeout_minutes']) ? (int) $data['timeout_minutes'] : 0;

        if ($timeout <= 0) {
            return $this->jsonResponse(false, 'Invalid timeout value.');
        }

        try {
            $success = $this->model->updateTimeout($timeout);
            $this->jsonResponse($success, $success ? 'Session timeout updated.' : 'Update failed.');
        } catch (Exception $e) {
            $this->jsonResponse(false, $e->getMessage());
        }
    }
}
