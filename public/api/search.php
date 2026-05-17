<?php
header("Content-Type: application/json");

require_once "../../models/Medicine.php";

$medicine = new Medicine();

$q = $_GET['q'] ?? '';
$vendor = $_GET['vendor'] ?? '';
$genre = $_GET['genre'] ?? '';

$data = $medicine->search($q, $vendor, $genre);

echo json_encode($data);