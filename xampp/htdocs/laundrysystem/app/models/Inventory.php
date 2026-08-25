<?php
namespace App\Models;

use Config\Database;
use PDO;

class Inventory {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM inventory_items ORDER BY category ASC, item_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM inventory_items WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO inventory_items (item_name, category, current_stock, unit, reorder_level, cost_per_unit) 
            VALUES (:item_name, :category, :current_stock, :unit, :reorder_level, :cost_per_unit)");
        return $stmt->execute([
            'item_name' => $data['item_name'],
            'category' => $data['category'] ?? 'Detergent',
            'current_stock' => $data['current_stock'] ?? 0,
            'unit' => $data['unit'] ?? 'Liters',
            'reorder_level' => $data['reorder_level'] ?? 5,
            'cost_per_unit' => $data['cost_per_unit'] ?? 0
        ]);
    }

    public function updateStock($id, $newStock) {
        $stmt = $this->pdo->prepare("UPDATE inventory_items SET current_stock = :current_stock WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'current_stock' => $newStock
        ]);
    }

    public function updateItem($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE inventory_items SET 
            item_name = :item_name, 
            category = :category, 
            current_stock = :current_stock, 
            unit = :unit, 
            reorder_level = :reorder_level, 
            cost_per_unit = :cost_per_unit 
            WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'item_name' => $data['item_name'],
            'category' => $data['category'] ?? 'Detergent',
            'current_stock' => $data['current_stock'] ?? 0,
            'unit' => $data['unit'] ?? 'Liters',
            'reorder_level' => $data['reorder_level'] ?? 5,
            'cost_per_unit' => $data['cost_per_unit'] ?? 0
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM inventory_items WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
