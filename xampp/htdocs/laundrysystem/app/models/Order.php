<?php
namespace App\Models;

use Config\Database;
use PDO;

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllWithCustomers() {
        $stmt = $this->db->query("
            SELECT 
                o.*, 
                u.full_name AS customer_name, 
                u.phone AS customer_phone 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.dropped_off_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                o.*, 
                u.full_name AS customer_name, 
                u.phone AS customer_phone 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE o.user_id = :user_id 
            ORDER BY o.dropped_off_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function create($userId, $servicesRegistered, $weightKg, $totalPrice, $specialInstructions = '', $paymentStatus = 'Unpaid', $orderStatus = 'Pending') {
        $orderCode = 'ATH-' . rand(1000, 9999);
        
        $codeCheck = $this->db->prepare("SELECT id FROM orders WHERE order_code = :code");
        $codeCheck->execute(['code' => $orderCode]);
        while ($codeCheck->fetch()) {
            $orderCode = 'ATH-' . rand(1000, 9999);
            $codeCheck->execute(['code' => $orderCode]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO orders 
            (user_id, order_code, services_registered, weight_kg, total_price, special_instructions, order_status, payment_status, dropped_off_at)
            VALUES 
            (:user_id, :order_code, :services_registered, :weight_kg, :total_price, :special_instructions, :order_status, :payment_status, NOW())
        ");

        $stmt->execute([
            'user_id'             => $userId,
            'order_code'          => $orderCode,
            'services_registered' => $servicesRegistered,
            'weight_kg'           => $weightKg,
            'total_price'         => $totalPrice,
            'special_instructions'=> $specialInstructions,
            'order_status'        => $orderStatus,
            'payment_status'      => $paymentStatus
        ]);

        return [
            'id' => $this->db->lastInsertId(),
            'order_code' => $orderCode
        ];
    }

    public function updateStatus($orderId, $orderStatus = null, $paymentStatus = null, $specialInstructions = null, $servicesRegistered = null, $weightKg = null, $totalPrice = null) {
        $fields = [];
        $params = [];

        if ($orderStatus !== null) {
            $fields[] = "order_status = :order_status";
            $params['order_status'] = $orderStatus;
        }

        if ($paymentStatus !== null) {
            $fields[] = "payment_status = :payment_status";
            $params['payment_status'] = $paymentStatus;
        }

        if ($specialInstructions !== null) {
            $fields[] = "special_instructions = :special_instructions";
            $params['special_instructions'] = $specialInstructions;
        }

        if ($servicesRegistered !== null) {
            $fields[] = "services_registered = :services_registered";
            $params['services_registered'] = $servicesRegistered;
        }

        if ($weightKg !== null) {
            $fields[] = "weight_kg = :weight_kg";
            $params['weight_kg'] = $weightKg;
        }

        if ($totalPrice !== null) {
            $fields[] = "total_price = :total_price";
            $params['total_price'] = $totalPrice;
        }

        if (empty($fields)) return false;

        $query = "UPDATE orders SET " . implode(", ", $fields) . " WHERE id = :id";
        $params['id'] = $orderId;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function getMetrics() {
        $totalRevenueStmt = $this->db->query("SELECT SUM(total_price) AS total_revenue FROM orders");
        $totalRevenue = $totalRevenueStmt->fetch()['total_revenue'] ?? 0;

        $totalOrdersStmt = $this->db->query("SELECT COUNT(*) AS total_orders FROM orders");
        $totalOrders = $totalOrdersStmt->fetch()['total_orders'] ?? 0;

        return [
            'totalRevenue' => (float)$totalRevenue,
            'totalOrders'  => (int)$totalOrders
        ];
    }
}
?>
