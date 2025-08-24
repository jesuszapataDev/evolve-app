<?php
namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse($value, $message = '', $data = null, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode(['value' => $value, 'message' => $message, 'data' => $data]);
        exit;
    }

    /* ======== Nuevas delegaciones al servicio ======== */

    public function login()
    {
        $input  = $this->getJsonInput();
        $result = $this->authService->login($input, $_SERVER);
        return $this->jsonResponse($result['value'], $result['message'], $result['data'], $result['status']);
    }

    public function registrar()
    {
        $data   = $this->getJsonInput();
        $result = $this->authService->registrar($data);
        return $this->jsonResponse($result['value'], $result['message'], $result['data'], $result['status']);
    }

    public function checkUserImage()
    {
        $input  = $this->getJsonInput();
        $result = $this->authService->checkUserImage($input['user_id'] ?? null);
        return $this->jsonResponse($result['value'], $result['message'], $result['data'], $result['status']);
    }

    public function logout()
    {
        $result = $this->authService->logout($_SESSION);
        // Limpiar sesión en el controlador (concern de HTTP)
        session_unset();
        session_destroy();
        // Redirigir
        $redirect = $result['data']['redirect'] ?? ($_ENV['APP_URL'] ?? 'http://localhost/');
        header("Location: " . $redirect);
        exit;
    }
}
