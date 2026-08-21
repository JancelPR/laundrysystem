<?php
namespace App\Controllers;

use App\Models\User;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function getCustomers() {
        header("Content-Type: application/json");
        AuthController::authenticateToken();

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $customers = $this->userModel->getCustomers($search);

        http_response_code(200);
        echo json_encode([
            "count" => count($customers),
            "customers" => $customers
        ]);
    }

    public function getUsersByRole() {
        header("Content-Type: application/json");
        $userData = AuthController::authenticateToken();

        if ($userData['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["message" => "Admin access required."]);
            return;
        }

        $role = isset($_GET['role']) ? trim($_GET['role']) : 'customer';
        $users = $this->userModel->getByRole($role);

        http_response_code(200);
        echo json_encode([
            "role" => $role,
            "count" => count($users),
            "users" => $users
        ]);
    }

    public function createCustomer() {
        header("Content-Type: application/json");
        $userData = AuthController::authenticateToken();

        if ($userData['role'] !== 'admin' && $userData['role'] !== 'staff') {
            http_response_code(403);
            echo json_encode(["message" => "Permission denied."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['full_name']) || empty($data['phone'])) {
            http_response_code(400);
            echo json_encode(["message" => "Full name and phone number are required."]);
            return;
        }

        $phone = trim($data['phone']);
        $existing = $this->userModel->findByPhone($phone);
        if ($existing) {
            http_response_code(409);
            echo json_encode(["message" => "A customer with phone $phone already exists."]);
            return;
        }

        $fullName = trim($data['full_name']);
        $address = isset($data['address']) ? trim($data['address']) : '';
        $id = $this->userModel->createCustomer($fullName, $phone, $address);

        http_response_code(201);
        echo json_encode([
            "message" => "Customer account registered successfully!",
            "customer_id" => $id
        ]);
    }

    public function updateCustomer() {
        header("Content-Type: application/json");
        $userData = AuthController::authenticateToken();

        if ($userData['role'] !== 'admin' && $userData['role'] !== 'staff') {
            http_response_code(403);
            echo json_encode(["message" => "Permission denied."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['customer_id']) || empty($data['full_name']) || empty($data['phone'])) {
            http_response_code(400);
            echo json_encode(["message" => "Customer ID, name, and phone are required."]);
            return;
        }

        $customerId = (int)$data['customer_id'];
        $fullName = trim($data['full_name']);
        $phone = trim($data['phone']);
        $address = isset($data['address']) ? trim($data['address']) : '';

        $success = $this->userModel->updateCustomer($customerId, $fullName, $phone, $address);

        if ($success) {
            http_response_code(200);
            echo json_encode(["message" => "Customer profile updated successfully!"]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Failed to update customer profile."]);
        }
    }
}
?>
