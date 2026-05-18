<?php
session_start();
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || !isset($_SESSION['shipping_address'])){
    header("Location: index.php"); exit();
}

$db  = (new Database())->connect();
$uid = $_SESSION['user_id'];

/* Get cart items */
$stmt = $db->prepare("
    SELECT c.*, m.price
    FROM cart c
    JOIN medicines m ON c.medicine_id = m.id
    WHERE c.user_id = ?
");
$stmt->execute([$uid]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Calculate total */
$total = 0;
foreach($items as $i){
    $total += $i['price'] * $i['quantity'];
}

/* Handle payment method submit */
if(isset($_POST['method'])){

    $method = $_POST['method'];

    /* Insert order */
    $db->prepare("
        INSERT INTO orders(user_id, total_amount, shipping_address, status, payment_method, order_date)
        VALUES(?, ?, ?, ?, ?, NOW())
    ")->execute([$uid, $total, $_SESSION['shipping_address'], 'pending', $method]);

    $order_id = $db->lastInsertId();

    /* Insert order items */
    foreach($items as $i){
        $db->prepare("
            INSERT INTO order_items(order_id, medicine_id, quantity, unit_price)
            VALUES(?, ?, ?, ?)
        ")->execute([$order_id, $i['medicine_id'], $i['quantity'], $i['price']]);
    }

    /* Insert payment */
    $db->prepare("
        INSERT INTO payments(order_id, amount, payment_method, transaction_id, payment_date)
        VALUES(?, ?, ?, ?, NOW())
    ")->execute([$order_id, $total, $method, uniqid()]);

    /* Clear cart */
    $db->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$uid]);

    unset($_SESSION['shipping_address']);

    header("Location: orders_success.php");
    exit();
}
?>

<link rel="stylesheet" href="../assets/css/index.css">
<link rel="stylesheet" href="../assets/css/payment.css">


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
        <a href="checkout.php">Checkout</a>
        <span>/</span>
        <a href="invoice.php">Invoice</a>
        <span>/</span>
        <span>Payment</span>
    </div>

    <h2 class="page-title">Select Payment</h2>
    <p class="page-subtitle">Choose how you'd like to pay for your order.</p>


    <div class="amount-box">
        <div>
            <p>Total Amount Due</p>
            <span>৳ <?= $total ?></span>
        </div>
        <div class="lock-icon">🔒</div>
    </div>

   
    <div class="payment-card">
        <div class="card-title">Payment Method</div>

        <form method="POST">

           
            <button class="method-btn bkash" name="method" value="bkash" type="submit">
                <div class="method-icon">💳</div>
                <div class="method-label">
                    bKash
                    <div class="method-sub">Mobile banking payment</div>
                </div>
                <span class="arrow">›</span>
            </button>

           
            <button class="method-btn nagad" name="method" value="nagad" type="submit">
                <div class="method-icon">📱</div>
                <div class="method-label">
                    Nagad
                    <div class="method-sub">Mobile banking payment</div>
                </div>
                <span class="arrow">›</span>
            </button>

          
            <button class="method-btn cod" name="method" value="cod" type="submit">
                <div class="method-icon">💵</div>
                <div class="method-label">
                    Cash on Delivery
                    <div class="method-sub">Pay when your order arrives</div>
                </div>
                <span class="arrow">›</span>
            </button>

        </form>
    </div>

    <a href="invoice.php" class="back-link">← Back to Invoice</a>

</div>