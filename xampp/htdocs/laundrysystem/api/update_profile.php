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
$fullName = isset($data['full_name']) ? trim($data['full_name']) : '';
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$address = isset($data['address']) ? trim($data['address']) : '';

if ($userId <= 0 || empty($fullName) || empty($phone)) {
    echo json_encode(["status" => "error", "message" => "Full Name and Phone Number are required."]);
    exit;
}

try {
    $pdo = Database::getConnection();

    // Check if phone is used by another user
    $check = $pdo->prepare("SELECT id FROM users WHERE phone = :phone AND id != :id");
    $check->execute(['phone' => $phone, 'id' => $userId]);
    if ($check->fetch()) {
        echo json_encode(["status" => "error", "message" => "Phone number is already associated with another account."]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, phone = :phone, address = :address WHERE id = :id");
    $res = $stmt->execute([
        'id' => $userId,
        'full_name' => $fullName,
        'phone' => $phone,
        'address' => $address
    ]);

    if ($res) {
        // Fetch updated record
        $stmtUser = $pdo->prepare("SELECT id, identifier, full_name, phone, address, role FROM users WHERE id = :id");
        $stmtUser->execute(['id' => $userId]);
        $updatedUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success", 
            "message" => "Profile updated successfully.",
            "user" => $updatedUser
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update profile."]);
    }
} catch (\PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
