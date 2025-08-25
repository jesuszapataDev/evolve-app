<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\PasswordRecoveryService;

class RecoveryPassWordController
{
    private PasswordRecoveryService $service;

    public function __construct()
    {
        $this->service = new PasswordRecoveryService();
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

    private function input(): array
    {
        $json = json_decode(file_get_contents('php://input'), true) ?? [];
        // Permitimos tanto JSON como x-www-form-urlencoded
        return array_merge($_POST ?? [], $json);
    }

    /** Solo UserModel + email → envía link (sin preguntas de seguridad) */
    public function verifyEmail()
    {
        $in   = $this->input();
        $lang = strtoupper($in['lang'] ?? 'EN');
        $r    = $this->service->verifyEmail($in['email'] ?? '', $lang);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    /** Mantengo endpoints auxiliares de disponibilidad usados en formularios */
    public function checkEmail()
    {
        $in   = $this->input();
        $lang = strtoupper($in['lang'] ?? 'EN');
        $r    = $this->service->checkEmailAvailability($in['email'] ?? '', $lang);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    public function checkTelephone()
    {
        $in   = $this->input();
        $lang = strtoupper($in['lang'] ?? 'EN');
        $r    = $this->service->checkTelephoneAvailability($in['telephone'] ?? '', $lang);
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }

    /** Actualiza password con userId o token; valida expiración 10 minutos */
    public function updatePassword()
    {
        $in   = $this->input();
        $lang = strtoupper($in['lang'] ?? 'EN');

        $r = $this->service->updatePassword(
            (string)($in['new_password'] ?? ''),
            $in['userId']  ?? null,
            $in['token']   ?? null,
            $lang
        );
        return $this->jsonResponse($r['value'], $r['message'], $r['data'], $r['status']);
    }
}
