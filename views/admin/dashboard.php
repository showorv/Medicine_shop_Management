<?php
session_start();

require_once __DIR__ . '/../../controllers/AdminDashboardController.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /medicine_shop/views/login.php");
    exit;
}

$controller = new AdminDashboardController();
$data = $controller->index();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard Medicine Shop</title>
    <link rel="stylesheet" href="/medicine_shop/assets/css/index.css">
    <link rel="stylesheet" href="/medicine_shop/assets/css/admin_dashboard.css">
</head>
<body>


<div class="navbar">
    <div class="navbar-brand">
        <span>Medicine Shop</span>
        <span class="admin-badge">Admin</span>
    </div>
    <div>
        <a href="/medicine_shop/public/index.php">Home</a>
        <!-- <a href="customers.php">Customers</a>
        <a href="medicines.php">Medicines</a>
        <a href="catagories.php">Categories</a>
        <a href="orders.php">Orders</a> -->
        <a href="/medicine_shop/views/logout.php">Logout</a>
    </div>
</div>

<div class="container">

 
    <div class="welcome-bar">
        <div>
            <h2>Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?> </h2>
            <p>Here's what's happening in your store today.</p>
        </div>
        <div class="date"><?= date('l, d M Y') ?></div>
    </div>

   
    <div class="section-title">Overview</div>
    <div class="stats-grid">

        <div class="stat-card teal">
            <div class="stat-icon"></div>
            <div class="stat-label">Total Customers</div>
            <div class="stat-number"><?= (int)$data['users'] ?></div>
            <a href="customers.php" class="stat-link">Manage →</a>
        </div>

        <div class="stat-card amber">
            <div class="stat-icon"></div>
            <div class="stat-label">Total Medicines</div>
            <div class="stat-number"><?= (int)$data['medicines'] ?></div>
            <a href="medicines.php" class="stat-link">Manage →</a>
        </div>

        <div class="stat-card blue">
            <div class="stat-icon"></div>
            <div class="stat-label">Categories</div>
            <div class="stat-number"><?= (int)$data['categories'] ?></div>
            <a href="catagories.php" class="stat-link">Manage →</a>
        </div>

        <div class="stat-card red">
            <div class="stat-icon"></div>
            <div class="stat-label">Pending Orders</div>
            <div class="stat-number"><?= (int)$data['pending'] ?></div>
            <a href="orders.php" class="stat-link">View →</a>
        </div>

    </div>

   
    <div class="section-title">Quick Actions</div>
    <div class="quick-grid">

        <a href="customers.php" class="quick-card">
            <div class="q-icon teal"></div>
            <div>
                Customers
                <div class="q-sub">View & manage users</div>
            </div>
        </a>

        <a href="medicines.php" class="quick-card">
            <div class="q-icon amber"></div>
            <div>
                Medicines
                <div class="q-sub">Add, edit, remove</div>
            </div>
        </a>

        <a href="catagories.php" class="quick-card">
            <div class="q-icon blue"></div>
            <div>
                Categories
                <div class="q-sub">Organise medicines</div>
            </div>
        </a>

        <a href="orders.php" class="quick-card">
            <div class="q-icon red"></div>
            <div>
                Orders
                <div class="q-sub">Track & update orders</div>
            </div>
        </a>

    </div>

</div>

</body>
</html>