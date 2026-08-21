<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/models/User.php";
require_once __DIR__ . "/../app/controllers/AuthController.php";

$controller = new \App\Controllers\AuthController();
$controller->login();
?>