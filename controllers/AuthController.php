<?php
session_start();
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if(strlen($_POST['password']) < 8){
                echo "Password must be at least 8 characters";
                return;
            }

            $this->userModel->register($_POST);

            header("Location: /medicine_shop/views/login.php");
            exit();
        }
    }

    public function login() {


        if(isset($_COOKIE['remember_token'])) {

            $user = $this->userModel->findByToken($_COOKIE['remember_token']);

            if($user){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                header("Location: /medicine_shop/public/index.php");
                exit();
            }
        }

 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $user = $this->userModel->login($_POST['email'], $_POST['password']);

            if ($user) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

  
                if(isset($_POST['remember'])) {

                    $token = bin2hex(random_bytes(32));

                    setcookie("remember_token", $token, time() + (86400 * 7), "/");

                    $this->userModel->saveToken($user['id'], $token);
                }

                if ($user['role'] === 'admin') {
                    header("Location: /medicine_shop/views/admin/dashboard.php");
                } else {
                    header("Location: /medicine_shop/public/index.php");
                }
                exit();

            } else {
                echo "Invalid credentials";
            }
        }
    }

    public function logout() {
        session_destroy();
        setcookie("remember_token", "", time() - 3600, "/");
        header("Location: /medicine_shop/views/login.php");
        exit();
    }
}