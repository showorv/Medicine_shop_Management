<?php
session_start();
require_once "../../config/database.php";

header("Content-Type: application/json");

if(!isset($_SESSION['user_id'])){
    echo json_encode(["error" => "login required"]);
    exit();
}

$db = (new Database())->connect();

$data = json_decode(file_get_contents("php://input"), true);

$uid = $_SESSION['user_id'];
$mid = $data['medicine_id'];
$qty = (int)$data['quantity'];


if($qty <= 0){
    echo json_encode(["error" => "Invalid quantity"]);
    exit();
}


$stmt = $db->prepare("SELECT availability FROM medicines WHERE id=?");
$stmt->execute([$mid]);
$med = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$med){
    echo json_encode(["error" => "Medicine not found"]);
    exit();
}

$stock = (int)$med['availability'];


$stmt = $db->prepare("SELECT quantity FROM cart WHERE user_id=? AND medicine_id=?");
$stmt->execute([$uid, $mid]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

$currentQty = $existing ? $existing['quantity'] : 0;

if(($currentQty + $qty) > $stock){
    echo json_encode(["error" => "Not enough stock available"]);
    exit();
}


if($existing){
    $db->prepare("
        UPDATE cart 
        SET quantity = quantity + ? 
        WHERE user_id=? AND medicine_id=?
    ")->execute([$qty, $uid, $mid]);
} else {
    $db->prepare("
        INSERT INTO cart(user_id, medicine_id, quantity)
        VALUES(?,?,?)
    ")->execute([$uid, $mid, $qty]);
}

echo json_encode(["success" => true]);