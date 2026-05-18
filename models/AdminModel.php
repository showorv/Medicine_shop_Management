<?php
require_once __DIR__ . "/../config/database.php";

class AdminModel {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

  
    public function customers() {
        return $this->conn->query("
            SELECT * FROM users WHERE role='customer'
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteCustomer($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id=? AND role='customer'");
        return $stmt->execute([$id]);
    }


    public function orders() {
        return $this->conn->query("
            SELECT o.*, u.name 
            FROM orders o 
            JOIN users u ON o.user_id = u.id
            ORDER BY o.id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status=? WHERE id=?");
        return $stmt->execute([$status, $id]);
    }
}