<?php
namespace App\Config;

use DateTime;
use DateTimeZone;
use Exception;
class TimezoneManager
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function applyTimezone(): void
    {
        try {
            $region = $_SESSION['timezone'] ?? 'America/Los_Angeles';
            $tz = new DateTimeZone($region);
            $now = new DateTime('now', $tz);
            $offset = $now->format('P'); // Ejemplo: -04:00
            $this->mysqli->query("SET time_zone = '{$offset}'");
        } catch (Exception $e) {
            error_log("Error applying timezone to MariaDB: " . $e->getMessage());
        }
    }
}
