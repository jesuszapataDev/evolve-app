<?php

namespace App\Controllers;

use App\Models\CountryModel;
use Exception;
use mysqli_sql_exception;

class CountryController
{
    private $model;

    public function __construct()
    {
        $this->model = new CountryModel();
    }

    private function getJsonInput(): array
    {
        return json_decode(file_get_contents("php://input"), true) ?? [];
    }

    private function errorResponse(int $http_code, string $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
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

    public function showAll()
    {
        $items = $this->model->getAll();
        $this->jsonResponse(true, '', $items);
    }

    public function showById($params)
    {
        $item = $this->model->getById($params['id'] ?? 0);
        $this->jsonResponse((bool) $item, $item ? '' : 'Not found', $item);
    }

    public function create()
    {
        $data = $this->getJsonInput();
        $result = $this->model->create($data);
        $this->jsonResponse($result, $result ? 'Created successfully' : 'Error creating');
    }

    public function update($params)
    {
        $data = $this->getJsonInput();
        $result = $this->model->update($params['id'] ?? 0, $data);
        $this->jsonResponse($result, $result ? 'Updated successfully' : 'Error updating');
    }

    public function delete($params)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $id = $params['id'] ?? 0;

            try {
                $result = $this->model->delete($id);
                return $this->jsonResponse(true, 'Deleted successfully');
            } catch (mysqli_sql_exception $e) {
                return $this->jsonResponse(false, $e->getMessage());
            } catch (Exception $e) {
                return $this->jsonResponse(false, 'Unexpected error: ' . $e->getMessage());
            }
        } else {
            return $this->errorResponse(405, 'Method Not Allowed');
        }
    }


    public function export($userId)
    {
        try {
            $this->model->exportAllCountriesToCSV();
        } catch (Exception $e) {
            echo json_encode(['value' => false, 'message' => $e->getMessage()]);
        }
    }
}
