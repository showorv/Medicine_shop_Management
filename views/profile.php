<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

require_once "../models/User.php";

$userModel = new User();
$user = $userModel->getById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile – Medicine Shop</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>


<div class="navbar">
    <span>Medicine Shop</span>
    <div>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="../views/admin/dashboard.php">⚙ Admin</a>
        <?php endif; ?>
        <a href="../public/index.php">Home</a>
        <a href="cart.php">🛒 Cart</a>
        <a href="orders.php">My Orders</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">


    <div class="breadcrumb">
        <a href="../public/index.php">Home</a>
        <span>/</span>
        <span>Profile</span>
    </div>

    <h2 class="page-title">Your Profile</h2>

   
    <div class="profile-top">
        <div class="avatar-wrap">
            <?php if(!empty($user['profile_picture'])): ?>
                <img src="<?= htmlspecialchars($user['profile_picture']) ?>"
                     class="avatar-img"
                     alt="Profile Picture">
            <?php else: ?>
                <div class="avatar-placeholder">👤</div>
            <?php endif; ?>
        </div>
        <div class="profile-top-info">
            <h3><?= htmlspecialchars($user['name']) ?></h3>
            <p><?= htmlspecialchars($user['email']) ?></p>
        </div>
    </div>


    <div class="form-card">
        <div class="form-card-title">Edit Information</div>

        <form method="POST" action="../controllers/ProfileController.php" enctype="multipart/form-data">

            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input class="form-input" type="text" id="name" name="name"
                       value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-input" type="email" id="email" name="email"
                       value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input class="form-input" type="text" id="phone" name="phone"
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Delivery Address</label>
                <input class="form-input" type="text" id="address" name="address"
                       value="<?= htmlspecialchars($user['address'] ?? '') ?>">
            </div>

            <hr class="form-divider">

            <div class="form-group">
                <label class="form-label">Profile Picture</label>
                <label class="file-upload-label" for="profile_picture">
                    Click to choose a new photo
                </label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>

            <button type="submit" class="btn-submit">Save Changes</button>

        </form>
    </div>

</div>

</body>
</html>