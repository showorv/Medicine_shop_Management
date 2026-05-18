<?php
session_start();
require_once "../config/database.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php"); exit();
}

$db  = (new Database())->connect();
$uid = $_SESSION['user_id'];


$stmt = $db->prepare("
    SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC
");
$stmt->execute([$uid]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/css/index.css">
<link rel="stylesheet" href="../assets/css/orders_success.css">


<div class="navbar">
    <span>Medicine Shop</span>
    <div>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="../views/admin/dashboard.php">⚙ Admin</a>
        <?php endif; ?>
        <a href="../views/profile.php">Profile</a>
        <a href="../views/cart.php">🛒 Cart</a>
        <a href="../views/logout.php">Logout</a>
    </div>
</div>

<div class="container">


    <div class="breadcrumb">
        <a href="../public/index.php">Home</a>
        <span>/</span>
        <span>My Orders</span>
    </div>

    <h2 class="page-title">My Orders</h2>
    <p class="page-subtitle">
        <?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?> found
    </p>

    <?php if(empty($orders)): ?>

   
        <div class="empty-state">
            <p>📦 You haven't placed any orders yet.</p>
            <a href="../public/index.php" class="btn-shop">Browse Medicines</a>
        </div>

    <?php else: ?>

        <?php foreach($orders as $o): ?>

        <?php
      
            $status = strtolower($o['status']);
            if($status === 'pending')   $badge = 'status-pending';
            elseif($status === 'confirmed') $badge = 'status-confirmed';
            elseif($status === 'delivered') $badge = 'status-delivered';
            elseif($status === 'cancelled') $badge = 'status-cancelled';
            else $badge = 'status-pending';

           
            $date = isset($o['order_date'])
                ? date('d M Y, h:i A', strtotime($o['order_date']))
                : '—';
        ?>

        <div class="order-card">

          
            <div class="order-header">
                <div>
                    <div class="order-id">Order #<?= $o['id'] ?></div>
                    <div class="order-date">Placed on: <?= $date ?></div>
                </div>
                <div class="order-header-right">
                    <span class="status-badge <?= $badge ?>"><?= htmlspecialchars($o['status']) ?></span>
                </div>
            </div>

            
            <div class="order-body">
                <div class="order-info">

                    <div class="info-row">
                        <span class="info-label">Total Amount</span>
                        <span class="info-value total">৳ <?= htmlspecialchars($o['total_amount']) ?></span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Payment</span>
                        <span class="info-value">
                            <span class="payment-badge"><?= htmlspecialchars($o['payment_method']) ?></span>
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label">Ship To</span>
                        <span class="info-value"><?= htmlspecialchars($o['shipping_address']) ?></span>
                    </div>

                </div>
            </div>

        </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>