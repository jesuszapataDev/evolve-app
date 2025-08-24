<?php
namespace App\Models;

use App\Config\ClientEnvironmentInfo;
use App\Config\Database;
use App\Config\TimezoneManager;

class AuditLogModel
{
    private $db;
    private $table = "audit_log";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        try {
            $query = "SELECT *
                      FROM {$this->table}
                      ORDER BY action_timestamp DESC";

            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener registros de auditoría: " . $this->db->error);
            }

            $logs = [];
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }

            return $logs;
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }

    public function getById($id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT *
                                        FROM {$this->table}
                                        WHERE audit_id = ?
                                        LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc() ?: null;
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }

    public function exportAllToCSV()
    {
        try {
            $query = "SELECT *
                      FROM {$this->table}
                      ORDER BY action_timestamp DESC";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $this->db->error);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "audit_log_export.csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                // CSV header
                fputcsv($output, [
                    'Audit ID', 'Table Name', 'Record ID', 'Action Type', 'Action By', 'Action Timestamp',
                    'Changes', 'Full Row', 'Client IP', 'Client Hostname', 'User Agent', 'Client OS',
                    'Client Browser', 'Domain Name', 'Request URI', 'Server Hostname'
                ]);

                // Data rows
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                $stmt->close();
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No audit records found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
