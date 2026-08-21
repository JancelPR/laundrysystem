<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private static $host = "localhost";
    private static $dbname = "athena_laundry";
    private static $username = "root";
    private static $password = "";
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8mb4",
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["message" => "Database Connection Error: " . $e->getMessage()]);
                exit();
            }
        }
        return self::$pdo;
    }
}
?>
