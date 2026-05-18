<?php
require_once "../../config/database.php";

$data = json_decode(file_get_contents("php://input"), true);

$db = (new Database())->connect();

$stmt = $db->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->execute([$data['status'], $data['id']]);