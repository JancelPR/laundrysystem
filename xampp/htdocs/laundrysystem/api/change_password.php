<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . '/../config/database.php';

use Config\Database;

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST;
}

$userId = isset($data['user_id']) ? (int)$data['user_id'] : 0;
$currentPassword = isset($data['current_password']) ? (string)$data['current_password'] : '';
$newPassword = isset($data['new_password']) ? (string)$data['new_password'] : '';

if ($userId <= 0 || empty($currentPassword) || empty($newPassword)) {
    echo json_encode(["status" => "error", "message" => "All password fields are required."]);
    exit;
}

if (strlen($newPassword) < 6) {
    echo json_encode(["status" => "error", "message" => "New password must be at least 6 characters long."]);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User account not found."]);
        exit;
    }

    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
        exit;
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
    $res = $updateStmt->execute(['password' => $newHash, 'id' => $userId]);

    if ($res) {
        echo json_encode(["status" => "success", "message" => "Password changed successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update password."]);
    }
} catch (\PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
