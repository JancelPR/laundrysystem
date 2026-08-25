<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");

require_once __DIR__ . '/../config/database.php';

use Config\Database;

try {
    $pdo = Database::getConnection();

    $query = isset($_GET['q']) ? trim($_GET['q']) : (isset($_GET['code']) ? trim($_GET['code']) : (isset($_GET['query']) ? trim($_GET['query']) : ''));
    if (empty($query)) {
        $input = json_decode(file_get_contents("php://input"), true);
        if ($input) {
            $query = !empty($input['q']) ? trim($input['q']) : (!empty($input['code']) ? trim($input['code']) : (!empty($input['query']) ? trim($input['query']) : ''));
        }
    }

    if (empty($query)) {
        echo json_encode(["status" => "error", "message" => "Please provide an Order Code or Mobile Number."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT o.*, u.full_name, u.phone 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.order_code = :query1 OR u.phone = :query2 
                           ORDER BY o.dropped_off_at DESC LIMIT 5");
    $stmt->execute(['query1' => $query, 'query2' => $query]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$orders || count($orders) === 0) {
        echo json_encode(["status" => "error", "message" => "No active or past laundry orders found matching '" . htmlspecialchars($query) . "'."]);
        exit;
    }

    // Mask sensitive details for public privacy
    $cleaned = [];
    foreach ($orders as $order) {
        $rawName = $order['full_name'];
        $maskedName = mb_substr($rawName, 0, 1) . str_repeat('*', max(1, mb_strlen($rawName) - 2)) . mb_substr($rawName, -1);
        
        $rawPhone = $order['phone'];
        $maskedPhone = substr($rawPhone, 0, 3) . '****' . substr($rawPhone, -3);

        // Map status to progress step index (0-5)
        $statusMap = [
            'pending' => 0,
            'received' => 0,
            'queued' => 0,
            'washing' => 1,
            'in washing' => 1,
            'wash' => 1,
            'drying' => 2,
            'in drying' => 2,
            'dry' => 2,
            'folding' => 3,
            'in folding' => 3,
            'fold' => 3,
            'ready' => 4,
            'ready for pickup' => 4,
            'ready to pickup' => 4,
            'completed' => 5,
            'delivered' => 5,
            'claimed' => 5
        ];

        $currentStatusLower = strtolower(trim($order['order_status']));
        $stepIndex = isset($statusMap[$currentStatusLower]) ? $statusMap[$currentStatusLower] : 1;

        $cleaned[] = [
            "order_code" => $order['order_code'],
            "customer_name" => $maskedName,
            "customer_phone" => $maskedPhone,
            "services" => $order['services_registered'],
            "weight_kg" => $order['weight_kg'],
            "total_price" => $order['total_price'],
            "order_status" => $order['order_status'],
            "payment_status" => $order['payment_status'],
            "dropped_off_at" => $order['dropped_off_at'],
            "step_index" => $stepIndex
        ];
    }

    echo json_encode(["status" => "success", "data" => $cleaned]);
} catch (\PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
