<?php
require_once __DIR__ . '/../config/Database.php';

class AuthModel
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Login por email
     */
    public function loginUser(string $email): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT user_id, first_name, last_name, sex, email, password, telephone, timezone, status, rol 
                                        FROM {$this->table} 
                                        WHERE email = ? AND deleted_at IS NULL
                                        LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener resultado: " . $stmt->error);
            }

            $usuario = $result->fetch_assoc();
            $stmt->close();

            return $usuario ?: null;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    /**
     * Validar si el usuario tiene preguntas de seguridad
     */
    public function checkSecurityQuestions(string $userId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT user_id FROM security_questions WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener resultado: " . $stmt->error);
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            $stmt->close();
            return $data;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    /**
     * Registrar un nuevo usuario
     */
    public function registerUser(array $data): bool
    {
        $this->db->begin_transaction();
        try {
            // Verificar si el correo ya está registrado (solo en registros activos)
            $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
            if (!$check) {
                throw new mysqli_sql_exception("Error al preparar la verificación: " . $this->db->error);
            }

            $check->bind_param("s", $data['email']);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                throw new mysqli_sql_exception("Este correo ya está registrado.");
            }
            $check->close();

            // Inicializar entorno y zona horaria
            $userId = 0; // aún no existe
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $createdAt = $env->getCurrentDatetime();

            // Generar UUID manualmente
            $uuid = $this->generateUUIDv4();

            // Valores por defecto
            $status = 1; // activo
            $rol = "user"; // por defecto
            $timezone = "America/Los_Angeles"; // por defecto

            // Insertar usuario
            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
                (user_id, first_name, last_name, sex, email, password, telephone, timezone, status, rol, created_at, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar inserción: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssssssssisss",
                $uuid,
                $data['first_name'],
                $data['last_name'],
                $data['sex'],
                $data['email'],
                $data['password'],
                $data['telephone'],
                $timezone,
                $status,
                $rol,
                $createdAt,
                $uuid // se registra como su propio created_by
            );

            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error al ejecutar inserción: " . $stmt->error);
            }

            $stmt->close();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Generar UUID v4
     */
    private function generateUUIDv4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Verificar si existe un archivo de imagen
     */
    public function checkImageExists(string $relativePath): bool
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($relativePath, '/');
        return file_exists($path) && is_file($path);
    }
}
