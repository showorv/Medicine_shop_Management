<?php
require_once "../controllers/AuthController.php";

$controller = new AuthController();
$controller->login();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login – Medicine Shop</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

<div class="login-wrapper">

    
    <div class="login-brand">
        <div class="brand-icon"></div>
        <h1>Medicine Shop</h1>
        <p>Your trusted online pharmacy</p>
    </div>

  
    <div class="login-card">
        <div class="login-card-title">Sign In to Your Account</div>

    
        <?php if(isset($error)): ?>
            <div class="error-msg">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" type="email" id="email"
                       name="email" placeholder="you@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password"
                       name="password" placeholder="Enter your password" required>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember Me</label>
            </div>

            <button type="submit" class="btn-login">Login →</button>

        </form>
    </div>

 
    <div class="login-footer">
        Don't have an account? <a href="register.php">Register here</a>
    </div>

</div>

</body>
</html>