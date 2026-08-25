<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once __DIR__ . '/../config/database.php';

use Config\Database;

$defaultSettings = [
    "store_name" => "LaundryEase Hub",
    "store_phone" => "+63 917 123 4567",
    "store_address" => "123 Coastal Ave, Suite 101, Metro Manila",
    "operating_hours" => "Mon - Sun: 7:00 AM - 8:00 PM",
    "rate_wash_fold" => "35.00",
    "rate_dry_clean" => "150.00",
    "rate_steam_press" => "80.00",
    "rate_student" => "120.00",
    "fee_express_rush" => "150.00",
    "receipt_header" => "Thank you for choosing LaundryEase!",
    "receipt_footer" => "Please present this stub upon pickup. Unclaimed laundry after 30 days will be donated.",
    "enable_sms_alerts" => "1",
    "enable_whatsapp" => "1"
];

try {
    $pdo = Database::getConnection();

    // Ensure system_settings table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(64) PRIMARY KEY,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
    $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $finalSettings = array_merge($defaultSettings, $dbSettings);

    echo json_encode(["status" => "success", "data" => $finalSettings]);
} catch (\Exception $e) {
    echo json_encode(["status" => "success", "data" => $defaultSettings, "note" => $e->getMessage()]);
}
