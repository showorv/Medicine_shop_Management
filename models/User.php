<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function register($data) {
        $sql = "INSERT INTO users (name, email, password_hash, role, address, phone)
                VALUES (:name, :email, :password, :role, :address, :phone)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => htmlspecialchars($data['name']),
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'],
            ':address' => $data['address'],
            ':phone' => $data['phone']
        ]);
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return false;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateProfile($data) {
        $sql = "UPDATE users 
                SET name=?, email=?, address=?, phone=?, profile_picture=?
                WHERE id=?";
    
        $stmt = $this->conn->prepare($sql);
    
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['address'],
            $data['phone'],
            $data['profile_picture'],
            $data['id']
        ]);
    }
    
    public function updatePassword($id, $newPassword) {
        $sql = "UPDATE users SET password_hash=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);
    
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $id
        ]);
    }

    public function findByToken($token){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE remember_token=?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function saveToken($id, $token){
        $stmt = $this->conn->prepare("UPDATE users SET remember_token=? WHERE id=?");
        return $stmt->execute([$token, $id]);
    }
}

