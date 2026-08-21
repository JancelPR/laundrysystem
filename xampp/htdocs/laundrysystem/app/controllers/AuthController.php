<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['identifier']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Please provide both identifier and password."]);
            return;
        }

        $user = $this->userModel->findByIdentifier(trim($data['identifier']));

        if (!$user) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials."]);
            return;
        }

        if (!password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials."]);
            return;
        }

        $tokenPayload = json_encode([
            "id"        => $user['id'],
            "full_name" => $user['full_name'],
            "phone"     => $user['phone'],
            "role"      => $user['role'],
            "iat"       => time()
        ]);
        $token = base64_encode($tokenPayload);

        $destination = "customer_dashboard.php";
        if ($user['role'] === 'admin') {
            $destination = "admin_dashboard.php";
        } elseif ($user['role'] === 'staff') {
            $destination = "staff_dashboard.php";
        }

        http_response_code(200);
        echo json_encode([
            "message"     => "Authentication successful!",
            "token"       => $token,
            "destination" => $destination,
            "user" => [
                "id"       => $user['id'],
                "fullName" => $user['full_name'],
                "role"     => $user['role']
            ]
        ]);
    }

    public function register() {
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['fullName']) || empty($data['phone']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Full name, phone, and password are required."]);
            return;
        }

        $fullName = trim($data['fullName']);
        $phone    = trim($data['phone']);
        $address  = isset($data['address']) ? trim($data['address']) : '';
        $password = $data['password'];

        $existing = $this->userModel->findByPhone($phone);
        if ($existing) {
            http_response_code(409);
            echo json_encode(["message" => "Phone number already registered."]);
            return;
        }

        $userId = $this->userModel->createCustomer($fullName, $phone, $address, $password);

        http_response_code(201);
        echo json_encode([
            "message" => "Customer account registered successfully!",
            "userId"  => $userId
        ]);
    }

    public static function authenticateToken() {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

        if (!$authHeader || strpos($authHeader, 'Bearer ') === false) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized access."]);
            exit();
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $userData = json_decode(base64_decode($token), true);

        if (!$userData || !isset($userData['id'])) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid session token."]);
            exit();
        }

        return $userData;
    }
}
?>
