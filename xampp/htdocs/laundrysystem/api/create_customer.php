<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/models/User.php";
require_once __DIR__ . "/../app/controllers/AuthController.php";
require_once __DIR__ . "/../app/controllers/UserController.php";

$controller = new \App\Controllers\UserController();
$controller->createCustomer();
?>
