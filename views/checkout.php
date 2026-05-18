<?php
session_start();
require_once "../config/database.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php"); exit();
}

$db  = (new Database())->connect();
$uid = $_SESSION['user_id'];


$stmt = $db->prepare("SELECT address FROM users WHERE id=?");
$stmt->execute([$uid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT c.*, m.name, m.price
    FROM cart c
    JOIN medicines m ON c.medicine_id = m.id
    WHERE c.user_id = ?
");
$stmt->execute([$uid]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


if(empty($items)){
    echo "
    <div style='text-align:center; margin-top:50px;'>
        <h2>Your cart is empty 🛒</h2>
        <a href='../public/index.php'>Continue Shopping</a>
    </div>";
    exit();
}


$total = 0;
foreach($items as $i){
    $total += $i['price'] * $i['quantity'];
}


if(isset($_POST['address'])){
    $_SESSION['shipping_address'] = $_POST['address'];
    header("Location: invoice.php");
    exit();
}
?>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/checkout.css">


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
        <a href="cart.php">Cart</a>
        <span>/</span>
        <span>Checkout</span>
    </div>

    <h2 class="page-title">Checkout</h2>

    <form method="POST">
    <div class="checkout-layout">

    
        <div class="checkout-form-box">
            <div class="box-title">Shipping Address</div>

            <label class="form-label" for="address">Delivery Address</label>
            <textarea
                id="address"
                name="address"
                class="form-input"
                rows="4"
                placeholder="Enter your full shipping address…"
                required
            ><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>

   
        <div class="order-summary-box">
            <div class="box-title">🧾 Order Summary</div>

            <?php foreach($items as $i): ?>
            <div class="summary-item">
                <div>
                    <div class="item-name"><?= htmlspecialchars($i['name']) ?></div>
                    <div class="item-qty">Qty: <?= $i['quantity'] ?></div>
                </div>
                <div class="item-price">৳ <?= $i['price'] * $i['quantity'] ?></div>
            </div>
            <?php endforeach; ?>

            <hr class="summary-divider">

            <div class="summary-total">
                <span>Total</span>
                <span>৳ <?= $total ?></span>
            </div>

            <button type="submit" class="btn-submit">Continue to Invoice →</button>
            <a href="cart.php" class="btn-back">← Back to Cart</a>
        </div>

    </div>
    </form>

</div>