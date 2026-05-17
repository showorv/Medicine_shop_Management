<?php
require_once __DIR__ . '/../config/database.php';

class Medicine {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll() {
        $sql = "SELECT m.*, c.name as category_name, c.category_type 
                FROM medicines m
                JOIN categories c ON m.category_id = c.id";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $sql = "SELECT * FROM categories";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function search($q, $vendor, $genre) {
        $sql = "SELECT m.*, c.name as category_name 
                FROM medicines m
                JOIN categories c ON m.category_id = c.id
                WHERE m.name LIKE :q";

        if ($vendor) {
            $sql .= " AND m.vendor_name = :vendor";
        }

        if ($genre) {
            $sql .= " AND c.name = :genre";
        }

        $stmt = $this->conn->prepare($sql);

        $params = [':q' => "%$q%"];

        if ($vendor) $params[':vendor'] = $vendor;
        if ($genre) $params[':genre'] = $genre;

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}