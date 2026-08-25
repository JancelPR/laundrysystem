<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Inventory.php';
require_once __DIR__ . '/../app/controllers/InventoryController.php';

use App\Controllers\InventoryController;

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

$id = isset($data['id']) ? (int)$data['id'] : 0;

$controller = new InventoryController();
$controller->deleteItem($id);
?>
