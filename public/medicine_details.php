<?php
session_start();
require_once "../config/database.php";

$db = (new Database())->connect();

/* GET ID */
if(!isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

/* GET MEDICINE */
$stmt = $db->prepare("
    SELECT m.*, c.name as category 
    FROM medicines m
    JOIN categories c ON m.category_id = c.id
    WHERE m.id=?
");
$stmt->execute([$id]);

$medicine = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$medicine){
    echo "Medicine not found";
    exit();
}

$isAvailable = (int)$medicine['availability'] > 0;
?>

<link rel="stylesheet" href="../assets/css/index.css">
<link rel="stylesheet" href="../assets/css/medicine_details.css">

<!-- ── Navbar (same as index) ── -->
<div class="navbar">
    <span>💊 Medicine Shop</span>
    <div class="navbar-links">
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="../views/admin/dashboard.php" class="admin-link">⚙ Admin</a>
        <?php endif; ?>
        <a href="../views/profile.php">Profile</a>
        <a href="../views/cart.php" class="cart-link">🛒 Cart (<span id="cartCount">0</span>)</a>
        <a href="../views/logout.php">Logout</a>
    </div>
</div>

<div class="container">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">← Back to Medicines</a>
        <span>/</span>
        <span><?= htmlspecialchars($medicine['name']) ?></span>
    </div>

    <!-- Detail Card -->
    <div class="detail-card">

        <!-- LEFT: Image -->
        <div class="detail-image-wrap">
            <?php if(!empty($medicine['image_path'])): ?>
                <img src="<?= htmlspecialchars($medicine['image_path']) ?>"
                     alt="<?= htmlspecialchars($medicine['name']) ?>"
                     class="detail-image">
            <?php else: ?>
                <div class="detail-image-placeholder">💊</div>
            <?php endif; ?>

            <div class="stock-badge <?= $isAvailable ? 'available' : 'unavailable' ?>">
                <?= $isAvailable ? '✔ In Stock' : '✘ Out of Stock' ?>
            </div>
        </div>

        <!-- RIGHT: Info -->
        <div class="detail-info">

            <h1 class="medicine-title"><?= htmlspecialchars($medicine['name']) ?></h1>

            <!-- Data Table -->
            <table class="info-table">
                <tbody>
                    <tr>
                        <td class="info-label">Category</td>
                        <td class="info-value">
                            <span class="category-tag"><?= htmlspecialchars($medicine['category']) ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Vendor</td>
                        <td class="info-value"><?= htmlspecialchars($medicine['vendor_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Price</td>
                        <td class="info-value price-value">৳ <?= htmlspecialchars($medicine['price']) ?></td>
                    </tr>
                    <tr>
                        <td class="info-label">Stock</td>
                        <td class="info-value"><?= htmlspecialchars($medicine['availability']) ?> units</td>
                    </tr>
                </tbody>
            </table>

            <!-- Description -->
            <?php if(!empty($medicine['description'])): ?>
            <div class="description-block">
                <p class="desc-label">Description</p>
                <p class="desc-text"><?= nl2br(htmlspecialchars($medicine['description'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Add to Cart -->
            <div class="cart-action">
                <div class="qty-wrap">
                    <label for="qty">Quantity</label>
                    <div class="qty-control">
                        <button type="button" onclick="changeQty(-1)">−</button>
                        <input  type="number" id="qty" value="1" min="1"
                                max="<?= (int)$medicine['availability'] ?>"
                                <?= $isAvailable ? '' : 'disabled' ?>>
                        <button type="button" onclick="changeQty(1)">+</button>
                    </div>
                </div>

                <button class="btn btn-primary btn-lg"
                        onclick="addToCart(<?= (int)$medicine['id'] ?>)"
                        <?= $isAvailable ? '' : 'disabled' ?>>
                    🛒 Add to Cart
                </button>
            </div>

            <?php if(!$isAvailable): ?>
                <p class="out-notice">This medicine is currently out of stock.</p>
            <?php endif; ?>

        </div><!-- /.detail-info -->
    </div><!-- /.detail-card -->

</div><!-- /.container -->


<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    const max   = parseInt(input.max) || 9999;
    let val = parseInt(input.value) + delta;
    if (val < 1)   val = 1;
    if (val > max) val = max;
    input.value = val;
}

function addToCart(id) {
    const qty = document.getElementById('qty').value;

    fetch('api/cart_add.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ medicine_id: id, quantity: qty })
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert('Login required');
            window.location = 'login.php';
        } else {
            document.getElementById('cartCount').innerText =
                parseInt(document.getElementById('cartCount').innerText || 0) + 1;
            if (confirm('✅ Added to cart! Go to cart now?')) {
                window.location = '../views/cart.php';
            }
        }
    });
}
</script>
