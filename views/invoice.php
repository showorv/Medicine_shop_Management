<?php
session_start();
require_once "../config/database.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php"); exit();
}

if(!isset($_SESSION['shipping_address'])){
    header("Location: checkout.php"); exit();
}

$db  = (new Database())->connect();
$uid = $_SESSION['user_id'];


$stmt = $db->prepare("
    SELECT c.*, m.name, m.price
    FROM cart c
    JOIN medicines m ON c.medicine_id = m.id
    WHERE c.user_id = ?
");
$stmt->execute([$uid]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total = 0;
foreach($items as $i){
    $total += $i['price'] * $i['quantity'];
}


$invoice_no  = 'INV-' . strtoupper(substr(md5($uid . time()), 0, 8));
$invoice_date = date('d M Y');
?>

<link rel="stylesheet" href="../assets/css/index.css">
<link rel="stylesheet" href="../assets/css/invoice.css">


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
        <span>Invoice</span>
    </div>

    <h2 class="page-title">Invoice</h2>
    <p class="page-subtitle">Please review your order before confirming.</p>

 
    <div class="invoice-card">

   
        <div class="invoice-header">
            <div class="invoice-header-left">
                <h3>Medicine Shop</h3>
                <p>Order Confirmation</p>
            </div>
            <div class="invoice-header-right">
                <p>Invoice No.</p>
                <span><?= $invoice_no ?></span>
                <p style="margin-top:6px;">Date</p>
                <span><?= $invoice_date ?></span>
            </div>
        </div>

     
        <div class="shipping-row">
            <span class="shipping-label">Ship To:</span>
            <span class="shipping-address"><?= htmlspecialchars($_SESSION['shipping_address']) ?></span>
        </div>


        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $i): ?>
                <tr>
                    <td class="med-name"><?= htmlspecialchars($i['name']) ?></td>
                    <td class="price-col">৳ <?= htmlspecialchars($i['price']) ?></td>
                    <td><?= (int)$i['quantity'] ?></td>
                    <td class="subtotal-col">৳ <?= $i['price'] * $i['quantity'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    
        <div class="invoice-footer">
            <span class="total-label">Total Amount</span>
            <span class="total-value">৳ <?= $total ?></span>
        </div>

    </div>

 
    <div class="action-row">
        <a href="cart.php" class="btn-cancel">✕ Cancel</a>
        <a href="payment.php" class="btn-confirm">✔ Confirm Purchase</a>
    </div>

</div>