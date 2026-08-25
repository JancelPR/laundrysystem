<?php
require_once __DIR__ . '/../config/database.php';

use Config\Database;

try {
    $pdo = Database::getConnection();

    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';

    $sql = "SELECT o.order_code, o.dropped_off_at, u.full_name, u.phone, 
                   o.services_registered, o.weight_kg, o.total_price, 
                   o.order_status, o.payment_status 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            WHERE 1=1";
    $params = [];

    if (!empty($startDate)) {
        $sql .= " AND DATE(o.dropped_off_at) >= :start_date";
        $params['start_date'] = $startDate;
    }
    if (!empty($endDate)) {
        $sql .= " AND DATE(o.dropped_off_at) <= :end_date";
        $params['end_date'] = $endDate;
    }
    if (!empty($status)) {
        $sql .= " AND o.order_status = :status";
        $params['status'] = $status;
    }

    $sql .= " ORDER BY o.dropped_off_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filename = "LaundryEase_Sales_Report_" . date('Ymd_His') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Headers
    fputcsv($output, [
        'Order Code',
        'Drop-Off Date & Time',
        'Customer Name',
        'Mobile Number',
        'Services Registered',
        'Weight (kg)',
        'Total Amount (PHP)',
        'Order Status',
        'Payment Status'
    ]);

    $totalRevenue = 0;
    $totalKg = 0;

    foreach ($rows as $row) {
        $totalRevenue += (float)$row['total_price'];
        $totalKg += (float)$row['weight_kg'];

        fputcsv($output, [
            $row['order_code'],
            date('Y-m-d H:i:s', strtotime($row['dropped_off_at'])),
            $row['full_name'],
            $row['phone'],
            $row['services_registered'],
            number_format((float)$row['weight_kg'], 2),
            number_format((float)$row['total_price'], 2),
            $row['order_status'],
            $row['payment_status']
        ]);
    }

    // Summary row
    fputcsv($output, []);
    fputcsv($output, [
        'SUMMARY TOTALS',
        'Total Orders: ' . count($rows),
        '',
        '',
        '',
        'Total Weight: ' . number_format($totalKg, 2) . ' kg',
        'Total Revenue: PHP ' . number_format($totalRevenue, 2),
        '',
        ''
    ]);

    fclose($output);
    exit;

} catch (\PDOException $e) {
    header("Content-Type: text/plain");
    echo "Export Error: " . $e->getMessage();
}
?>
