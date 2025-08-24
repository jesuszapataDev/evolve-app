<?php

namespace App\Controllers;

use App\Services\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /* =====================
     *  JSON helpers
     * ===================== */
    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : []),
        ];

        echo json_encode($response);
        exit;
    }

    private function errorResponse(int $http_code, string $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }

    /* =====================
     *  USERS: Queries
     * ===================== */

    public function showByEmail($params)
    {
        $email = $params['email'] ?? $_POST['email'] ?? $_GET['email'] ?? null;
        $r = $this->userService->findByEmail($email);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function showByTelephone($params)
    {
        $telephone = $params['telephone'] ?? $_POST['telephone'] ?? $_GET['telephone'] ?? null;
        $r = $this->userService->findByTelephone($telephone);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    /**
     * Actualiza el tipo de sistema (métrico/imperial) del usuario en sesión.
     * Mantengo la verificación del método HTTP en el controlador.
     */
    public function update_system_type_session_user()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $lang = strtoupper($_SESSION['idioma'] ?? 'EN');

        if (!($method === 'PUT' || ($method === 'POST' && (($_POST['_method'] ?? '') === 'PUT')))) {
            $msg = $lang === 'ES' ? "Método no permitido. Se requiere PUT." : "Method not allowed. PUT required.";
            return $this->errorResponse(405, $msg);
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $msg = $lang === 'ES' ? "Usuario no autenticado." : "User not authenticated.";
            return $this->errorResponse(401, $msg);
        }

        // Obtener payload PUT
        if ($method === 'POST') {
            $putData = $_POST;
        } else {
            parse_str(file_get_contents("php://input"), $putData);
        }
        $systemType = $putData['system_type'] ?? null;

        $r = $this->userService->updateSystemTypeBySessionUser($userId, $systemType, $lang);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function showAll()
    {
        $r = $this->userService->listAll();
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function showById($params)
    {
        $id = $params['id'] ?? null;
        $r = $this->userService->getById($id);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Método no permitido. Se requiere POST.");
        }
        $data = $_POST;
        $r = $this->userService->create($data);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function updateStatus($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method Not Allowed. Use POST.");
        }

        $data = $_POST;
        $data['user_id'] = $params['id'] ?? $data['user_id'] ?? null;

        $r = $this->userService->updateStatus($data);
        // Nota: el servicio ya devuelve el mensaje apropiado.
        if ($r['value']) {
            return $this->jsonResponse(true, $r['message'], $r['data']);
        }
        // Cuando es fallo de validación, 400
        return $this->errorResponse(400, $r['message']);
    }

    public function update($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Método no permitido. Se requiere POST.");
        }
        $id = $params['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "ID de usuario requerido.");
        }

        $data = $_POST;
        $r = $this->userService->update($id, $data);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function update_profile($params)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!($method === 'PUT' || ($method === 'POST' && (($_POST['_method'] ?? '') === 'PUT')))) {
            return $this->errorResponse(405, "Método no permitido. Se requiere PUT.");
        }

        $id = $params['id'] ?? null;
        if ($method === 'POST') {
            $putData = $_POST;
        } else {
            parse_str(file_get_contents("php://input"), $putData);
        }

        $r = $this->userService->updateProfile((string) $id, $putData, $_FILES);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function delete($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return $this->errorResponse(405, "Method Not Allowed. DELETE required.");
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            return $this->errorResponse(400, "Missing ID parameter.");
        }
        $lang = strtoupper($_SESSION['idioma'] ?? 'EN');

        $r = $this->userService->delete((string) $id, $lang);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    public function countUsers()
    {
        $r = $this->userService->countUsers();
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }

    /**
     * Datos del usuario para la sesión (user-only).
     */
    public function getSessionUserData($params)
    {
        $userId = $params['id'] ?? null;
        $r = $this->userService->getSessionUserData($userId);
        return $this->jsonResponse($r['value'], $r['message'], $r['data']);
    }
}
