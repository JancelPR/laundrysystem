<?php
// One-click Database Setup & Temporary Credential Generator for Athena Laundry System

$host = "localhost";
$username = "root";
$password = "";
$dbname = "athena_laundry";

$statusMessages = [];
$success = false;

try {
    // 1. Connect without DB selected to ensure DB exists
    $pdoRoot = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $statusMessages[] = ["type" => "success", "msg" => "Database '$dbname' verified / created."];

    // 2. Connect to athena_laundry
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // 3. Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `identifier` VARCHAR(100) NOT NULL UNIQUE,
          `full_name` VARCHAR(150) NOT NULL,
          `phone` VARCHAR(30) NOT NULL UNIQUE,
          `address` TEXT NULL,
          `password` VARCHAR(255) NOT NULL,
          `role` ENUM('customer', 'staff', 'admin') NOT NULL DEFAULT 'customer',
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    $statusMessages[] = ["type" => "success", "msg" => "Table 'users' verified / created."];

    // 4. Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `orders` (
          `id` INT AUTO_INCREMENT PRIMARY KEY,
          `user_id` INT NOT NULL,
          `order_code` VARCHAR(50) NOT NULL UNIQUE,
          `services_registered` TEXT NOT NULL,
          `weight_kg` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
          `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
          `special_instructions` TEXT NULL,
          `order_status` VARCHAR(50) NOT NULL DEFAULT 'Pending',
          `payment_status` VARCHAR(50) NOT NULL DEFAULT 'Unpaid',
          `dropped_off_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    $statusMessages[] = ["type" => "success", "msg" => "Table 'orders' verified / created."];

    // 5. Seed Temporary Accounts
    $defaultAccounts = [
        [
            'full_name'  => 'Jane Doe',
            'phone'      => '09123456789',
            'address'    => '123 Sampaguita St, Quezon City',
            'password'   => 'Password123!',
            'role'       => 'customer'
        ],
        [
            'full_name'  => 'John Smith',
            'phone'      => '09187654321',
            'address'    => '456 Acacia Ave, Makati City',
            'password'   => 'Password123!',
            'role'       => 'customer'
        ],
        [
            'full_name'  => 'Staff Washer',
            'phone'      => '09554443333',
            'address'    => 'Athena Laundry Counter 1',
            'password'   => 'Password123!',
            'role'       => 'staff'
        ],
        [
            'full_name'  => 'Admin Manager',
            'phone'      => '09998887777',
            'address'    => 'Athena Laundry Main HQ',
            'password'   => 'Password123!',
            'role'       => 'admin'
        ]
    ];

    $userStmt = $pdo->prepare("
        INSERT INTO users (identifier, full_name, phone, address, password, role)
        VALUES (:identifier, :full_name, :phone, :address, :password, :role)
        ON DUPLICATE KEY UPDATE 
            full_name = VALUES(full_name), 
            password = VALUES(password),
            address = VALUES(address)
    ");

    foreach ($defaultAccounts as $acc) {
        $identifier = strtolower(str_replace(' ', '', $acc['full_name']));
        $hashPassword = password_hash($acc['password'], PASSWORD_BCRYPT);

        $userStmt->execute([
            'identifier' => $identifier,
            'full_name'  => $acc['full_name'],
            'phone'      => $acc['phone'],
            'address'    => $acc['address'],
            'password'   => $hashPassword,
            'role'       => $acc['role']
        ]);
    }
    $statusMessages[] = ["type" => "success", "msg" => "Temporary accounts created / updated."];

    // 6. Seed Sample Orders for Jane Doe (user_id = 1)
    $janeStmt = $pdo->prepare("SELECT id FROM users WHERE phone = '09123456789' LIMIT 1");
    $janeStmt->execute();
    $janeUser = $janeStmt->fetch();

    if ($janeUser) {
        $janeId = $janeUser['id'];

        $orderStmt = $pdo->prepare("
            INSERT INTO orders (user_id, order_code, services_registered, weight_kg, total_price, special_instructions, order_status, payment_status, dropped_off_at)
            VALUES (:user_id, :order_code, :services_registered, :weight_kg, :total_price, :special_instructions, :order_status, :payment_status, NOW())
            ON DUPLICATE KEY UPDATE order_status = VALUES(order_status), payment_status = VALUES(payment_status)
        ");

        $orderStmt->execute([
            'user_id'              => $janeId,
            'order_code'           => 'ATH-8801',
            'services_registered'  => 'Wash & Fold, Fabric Softener',
            'weight_kg'            => 6.5,
            'total_price'          => 280.00,
            'special_instructions' => "Separate whites from darks.\nExtra softener requested.",
            'order_status'         => 'Washing',
            'payment_status'       => 'Paid'
        ]);

        $orderStmt->execute([
            'user_id'              => $janeId,
            'order_code'           => 'ATH-8794',
            'services_registered'  => 'Dry Cleaning & Steam Press',
            'weight_kg'            => 3.0,
            'total_price'          => 450.00,
            'special_instructions' => 'Handle silk dress with care.',
            'order_status'         => 'Ready for Pickup',
            'payment_status'       => 'Unpaid'
        ]);

        $statusMessages[] = ["type" => "success", "msg" => "Sample laundry orders seeded for customer 'Jane Doe'."];
    }

    $success = true;

} catch (PDOException $e) {
    $statusMessages[] = ["type" => "error", "msg" => "Database Setup Failed: " . $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Setup & Temporary Credentials — Athena Laundry</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #090d16;
      --card: #131b2e;
      --card-border: #1e293b;
      --text: #f8fafc;
      --text-muted: #94a3b8;
      --accent: #38bdf8;
      --green: #4ade80;
      --red: #f87171;
      --font-title: 'Sora', sans-serif;
      --font-body: 'Inter', sans-serif;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background-color: var(--bg);
      color: var(--text);
      font-family: var(--font-body);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }
    .container {
      max-width: 800px;
      width: 100%;
    }
    .card {
      background: var(--card);
      border: 1px solid var(--card-border);
      border-radius: 20px;
      padding: 36px;
      box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    h1 { font-family: var(--font-title); font-size: 1.8rem; margin-bottom: 8px; color: var(--text); }
    p.sub { color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px; }
    
    .status-box {
      background: rgba(15, 23, 42, 0.6);
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 28px;
    }
    .status-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.9rem;
      margin-bottom: 8px;
    }
    .status-item:last-child { margin-bottom: 0; }
    .badge-success { color: var(--green); font-weight: 600; }
    .badge-error { color: var(--red); font-weight: 600; }

    .section-title {
      font-family: var(--font-title);
      font-size: 1.1rem;
      color: var(--accent);
      margin-bottom: 16px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 28px;
    }
    th, td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid var(--card-border);
      font-size: 0.9rem;
    }
    th {
      color: var(--text-muted);
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
    }
    td code {
      background: rgba(56, 189, 248, 0.1);
      color: var(--accent);
      padding: 3px 8px;
      border-radius: 6px;
      font-size: 0.85rem;
    }

    .btn {
      display: inline-block;
      background: #0284c7;
      color: #ffffff;
      font-weight: 600;
      text-decoration: none;
      padding: 14px 28px;
      border-radius: 12px;
      transition: all 0.2s ease;
    }
    .btn:hover {
      background: #0369a1;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Athena Laundry — Database & Credentials Setup</h1>
      <p class="sub">Automated database initialization and temporary demo credential generator.</p>

      <div class="status-box">
        <?php foreach ($statusMessages as $msg): ?>
          <div class="status-item">
            <span class="<?= $msg['type'] === 'success' ? 'badge-success' : 'badge-error' ?>">
              <?= $msg['type'] === 'success' ? '✓' : '✗' ?>
            </span>
            <span><?= htmlspecialchars($msg['msg']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($success): ?>
        <div class="section-title">🔑 Temporary Demo Accounts</div>
        <table>
          <thead>
            <tr>
              <th>Role</th>
              <th>Full Name</th>
              <th>Login Identifier / Phone</th>
              <th>Password</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong style="color:#4ade80;">Customer</strong></td>
              <td>Jane Doe</td>
              <td><code>janedoe</code> or <code>09123456789</code></td>
              <td><code>Password123!</code></td>
            </tr>
            <tr>
              <td><strong style="color:#4ade80;">Customer</strong></td>
              <td>John Smith</td>
              <td><code>johnsmith</code> or <code>09187654321</code></td>
              <td><code>Password123!</code></td>
            </tr>
            <tr>
              <td><strong style="color:#f59e0b;">Staff</strong></td>
              <td>Staff Washer</td>
              <td><code>staffwasher</code> or <code>09554443333</code></td>
              <td><code>Password123!</code></td>
            </tr>
            <tr>
              <td><strong style="color:#38bdf8;">Admin</strong></td>
              <td>Admin Manager</td>
              <td><code>adminmanager</code> or <code>09998887777</code></td>
              <td><code>Password123!</code></td>
            </tr>
          </tbody>
        </table>

        <div style="text-align: right;">
          <a href="index.html" class="btn">Go to Login Page →</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
