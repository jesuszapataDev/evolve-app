<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\CountryService;

class CountryController
{
    private CountryService $service;

    public function __construct()
    {
        $this->service = new CountryService();
    }

    private function getJsonInput(): array
    {
        return json_decode(file_get_contents("php://input"), true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'value'   => $value,
            'message' => $message,
            'data'    => $data
        ]);
        exit;
    }

    public function showAll()
    {
        $r = $this->service->listAll();
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function showById($params)
    {
        $r = $this->service->getById($params['id'] ?? 0);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function create()
    {
        $data = $this->getJsonInput();
        $r    = $this->service->create($data);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function update($params)
    {
        $data = $this->getJsonInput();
        $r    = $this->service->update($params['id'] ?? 0, $data);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function delete($params)
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'DELETE') {
            return $this->jsonResponse(false, 'Method Not Allowed', null, 405);
        }
        $r = $this->service->delete($params['id'] ?? 0);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function export($userId = null)
    {
        $r = $this->service->export();
        // Si el modelo hizo streaming y finalizó, no se ejecuta lo siguiente.
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }
}
