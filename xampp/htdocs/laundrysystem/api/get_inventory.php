<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Inventory.php';
require_once __DIR__ . '/../app/controllers/InventoryController.php';

use App\Controllers\InventoryController;

$controller = new InventoryController();
$controller->listItems();
?>
