<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/models/User.php";
require_once __DIR__ . "/../app/models/Order.php";
require_once __DIR__ . "/../app/controllers/AuthController.php";
require_once __DIR__ . "/../app/controllers/OrderController.php";

$controller = new \App\Controllers\OrderController();
$controller->getAllOrders();
?>
