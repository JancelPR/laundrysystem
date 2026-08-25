<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . '/../config/database.php';

use Config\Database;

try {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input || !is_array($input)) {
        echo json_encode(["status" => "error", "message" => "Invalid payload received."]);
        exit;
    }

    $pdo = Database::getConnection();

    // Ensure system_settings table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(64) PRIMARY KEY,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) 
                           VALUES (:key, :value) 
                           ON DUPLICATE KEY UPDATE setting_value = :value_update");

    foreach ($input as $key => $val) {
        $cleanKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
        if (!empty($cleanKey)) {
            $valStr = is_bool($val) ? ($val ? '1' : '0') : (string)$val;
            $stmt->execute([
                'key' => $cleanKey,
                'value' => $valStr,
                'value_update' => $valStr
            ]);
        }
    }

    echo json_encode(["status" => "success", "message" => "System settings updated successfully."]);
} catch (\Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
