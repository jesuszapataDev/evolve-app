<?php

require_once __DIR__ . '/../config/Database.php';

class UserModel
{
    private $db;
    private $table = "users";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* =========================
     *    QUERIES BÁSICAS
     * ========================= */

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
        $result = $this->db->query($sql);
        if (!$result) {
            throw new mysqli_sql_exception("Error al obtener usuarios: " . $this->db->error);
        }
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /** Búsqueda segura por columna whitelisteada */
    public function getUserBy(string $column, string $value): ?array
    {
        $allowed = ['user_id', 'email', 'telephone'];
        if (!in_array($column, $allowed, true)) {
            throw new InvalidArgumentException("Columna no permitida: {$column}");
        }
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    public function getUserByTelephone(string $telephone): ?array
    {
        $normalized = $telephone;
        $sql = "
            SELECT * FROM {$this->table}
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telephone,'(',''),')',''),'-',''),' ',''),'+',''),'.','') = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $normalized);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->fetch_assoc() ?: null;
    }

    public function countUsers(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE deleted_at IS NULL";
        $result = $this->db->query($sql);
        if (!$result) {
            throw new mysqli_sql_exception("Error al contar usuarios: " . $this->db->error);
        }
        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /* =========================
     *    PREFERENCIAS / IDIOMA
     * ========================= */

    public function setIdioma(string $lang): bool
    {
        try {
            $lang = strtoupper(trim($lang));
            if (!in_array($lang, ['EN', 'ES'], true)) {
                throw new InvalidArgumentException("Idioma no válido: $lang");
            }
            $_SESSION['idioma'] = $lang;
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /* =========================
     *    AUTENTICACIÓN / PASS
     * ========================= */

    public function authenticate(string $email, string $password): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res  = $stmt->get_result();
        $user = $res->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return null;
    }

    public function verifySecurityAnswers(string $userId, string $answer1, string $answer2): bool
    {
        $stmt = $this->db->prepare("SELECT user_id FROM security_questions WHERE user_id = ? AND answer1 = ? AND answer2 = ? LIMIT 1");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("sss", $userId, $answer1, $answer2);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows > 0;
    }

    /** Actualiza contraseña por userId (respuestas) o por token (email link) */
    public function updatePassword(array $data): bool
    {
        $newPassword = $data['newPassword'] ?? null;
        $userId      = $data['userId']      ?? null;
        $token       = $data['token']       ?? null;

        if (!$newPassword) return false;

        // Auditoría
        $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        (new TimezoneManager($this->db))->applyTimezone();
        $updatedAt = $env->getCurrentDatetime();
        $updatedBy = $userId;

        // Caso 1: con userId (seguridad)
        if (!empty($userId)) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
            if (!$stmt) throw new mysqli_sql_exception("Error al preparar la actualización: " . $this->db->error);
            $stmt->bind_param("ssss", $hash, $updatedAt, $updatedBy, $userId);
            $stmt->execute();
            $ok = $stmt->affected_rows > 0;
            $stmt->close();

            if ($ok) {
                $stmt = $this->db->prepare("DELETE pr FROM password_resets pr JOIN {$this->table} u ON u.email = pr.email WHERE u.user_id = ?");
                $stmt->bind_param("s", $userId);
                $stmt->execute();
                $stmt->close();
            }
            return $ok;
        }

        // Caso 2: con token
        if (!empty($token)) {
            $stmt = $this->db->prepare("SELECT email, created_at FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $reset = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$reset) return false;

            $createdAt = new DateTime($reset['created_at']);
            if ((time() - $createdAt->getTimestamp()) > 600) {
                $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $stmt->close();
                return false;
            }

            $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $reset['email']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$user) return false;

            $uid  = $user['user_id'];
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
            if (!$stmt) throw new mysqli_sql_exception("Error al preparar la actualización: " . $this->db->error);
            $stmt->bind_param("ssss", $hash, $updatedAt, $uid, $uid);
            $stmt->execute();
            $ok = $stmt->affected_rows > 0;
            $stmt->close();

            if ($ok) {
                $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $stmt->close();
            }
            return $ok;
        }

        return false;
    }

    /* =========================
     *          CREATE
     * ========================= */

    public function create(array $data): bool
    {
        $this->db->begin_transaction();
        try {
            // Email único (activos)
            $chk = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
            if (!$chk) throw new mysqli_sql_exception("Error preparando validación de email: " . $this->db->error);
            $chk->bind_param("s", $data['email']);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) {
                throw new mysqli_sql_exception("Este correo ya está registrado.");
            }
            $chk->close();

            $uuid           = $this->generateUUIDv4();
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Auditoría / TZ
            $sessionUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $sessionUserId);
            (new TimezoneManager($this->db))->applyTimezone();
            $createdAt = $env->getCurrentDatetime();
            $createdBy = $sessionUserId;

            $timezone = $data['timezone'] ?? 'America/Los_Angeles';
            $status   = isset($data['status']) ? (int)$data['status'] : 1;
            $rol      = $data['rol'] ?? 'user';

            $stmt = $this->db->prepare("
                INSERT INTO {$this->table}
                (user_id, first_name, last_name, sex, email, telephone, password, timezone, status, rol, created_at, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            if (!$stmt) throw new mysqli_sql_exception("Error preparando inserción: " . $this->db->error);

            $stmt->bind_param(
                "ssssssssisss",
                $uuid,
                $data['first_name'],
                $data['last_name'],
                $data['sex'],
                $data['email'],
                $data['telephone'],
                $hashedPassword,
                $timezone,
                $status,
                $rol,
                $createdAt,
                $createdBy
            );
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error al crear usuario: " . $stmt->error);
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

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

    /* =========================
     *          UPDATE
     * ========================= */

    public function update(string $id, array $data): bool
    {
        $this->db->begin_transaction();
        try {
            // Email único
            $chk = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND user_id != ? AND deleted_at IS NULL");
            if (!$chk) throw new Exception('Error al preparar validación de email: ' . $this->db->error);
            $chk->bind_param("ss", $data['email'], $id);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) {
                throw new Exception('Este correo ya está registrado por otro usuario.');
            }
            $chk->close();

            // Auditoría
            $sessionUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $sessionUserId);
            (new TimezoneManager($this->db))->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $sessionUserId;

            $sql = "UPDATE {$this->table} SET
                        first_name = ?, last_name = ?, sex = ?,
                        email = ?, telephone = ?, timezone = ?, status = ?, rol = ?,
                        updated_at = ?, updated_by = ?";

            $params = [
                $data['first_name'] ?? '',
                $data['last_name']  ?? '',
                $data['sex']        ?? '',
                $data['email']      ?? '',
                $data['telephone']  ?? '',
                $data['timezone']   ?? 'America/Los_Angeles',
                isset($data['status']) ? (int)$data['status'] : 1,
                $data['rol']        ?? 'user',
                $updatedAt,
                $updatedBy
            ];
            $types = "ssssssisss";

            if (!empty($data['password'])) {
                $sql     .= ", password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
                $types   .= "s";
            }

            $sql    .= " WHERE user_id = ?";
            $params[] = $id;
            $types   .= "s";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new Exception('Error al preparar la consulta de actualización: ' . $this->db->error);

            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                throw new Exception('No se pudo actualizar el usuario: ' . $stmt->error);
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateStatus(array $data): bool
    {
        $userId    = $data['user_id'] ?? null;
        $newStatus = $data['status'] ?? null;

        if ($userId === null || !in_array((int)$newStatus, [0,1], true)) {
            return false;
        }

        // Auditoría
        $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        (new TimezoneManager($this->db))->applyTimezone();
        $updatedAt = $env->getCurrentDatetime();
        $updatedBy = $userId;

        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
        if (!$stmt) throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);

        $newStatus = (int)$newStatus;
        $stmt->bind_param("isss", $newStatus, $updatedAt, $updatedBy, $userId);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();

        return $ok;
    }

    public function updateProfile(string $id, array $data): bool
    {
        $this->db->begin_transaction();
        try {
            // Email único
            $chk = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND user_id != ? AND deleted_at IS NULL");
            if (!$chk) throw new Exception('Error al preparar validación de email: ' . $this->db->error);
            $chk->bind_param("ss", $data['email'], $id);
            $chk->execute();
            $chk->store_result();
            if ($chk->num_rows > 0) {
                throw new Exception('Este correo ya está registrado por otro usuario.');
            }
            $chk->close();

            // Auditoría
            $sessionUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $sessionUserId);
            (new TimezoneManager($this->db))->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $sessionUserId;

            $sql = "UPDATE {$this->table} SET
                        first_name = ?, last_name = ?, sex = ?,
                        email = ?, telephone = ?, timezone = ?,
                        updated_at = ?, updated_by = ?";

            $params = [
                $data['first_name'] ?? '',
                $data['last_name']  ?? '',
                $data['sex']        ?? '',
                $data['email']      ?? '',
                $data['telephone']  ?? '',
                $data['timezone']   ?? 'America/Los_Angeles',
                $updatedAt,
                $updatedBy
            ];
            $types = "ssssssss";

            if (!empty($data['password'])) {
                $sql     .= ", password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
                $types   .= "s";
            }

            $sql    .= " WHERE user_id = ?";
            $params[] = $id;
            $types   .= "s";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) throw new Exception('Error al preparar consulta: ' . $this->db->error);
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                throw new Exception('No se pudo actualizar el usuario: ' . $stmt->error);
            }

            $this->db->commit();

            // Refrescar sesión (si aplica)
            $_SESSION['first_name'] = $data['first_name'] ?? ($_SESSION['first_name'] ?? null);
            $_SESSION['last_name']  = $data['last_name']  ?? ($_SESSION['last_name'] ?? null);
            $_SESSION['user_name']  = trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''));
            $_SESSION['email']      = $data['email']      ?? ($_SESSION['email'] ?? null);
            $_SESSION['timezone']   = $data['timezone']   ?? ($_SESSION['timezone'] ?? null);
            $_SESSION['sex']        = $data['sex']        ?? ($_SESSION['sex'] ?? null);

            return true;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /* =========================
     *          DELETE
     * ========================= */

    public function delete(string $id): bool
    {
        $this->db->begin_transaction();
        try {
            // Idioma
            $lang          = strtoupper($_SESSION['idioma'] ?? 'EN');
            $archivoIdioma = $_SERVER['DOCUMENT_ROOT'] . "/lang/{$lang}.php";
            $t             = file_exists($archivoIdioma) ? include $archivoIdioma : [];

            // Existe?
            $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE user_id = ? LIMIT 1");
            $check->bind_param("s", $id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows === 0) {
                throw new mysqli_sql_exception($t['user_not_found'] ?? "User not found.");
            }
            $check->close();

            // Dependencias (ajusta a tus tablas)
            $relatedTables = [
                'security_questions'   => ['user_id', false],
            ];
            foreach ($relatedTables as $table => [$field, $hasDeletedAt]) {
                $sql = $hasDeletedAt
                    ? "SELECT COUNT(*) AS total FROM {$table} WHERE {$field} = ? AND deleted_at IS NULL"
                    : "SELECT COUNT(*) AS total FROM {$table} WHERE {$field} = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new mysqli_sql_exception("Error preparing dependency check for {$table}: " . $this->db->error);
                }
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ((int)($row['total'] ?? 0) > 0) {
                    $msg = $t['user_delete_dependency'] ?? "Cannot delete user: related records exist in table '{table}'.";
                    $msg = str_replace('{table}', $table, $msg);
                    throw new mysqli_sql_exception($msg);
                }
            }

            // Auditoría
            $sessionUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $sessionUserId);
            (new TimezoneManager($this->db))->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();
            $deletedBy = $sessionUserId;

            // Soft delete
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing delete statement: " . $this->db->error);
            }
            $stmt->bind_param("sss", $deletedAt, $deletedBy, $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error deleting user: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /* =========================
     *   DATA PARA LA SESIÓN
     * ========================= */

    public function getSessionUserData(string $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            return ['status' => 'error', 'message' => 'Usuario no encontrado'];
        }

        $user = $res->fetch_assoc();

        // sex legible
        $user['sex'] = ($user['sex'] === 'm') ? 'Male' : (($user['sex'] === 'f') ? 'Female' : 'Other');

        // (Sin birthday/age en la nueva estructura)

        return $user;
    }
}
