<?php
session_start();
require_once "../config/database.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$db  = (new Database())->connect();
$uid = $_SESSION['user_id'];


if(isset($_GET['remove'])){
    $db->prepare("DELETE FROM cart WHERE id=?")->execute([$_GET['remove']]);
    header("Location: cart.php");
    exit();
}


if(isset($_POST['update'])){
    $db->prepare("UPDATE cart SET quantity=? WHERE id=?")
       ->execute([$_POST['qty'], $_POST['id']]);
}


$stmt = $db->prepare("
    SELECT c.id, m.name, m.price, c.quantity, m.vendor_name
    FROM cart c
    JOIN medicines m ON c.medicine_id = m.id
    WHERE c.user_id = ?
");
$stmt->execute([$uid]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../assets/css/index.css">
<link rel="stylesheet" href="../assets/css/cart.css">


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
        <a href="../public/index.php">← Home</a>
        <span>/</span>
        <span>Your Cart</span>
    </div>

    <h2 class="page-title">Your Cart</h2>

    <?php if(empty($items)): ?>

     
        <div class="empty-cart">
            <p>🛒 Your cart is empty.</p>
            <a href="../public/index.php" class="btn btn-primary">Browse Medicines</a>
        </div>

    <?php else: ?>

        <table class="cart-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach($items as $i): ?>
                <?php $subtotal = $i['price'] * $i['quantity']; $total += $subtotal; ?>
                <tr>
                   
                    <td>
                        <div class="medicine-name"><?= htmlspecialchars($i['name']) ?></div>
                        <div class="vendor-name">Vendor: <?= htmlspecialchars($i['vendor_name']) ?></div>
                    </td>

                   
                    <td class="price-col">৳ <?= htmlspecialchars($i['price']) ?></td>

                  
                    <td>
                        <form method="POST" style="display:flex; gap:6px; align-items:center;">
                            <input type="hidden" name="id" value="<?= $i['id'] ?>">
                            <input type="number" name="qty" value="<?= $i['quantity'] ?>" min="1" class="qty-input">
                            <button name="update" class="btn btn-primary">Update</button>
                        </form>
                    </td>

                   
                    <td class="subtotal-col">৳ <?= $subtotal ?></td>

                
                    <td>
                        <a href="?remove=<?= $i['id'] ?>" class="btn btn-danger"
                           onclick="return confirm('Remove this item?')">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Summary Box -->
        <div class="cart-summary">
            <div class="summary-row">
                <span>Items (<?= count($items) ?>)</span>
                <span>৳ <?= $total ?></span>
            </div>
            <hr class="summary-divider">
            <div class="summary-total">
                <span>Total</span>
                <span>৳ <?= $total ?></span>
            </div>
            <a href="checkout.php" class="btn-checkout">Proceed to Checkout →</a>
        </div>

    <?php endif; ?>

</div>