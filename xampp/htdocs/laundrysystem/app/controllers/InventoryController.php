<?php
namespace App\Controllers;

use App\Models\Inventory;

class InventoryController {
    private $inventoryModel;

    public function __construct() {
        $this->inventoryModel = new Inventory();
    }

    public function listItems() {
        $items = $this->inventoryModel->getAll();
        echo json_encode(["status" => "success", "data" => $items]);
    }

    public function createItem($data) {
        if (empty($data['item_name'])) {
            echo json_encode(["status" => "error", "message" => "Item name is required."]);
            return;
        }

        $res = $this->inventoryModel->create($data);
        if ($res) {
            echo json_encode(["status" => "success", "message" => "Inventory item created successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to create inventory item."]);
        }
    }

    public function updateStock($id, $newStock) {
        if ($id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid item ID."]);
            return;
        }

        $res = $this->inventoryModel->updateStock($id, $newStock);
        if ($res) {
            echo json_encode(["status" => "success", "message" => "Stock updated successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update stock."]);
        }
    }

    public function deleteItem($id) {
        if ($id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid item ID."]);
            return;
        }

        $res = $this->inventoryModel->delete($id);
        if ($res) {
            echo json_encode(["status" => "success", "message" => "Inventory item deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete item."]);
        }
    }
}
?>
