<?php
namespace App\Controllers;

use App\Models\Order;
use App\Models\User;

class OrderController {
    private $orderModel;
    private $userModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->userModel = new User();
    }

    public function getAllOrders() {
        header("Content-Type: application/json");
        $userData = AuthController::authenticateToken();

        $orders = $this->orderModel->getAllWithCustomers();
        $metrics = $this->orderModel->getMetrics();
        $totalCustomers = $this->userModel->getTotalCustomerCount();

        http_response_code(200);
        echo json_encode([
            "user" => [
                "fullName" => $userData['full_name'],
                "phone"    => $userData['phone'],
                "role"     => $userData['role']
            ],
            "metrics" => [
                "totalOrders"    => $metrics['totalOrders'],
                "totalRevenue"   => $metrics['totalRevenue'],
                "totalCustomers" => $totalCustomers
            ],
            "orders" => $orders
        ]);
    }

    public function getCustomerOrders() {
        header("Content-Type: application/json");
        $userData = AuthController::authenticateToken();

        $userId = $userData['id'];
        if (($userData['role'] === 'admin' || $userData['role'] === 'staff') && !empty($_GET['user_id'])) {
            $userId = (int)$_GET['user_id'];
        }

        $customer = $this->userModel->findById($userId);
        $orders = $this->orderModel->getByUserId($userId);

        http_response_code(200);
        echo json_encode([
            "user" => [
                "id"       => $customer ? $customer['id'] : $userId,
                "fullName" => $customer ? $customer['full_name'] : $userData['full_name'],
                "phone"    => $customer ? $customer['phone'] : $userData['phone'],
                "address"  => $customer ? $customer['address'] : ''
            ],
            "orders" => $orders
        ]);
    }

    public function createOrder() {
        header("Content-Type: application/json");
        $userData = AuthController::authenticateToken();

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input data."]);
            return;
        }

        $userId = null;
        if (isset($data['customerType']) && $data['customerType'] === 'new') {
            if (empty($data['newCustomer']['fullName']) || empty($data['newCustomer']['phone'])) {
                http_response_code(400);
                echo json_encode(["message" => "Please enter customer full name and phone number."]);
                return;
            }

            $fullName = trim($data['newCustomer']['fullName']);
            $phone    = trim($data['newCustomer']['phone']);
            $address  = isset($data['newCustomer']['address']) ? trim($data['newCustomer']['address']) : '';

            $existing = $this->userModel->findByPhone($phone);
            if ($existing) {
                $userId = $existing['id'];
            } else {
                $userId = $this->userModel->createCustomer($fullName, $phone, $address);
            }
        } else {
            if (empty($data['userId'])) {
                http_response_code(400);
                echo json_encode(["message" => "Please select an existing customer."]);
                return;
            }
            $userId = (int)$data['userId'];
        }

        $servicesList = [];
        $servicesList[] = isset($data['service']) ? $data['service'] : 'Wash & Fold';
        if (!empty($data['studentRate'])) $servicesList[] = 'Student Rate';
        if (!empty($data['expressRush'])) $servicesList[] = 'Express Rush (Same-Day)';
        
        $servicesRegistered = implode(', ', $servicesList);
        $weightKg = isset($data['weightKg']) ? (float)$data['weightKg'] : 8.0;
        $totalPrice = isset($data['totalPrice']) ? (float)$data['totalPrice'] : 200.00;
        $specialInstructions = isset($data['specialInstructions']) ? trim($data['specialInstructions']) : '';
        $paymentStatus = isset($data['paymentStatus']) ? trim($data['paymentStatus']) : 'Unpaid';
        $orderStatus = 'Pending';

        $result = $this->orderModel->create(
            $userId,
            $servicesRegistered,
            $weightKg,
            $totalPrice,
            $specialInstructions,
            $paymentStatus,
            $orderStatus
        );

        http_response_code(201);
        echo json_encode([
            "message"    => "New drop-off order created successfully!",
            "order_code" => $result['order_code']
        ]);
    }

    public function updateOrder() {
        header("Content-Type: application/json");
        $userData = AuthController::authenticateToken();

        if ($userData['role'] !== 'admin' && $userData['role'] !== 'staff') {
            http_response_code(403);
            echo json_encode(["message" => "Permission denied. Exclusive to Admin & Staff."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data) || empty($data['order_id'])) {
            http_response_code(400);
            echo json_encode(["message" => "Order ID is required."]);
            return;
        }

        $orderId = (int)$data['order_id'];
        $orderStatus = isset($data['order_status']) ? trim($data['order_status']) : null;
        $paymentStatus = isset($data['payment_status']) ? trim($data['payment_status']) : null;
        $specialInstructions = isset($data['special_instructions']) ? trim($data['special_instructions']) : null;
        $servicesRegistered = isset($data['services_registered']) ? trim($data['services_registered']) : null;
        $weightKg = isset($data['weight_kg']) ? (float)$data['weight_kg'] : null;
        $totalPrice = isset($data['total_price']) ? (float)$data['total_price'] : null;

        $success = $this->orderModel->updateStatus($orderId, $orderStatus, $paymentStatus, $specialInstructions, $servicesRegistered, $weightKg, $totalPrice);

        if ($success) {
            http_response_code(200);
            echo json_encode([
                "message" => "Order updated successfully!",
                "order_status" => $orderStatus,
                "payment_status" => $paymentStatus
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "No changes made or order update failed."]);
        }
    }
}
?>
