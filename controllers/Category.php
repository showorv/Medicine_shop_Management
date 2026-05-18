<?php
require_once __DIR__ . "/../config/database.php";

class Category {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function all() {
        return $this->conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $type) {
        $stmt = $this->conn->prepare("INSERT INTO categories(name, category_type) VALUES(?,?)");
        return $stmt->execute([$name, $type]);
    }

    public function delete($id) {
        $check = $this->conn->prepare("SELECT COUNT(*) FROM medicines WHERE category_id=?");
        $check->execute([$id]);

        if($check->fetchColumn() > 0){
            return false;
        }

        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id=?");
        return $stmt->execute([$id]);
    }
}