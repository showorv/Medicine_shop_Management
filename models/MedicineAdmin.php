<?php
require_once __DIR__ . "/../config/database.php";

class MedicineAdmin {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->connect();
    }

    public function all() {
        return $this->conn->query("
            SELECT m.*, c.name as category 
            FROM medicines m 
            JOIN categories c ON m.category_id=c.id
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM medicines WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO medicines(name, category_id, vendor_name, price, availability, description, image_path)
            VALUES(?,?,?,?,?,?,?)
        ");

        return $stmt->execute([
            $data['name'],
            $data['category_id'],
            $data['vendor'],
            $data['price'],
            $data['stock'],
            $data['description'],
            $data['image']
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM medicines WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE medicines 
            SET name=?, category_id=?, vendor_name=?, price=?, availability=?, description=?, image_path=?
            WHERE id=?
        ");

        return $stmt->execute([
            $data['name'],
            $data['category_id'],
            $data['vendor'],
            $data['price'],
            $data['stock'],
            $data['description'],
            $data['image'],
            $id
        ]);
    }
    public function categories() {
        return $this->conn->query("SELECT * FROM categories")
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}