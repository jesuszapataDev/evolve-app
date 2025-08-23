<?php

require_once __DIR__ . '/../models/UserModel.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Sube la imagen de perfil, la normaliza a JPG y devuelve la ruta relativa.
     */
    private function handleProfileImageUpload($userId): ?string
    {
        if (empty($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmpPath = $_FILES['profile_image']['tmp_name'];
        $mimeInfo = getimagesize($tmpPath);
        if ($mimeInfo === false) {
            throw new RuntimeException("El archivo no es una imagen válida.");
        }

        switch ($mimeInfo['mime']) {
            case 'image/jpeg':
                $srcImg = imagecreatefromjpeg($tmpPath);
                break;
            case 'image/png':
                $srcImg = imagecreatefrompng($tmpPath);
                $width  = imagesx($srcImg);
                $height = imagesy($srcImg);
                $bg     = imagecreatetruecolor($width, $height);
                $white  = imagecolorallocate($bg, 255, 255, 255);
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

        $uploadDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/uploads/users/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            imagedestroy($srcImg);
            throw new RuntimeException("No se pudo crear el directorio de uploads.");
        }

        $filename    = "user_{$userId}.jpg";
        $destination = $uploadDir . $filename;

        if (!imagejpeg($srcImg, $destination, 85)) {
            imagedestroy($srcImg);
            throw new RuntimeException("Error al generar el JPEG.");
        }

        imagedestroy($srcImg);
        return "/uploads/users/{$filename}";
    }

    /**
     * Actualiza el tipo de sistema (métrico/imperial) del usuario en sesión.
     */
    public function update_system_type_session_user()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $lang   = strtoupper($_SESSION['idioma'] ?? 'EN');

        if ($method === 'PUT' || ($method === 'POST' && (($_POST['_method'] ?? '') === 'PUT'))) {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                $msg = $lang === 'ES' ? "Usuario no autenticado." : "User not authenticated.";
                return $this->errorResponse(401, $msg);
            }

            $putData = [];
            if ($method === 'POST') {
                $putData = $_POST;
            } else {
                parse_str(file_get_contents("php://input"), $putData);
            }

            $systemType = $putData['system_type'] ?? null;
            if (!$systemType) {
                $msg = $lang === 'ES' ? "Falta el valor de system_type." : "Missing system_type value.";
                return $this->errorResponse(400, $msg);
            }

            try {
                $ok = $this->userModel->updateSystemTypeByUserId($userId, $systemType);

                $success = $lang === 'ES' ? "Sistema de unidades actualizado correctamente." : "System type updated successfully.";
                $fail    = $lang === 'ES' ? "Error al actualizar el sistema de unidades."   : "Error updating system type.";

                return $this->jsonResponse(true, $ok ? $success : $fail, $ok);
            } catch (\Exception $e) {
                $msg = ($lang === 'ES' ? "Error al actualizar el sistema de unidades: " : "Error updating system type: ") . $e->getMessage();
                return $this->errorResponse(400, $msg);
            }
        }

        $msg = $lang === 'ES' ? "Método no permitido. Se requiere PUT." : "Method not allowed. PUT required.";
        return $this->errorResponse(405, $msg);
    }

    /**
     * JSON helpers
     */
    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value'   => $value,
            'message' => $message,
            'data'    => is_array($data) ? $data : ($data !== null ? [$data] : []),
        ];

        echo json_encode($response);
        exit;
    }

    private function errorResponse(int $http_code, string $message)
    {
        http_response_code($http_code);
        $this->jsonResponse(false, $message);
    }

    /**
     * USERS CRUD & RELATED
     */
    public function showAll()
    {
        try {
            $users = $this->userModel->getAll();
            return $this->jsonResponse(true, '', $users);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al listar usuarios: " . $e->getMessage());
        }
    }

    public function showById($params)
    {
        $id = $params['id'] ?? null;
        try {
            $user = $this->userModel->getById($id);
            return $user
                ? $this->jsonResponse(true, '', $user)
                : $this->jsonResponse(false, "Usuario no encontrado");
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al obtener usuario: " . $e->getMessage());
        }
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Método no permitido. Se requiere POST.");
        }

        try {
            $data   = $_POST;
            $result = $this->userModel->create($data);
            return $this->jsonResponse(
                (bool)$result,
                $result ? "Usuario guardado correctamente" : "Error al guardar usuario"
            );
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al guardar usuario: " . $e->getMessage());
        }
    }

    public function updateStatus($params)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse(405, "Method Not Allowed. Use POST.");
        }

        $data   = $_POST;
        $userId = $params['id'] ?? $data['user_id'] ?? null;

        if (!$userId || !isset($data['status'])) {
            return $this->errorResponse(400, "Missing user ID or status value.");
        }

        $data['user_id'] = $userId;

        try {
            $ok = $this->userModel->updateStatus($data);
            return $ok
                ? $this->jsonResponse(true, "Status updated successfully", $data)
                : $this->errorResponse(400, "Failed to update status.");
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(500, "Database error: " . $e->getMessage());
        }
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

        try {
            $data   = $_POST;
            $result = $this->userModel->update($id, $data);
            return $this->jsonResponse(true, $result ? "Usuario actualizado correctamente" : "Error al actualizar usuario", $result);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al actualizar usuario: " . $e->getMessage());
        }
    }

    public function update_profile($params)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!($method === 'PUT' || ($method === 'POST' && (($_POST['_method'] ?? '') === 'PUT')))) {
            return $this->errorResponse(405, "Método no permitido. Se requiere PUT.");
        }

        $id = $params['id'] ?? null;
        $putData = [];
        if ($method === 'POST') {
            $putData = $_POST;
        } else {
            parse_str(file_get_contents("php://input"), $putData);
        }

        try {
            // Imagen de perfil (opcional)
            $imagePath = $this->handleProfileImageUpload($id);
            if ($imagePath !== null) {
                $putData['profile_image']  = $imagePath;
                $_SESSION['user_image']    = $imagePath; // refrescar en sesión
            }

            $result = $this->userModel->updateProfile($id, $putData);
            return $this->jsonResponse(
                true,
                $result ? "Usuario actualizado correctamente" : "Error al actualizar usuario",
                $result
            );
        } catch (\Exception $e) {
            return $this->errorResponse(400, "Error al actualizar usuario: " . $e->getMessage());
        }
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

        $lang            = strtoupper($_SESSION['idioma'] ?? 'EN');
        $archivo_idioma  = $_SERVER['DOCUMENT_ROOT'] . "/lang/{$lang}.php";
        $traducciones    = file_exists($archivo_idioma) ? include $archivo_idioma : [];
        $msgSuccess      = $traducciones['user_deleted_successfully'] ?? "User deleted successfully";
        $msgError        = $traducciones['user_delete_error'] ?? "Error deleting user";

        try {
            $deleted = $this->userModel->delete($id);
            return $deleted
                ? $this->jsonResponse(true, $msgSuccess)
                : $this->jsonResponse(false, $msgError);
        } catch (mysqli_sql_exception $e) {
            $errorMsg = $e->getMessage();

            if (stripos($errorMsg, 'related records exist') !== false) {
                return $this->jsonResponse(false, "$msgError: $errorMsg");
            }

            return $this->errorResponse(400, "$msgError: $errorMsg");
        }
    }

    public function countUsers()
    {
        try {
            $total = $this->userModel->countUsers();
            return $this->jsonResponse(true, '', ['total' => $total]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error al contar usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Datos del usuario para la sesión (user-only).
     */
    public function getSessionUserData($params)
    {
        $userId = $params['id'] ?? null;
        try {
            $result = $this->userModel->getSessionUserData($userId);
            return $this->jsonResponse(true, '', $result);
        } catch (mysqli_sql_exception $e) {
            return $this->errorResponse(400, "Error al obtener datos del usuario: " . $e->getMessage());
        }
    }
}
