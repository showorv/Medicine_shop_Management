

<?php
session_start();

require_once "../models/User.php";

$user = new User();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $imageName = $_FILES['profile_picture']['name'];
    $tmp = $_FILES['profile_picture']['tmp_name'];

  
    $path = "../public/uploads/profiles/" . time() . "_" . $imageName;

    move_uploaded_file($tmp, "../public/" . $path);

    $data = [
        "id" => $_SESSION['user_id'],
        "name" => $_POST['name'],
        "email" => $_POST['email'],
        "address" => $_POST['address'],
        "phone" => $_POST['phone'],
        "profile_picture" => $path
    ];

    $user->updateProfile($data);

    header("Location: ../views/profile.php");
    exit();
}