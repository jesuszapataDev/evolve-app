<?php


namespace App\Models;

use App\Config\Database;
use ClientEnvironmentInfo;
use Exception;
use mysqli_sql_exception;
use TimezoneManager;

class CountryModel
{
    private $db;
    private $table = 'countries';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY country_id  ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE country_id  = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $createdAt = $env->getCurrentDatetime();

            // Generar UUID
            $uuid = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (country_id, suffix, full_prefix, normalized_prefix, country_name, phone_mask, created_at, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing insert: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssssssss",
                $uuid,
                $data['suffix'],
                $data['full_prefix'],
                $data['normalized_prefix'],
                $data['country_name'],
                $data['phone_mask'],
                $createdAt,
                $userId
            );

            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    private function generateUUIDv4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }


    public function update($id, $data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} 
                SET suffix = ?, full_prefix = ?, normalized_prefix = ?, country_name = ?, phone_mask = ?, updated_at = ?, updated_by = ? 
                WHERE country_id  = ?");
            $stmt->bind_param(
                "ssssssss",
                $data['suffix'],
                $data['full_prefix'],
                $data['normalized_prefix'],
                $data['country_name'],
                $data['phone_mask'],
                $updatedAt,
                $userId,
                $id
            );

            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            // Cargar traducciones según idioma
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $archivo_idioma = $_SERVER['DOCUMENT_ROOT'] . "/lang/{$lang}.php";
            $traducciones = file_exists($archivo_idioma) ? include $archivo_idioma : [];

            $userId = $_SESSION['user_id'] ?? null;

            // Obtener el normalized_prefix del país
            $stmtCountry = $this->db->prepare("SELECT normalized_prefix FROM {$this->table} WHERE country_id  = ?");
            $stmtCountry->bind_param("s", $id);
            $stmtCountry->execute();
            $result = $stmtCountry->get_result();
            $countryRow = $result->fetch_assoc();
            $stmtCountry->close();

            if (!$countryRow) {
                throw new mysqli_sql_exception($traducciones['country_not_found'] ?? "Country not found.");
            }

            $normalizedPrefix = $countryRow['normalized_prefix'];

            // Lista de tablas y campos a verificar
            $relatedTables = [
                'users' => 'telephone',
                'administrators' => 'phone',
                'specialists' => 'phone'
            ];

            foreach ($relatedTables as $table => $field) {
                // Consulta que extrae el prefijo dentro de los paréntesis
                $stmtCheck = $this->db->prepare("
                SELECT COUNT(*) as total
                FROM {$table}
                WHERE 
                    {$field} IS NOT NULL 
                    AND {$field} != '' 
                    AND SUBSTRING_INDEX(SUBSTRING_INDEX({$field}, ')', 1), '(', -1) = ?
                    AND deleted_at IS NULL
            ");
                if (!$stmtCheck) {
                    throw new mysqli_sql_exception("Error preparando la consulta de dependencias en {$table}: " . $this->db->error);
                }

                $stmtCheck->bind_param("s", $normalizedPrefix);
                $stmtCheck->execute();
                $res = $stmtCheck->get_result();
                $row = $res->fetch_assoc();
                $stmtCheck->close();

                if ($row['total'] > 0) {
                    $msg = $traducciones['country_delete_dependency'] ?? "Cannot delete country: related records exist in {table}.";
                    throw new mysqli_sql_exception(str_replace('{table}', $table, $msg));
                }
            }

            // Auditoría
            $env = new ClientEnvironmentInfo($_SERVER['DOCUMENT_ROOT'] . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            // Eliminación lógica
            $stmtDelete = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE country_id  = ?");
            if (!$stmtDelete) {
                throw new mysqli_sql_exception("Error preparando la eliminación: " . $this->db->error);
            }
            $stmtDelete->bind_param("sss", $deletedAt, $userId, $id);
            if (!$stmtDelete->execute()) {
                throw new mysqli_sql_exception("Error eliminando el país: " . $stmtDelete->error);
            }
            $stmtDelete->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    public function exportAllCountriesToCSV()
    {
        try {
            $query = "SELECT country_id , suffix, full_prefix, normalized_prefix, country_name, phone_mask 
                      FROM {$this->table} 
                      WHERE deleted_at IS NULL 
                      ORDER BY country_id  ASC";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $this->db->error);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "countries_export.csv";
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                // CSV header
                fputcsv($output, ['ID', 'Suffix', 'Full Prefix', 'Normalized Prefix', 'Country Name', 'Phone Mask']);

                // Data rows
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                $stmt->close();
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No countries found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
