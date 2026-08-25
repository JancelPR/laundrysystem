<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = \Config\Database::getConnection();

    // Create Inventory Items Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `inventory_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `item_name` VARCHAR(150) NOT NULL,
        `category` VARCHAR(100) NOT NULL DEFAULT 'Detergent',
        `current_stock` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `unit` VARCHAR(30) NOT NULL DEFAULT 'liters',
        `reorder_level` DECIMAL(10,2) NOT NULL DEFAULT 5.00,
        `cost_per_unit` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Check if table is empty, if so seed starter items
    $stmt = $pdo->query("SELECT COUNT(*) FROM `inventory_items`");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $seed = $pdo->prepare("INSERT INTO `inventory_items` (`item_name`, `category`, `current_stock`, `unit`, `reorder_level`, `cost_per_unit`) VALUES
            ('Liquid Laundry Detergent (Lavender)', 'Detergent', 45.00, 'Liters', 10.00, 85.00),
            ('Fabric Softener (Fresh Ocean)', 'Softener', 30.00, 'Liters', 8.00, 95.00),
            ('Color-Safe Oxygen Bleach', 'Bleach', 15.50, 'Liters', 5.00, 120.00),
            ('Laundry Packaging Bags (Large Roll)', 'Packaging', 180.00, 'Pcs', 50.00, 3.50),
            ('Heavy Stain Remover Spray', 'Chemicals', 8.00, 'Bottles', 3.00, 175.00),
            ('Dry Cleaning Solvent (Eco Clean)', 'Chemicals', 22.00, 'Liters', 5.00, 310.00)
        ");
        $seed->execute();
    }

    echo json_encode(["status" => "success", "message" => "Database tables and starter inventory initialized successfully."]);
} catch (\PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
