<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\UserModel;



class UserService
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /* ============
     *  Helpers
     * ============ */
    private function resp(bool $value, string $message = '', $data = null): array
    {
        return [
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : []),
        ];
    }

    private function loadLang(string $langCode): array
    {
        // Si APP_ROOT no está definida (p.ej., en entornos de prueba), la definimos al raíz del proyecto.
        if (!defined('APP_ROOT')) {
            define('APP_ROOT', rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/'));
            // __DIR__ apunta a /services; .. sube al raíz del proyecto donde está /lang
        }

        $code = strtoupper($langCode ?: 'EN');
        $file = APP_ROOT . "/lang/{$code}.php";

        return is_file($file) ? include $file : [];
    }


    /**
     * Sube la imagen de perfil, la normaliza a JPG y devuelve la ruta relativa.
     * Devuelve null si no hay archivo; lanza excepción si el archivo es inválido.
     */
    /***************
     * Subida de imagen de perfil usando APP_ROOT
     ***************/
    private function handleProfileImageUpload(string $userId, array $files): ?string
    {
        if (empty($files['profile_image']) || $files['profile_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Asegurar APP_ROOT
        if (!defined('APP_ROOT')) {
            define('APP_ROOT', rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/'));
            // __DIR__ = /services; APP_ROOT = raíz del proyecto (donde vive /uploads)
        }

        $tmpPath = $files['profile_image']['tmp_name'];
        $mimeInfo = @getimagesize($tmpPath);
        if ($mimeInfo === false) {
            throw new RuntimeException("El archivo no es una imagen válida.");
        }

        switch ($mimeInfo['mime']) {
            case 'image/jpeg':
                $srcImg = imagecreatefromjpeg($tmpPath);
                break;
            case 'image/png':
                $srcImg = imagecreatefrompng($tmpPath);
                $width = imagesx($srcImg);
                $height = imagesy($srcImg);
                $bg = imagecreatetruecolor($width, $height);
                $white = imagecolorallocate($bg, 255, 255, 255);
                imagefill($bg, 0, 0, $white);
                imagecopy($bg, $srcImg, 0, 0, 0, 0, $width, $height);
                imagedestroy($srcImg);
                $srcImg = $bg;
                break;
            case 'image/gif':
                $srcImg = imagecreatefromgif($tmpPath);
                break;
            default:
                throw new RuntimeException("Formato de imagen no soportado ({$mimeInfo['mime']}).");
        }

        // Guardar bajo APP_ROOT/uploads/users/
        $uploadDir = APP_ROOT . '/uploads/users/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            imagedestroy($srcImg);
            throw new RuntimeException("No se pudo crear el directorio de uploads.");
        }

        $filename = "user_{$userId}.jpg";
        $destination = $uploadDir . $filename;

        if (!imagejpeg($srcImg, $destination, 85)) {
            imagedestroy($srcImg);
            throw new RuntimeException("Error al generar el JPEG.");
        }

        imagedestroy($srcImg);

        // Ruta pública relativa (sirve para el front)
        return "/uploads/users/{$filename}";
    }

    /* ========================
     *  Casos de uso (Servicio)
     * ======================== */

    public function findByEmail(?string $email): array
    {
        try {
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->resp(false, "Email inválido o faltante.");
            }
            $user = $this->userModel->getUserByEmail($email);
            return $user ? $this->resp(false, '', $user) : $this->resp(true, "");
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Error al obtener usuario: " . $e->getMessage());
        }
    }

    public function findByTelephone(?string $telephone): array
    {
        try {
            if (!$telephone || !is_string($telephone)) {
                return $this->resp(false, "Teléfono inválido o faltante.");
            }
            // Formatear telefono
            $telephone = preg_replace('/[^\d]/', '', $telephone); // Eliminar caracteres no numéricos
            $user = $this->userModel->getUserByTelephone($telephone);
            return $user ? $this->resp(true, '', $user) : $this->resp(false, "Usuario no encontrado");
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Error al obtener usuario: " . $e->getMessage());
        }
    }

    public function updateSystemTypeBySessionUser(string $userId, ?string $systemType, string $langCode): array
    {
        $lang = strtoupper($langCode ?: 'EN');
        if (!$systemType) {
            $msg = $lang === 'ES' ? "Falta el valor de system_type." : "Missing system_type value.";
            return $this->resp(false, $msg);
        }

        try {
            $ok = $this->userModel->updateSystemTypeByUserId($userId, $systemType);
            $success = $lang === 'ES' ? "Sistema de unidades actualizado correctamente." : "System type updated successfully.";
            $fail = $lang === 'ES' ? "Error al actualizar el sistema de unidades." : "Error updating system type.";
            return $this->resp((bool) $ok, $ok ? $success : $fail, $ok);
        } catch (Throwable $e) {
            $msg = ($lang === 'ES' ? "Error al actualizar el sistema de unidades: " : "Error updating system type: ") . $e->getMessage();
            return $this->resp(false, $msg);
        }
    }

    public function listAll(): array
    {
        try {
            $users = $this->userModel->getAll();
            return $this->resp(true, '', $users);
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Error al listar usuarios: " . $e->getMessage());
        }
    }

    public function getById(?string $id): array
    {
        try {
            $user = $this->userModel->getById($id);
            return $user ? $this->resp(true, '', $user) : $this->resp(false, "Usuario no encontrado");
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Error al obtener usuario: " . $e->getMessage());
        }
    }

    public function create(array $data): array
    {
        try {
            $result = $this->userModel->create($data);
            return $this->resp((bool) $result, $result ? "Usuario guardado correctamente" : "Error al guardar usuario", $result);
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Error al guardar usuario: " . $e->getMessage());
        }
    }

    public function updateStatus(array $data): array
    {
        try {
            if (empty($data['user_id']) || !isset($data['status'])) {
                return $this->resp(false, "Missing user ID or status value.");
            }
            $ok = $this->userModel->updateStatus($data);
            return $ok
                ? $this->resp(true, "Status updated successfully", $data)
                : $this->resp(false, "Failed to update status.");
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Database error: " . $e->getMessage());
        }
    }

    public function update(string $id, array $data): array
    {
        try {
            $result = $this->userModel->update($id, $data);
            return $this->resp(true, $result ? "Usuario actualizado correctamente" : "Error al actualizar usuario", $result);
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Error al actualizar usuario: " . $e->getMessage());
        }
    }

    public function updateProfile(string $id, array $payload, array $files): array
    {
        try {
            $imagePath = $this->handleProfileImageUpload($id, $files);
            if ($imagePath !== null) {
                $payload['profile_image'] = $imagePath;
                $_SESSION['user_image'] = $imagePath; // refrescar en sesión
            }
            $result = $this->userModel->updateProfile($id, $payload);
            return $this->resp(true, $result ? "Usuario actualizado correctamente" : "Error al actualizar usuario", $result);
        } catch (Throwable $e) {
            return $this->resp(false, "Error al actualizar usuario: " . $e->getMessage());
        }
    }

    public function delete(string $id, string $langCode): array
    {
        $t = $this->loadLang($langCode);
        $msgSuccess = $t['user_deleted_successfully'] ?? "User deleted successfully";
        $msgError = $t['user_delete_error'] ?? "Error deleting user";

        try {
            $deleted = $this->userModel->delete($id);
            return $deleted
                ? $this->resp(true, $msgSuccess)
                : $this->resp(false, $msgError);
        } catch (mysqli_sql_exception $e) {
            $errorMsg = $e->getMessage();
            if (stripos($errorMsg, 'related records exist') !== false) {
                return $this->resp(false, "$msgError: $errorMsg");
            }
            return $this->resp(false, "$msgError: $errorMsg");
        }
    }

    public function countUsers(): array
    {
        try {
            $total = $this->userModel->countUsers();
            return $this->resp(true, '', ['total' => $total]);
        } catch (Throwable $e) {
            return $this->resp(false, 'Error al contar usuarios: ' . $e->getMessage());
        }
    }

    public function getSessionUserData(?string $userId): array
    {
        try {
            $result = $this->userModel->getSessionUserData($userId);
            return $this->resp(true, '', $result);
        } catch (mysqli_sql_exception $e) {
            return $this->resp(false, "Error al obtener datos del usuario: " . $e->getMessage());
        }
    }
}
