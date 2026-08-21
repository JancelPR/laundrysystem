<?php
namespace App\Models;

use Config\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByIdentifier($identifier) {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE LOWER(identifier) = LOWER(:id) 
               OR phone = :phone 
               OR LOWER(full_name) = LOWER(:fullname) 
            LIMIT 1
        ");
        $stmt->execute([
            'id'       => $identifier,
            'phone'    => $identifier,
            'fullname' => $identifier
        ]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByPhone($phone) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = :phone LIMIT 1");
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch();
    }

    public function createCustomer($fullName, $phone, $address = '', $password = 'Password123!') {
        $baseIdentifier = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fullName));
        if (empty($baseIdentifier)) {
            $baseIdentifier = 'client' . substr(preg_replace('/[^0-9]/', '', $phone), -4);
        }

        $identifier = $baseIdentifier;
        $counter = 1;
        while ($this->findByIdentifierOnly($identifier)) {
            $identifier = $baseIdentifier . $counter;
            $counter++;
        }

        $hashPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("
            INSERT INTO users (identifier, full_name, phone, address, password, role)
            VALUES (:identifier, :full_name, :phone, :address, :password, 'customer')
        ");
        $stmt->execute([
            'identifier' => $identifier,
            'full_name'  => $fullName,
            'phone'      => $phone,
            'address'    => $address,
            'password'   => $hashPassword
        ]);
        return $this->db->lastInsertId();
    }

    public function findByIdentifierOnly($identifier) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE LOWER(identifier) = LOWER(:id) LIMIT 1");
        $stmt->execute(['id' => $identifier]);
        return $stmt->fetch();
    }

    public function getCustomers($search = '') {
        if (!empty($search)) {
            $stmt = $this->db->prepare("
                SELECT id, identifier, full_name, phone, address, created_at 
                FROM users 
                WHERE role = 'customer' 
                  AND (full_name LIKE :search OR phone LIKE :search OR identifier LIKE :search)
                ORDER BY full_name ASC
            ");
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $stmt = $this->db->prepare("
                SELECT id, identifier, full_name, phone, address, created_at 
                FROM users 
                WHERE role = 'customer' 
                ORDER BY full_name ASC
            ");
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    public function getByRole($role) {
        $stmt = $this->db->prepare("
            SELECT id, identifier, full_name, phone, address, role, created_at 
            FROM users 
            WHERE role = :role 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll();
    }

    public function updateCustomer($id, $fullName, $phone, $address) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET full_name = :full_name, phone = :phone, address = :address 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'        => $id,
            'full_name' => $fullName,
            'phone'     => $phone,
            'address'   => $address
        ]);
    }

    public function getTotalCustomerCount() {
        $stmt = $this->db->query("SELECT COUNT(*) AS count FROM users WHERE role = 'customer'");
        return (int)($stmt->fetch()['count'] ?? 0);
    }
}
?>
