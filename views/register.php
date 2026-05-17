<?php
require_once "../controllers/AuthController.php";

$controller = new AuthController();
$controller->register();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register – Medicine Shop</title>
    <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>

<div class="register-wrapper">

   
    <div class="login-brand">
        <div class="brand-icon"></div>
        <h1>Medicine Shop</h1>
        <p>Create your account to get started</p>
    </div>

   
    <div class="login-card">
        <div class="login-card-title">Create an Account</div>

    
        <?php if(isset($error)): ?>
            <div class="error-msg">⚠ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">

    
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input class="form-input" type="text" id="name"
                           name="name" placeholder="John Doe" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Account Type</label>
                    <select class="form-select" id="role" name="role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

   
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" type="email" id="email"
                       name="email" placeholder="you@example.com" required>
            </div>

        
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password"
                       name="password" placeholder="Create a strong password" required>
            </div>

            <hr class="form-divider">

          
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="phone">Phone</label>
                    <input class="form-input" type="text" id="phone"
                           name="phone" placeholder="01XXXXXXXXX">
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <input class="form-input" type="text" id="address"
                           name="address" placeholder="Your city">
                </div>
            </div>

            <button type="submit" class="btn-register">Create Account →</button>

        </form>
    </div>


    <div class="login-footer">
        Already have an account? <a href="login.php">Sign in here</a>
    </div>

</div>

</body>
</html>