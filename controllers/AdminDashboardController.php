<?php
require_once __DIR__ . '/../config/database.php';

class AdminDashboardController {

    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function index() {

        return [
            "users" => $this->db->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
            "medicines" => $this->db->query("SELECT COUNT(*) FROM medicines")->fetchColumn(),
            "categories" => $this->db->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
            "pending" => $this->db->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn()
        ];
    }
}