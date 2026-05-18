<?php
require_once "../../controllers/AdminMiddleware.php";
require_once "../../config/database.php";

$db = (new Database())->connect();


if(isset($_GET['id']) && isset($_GET['status'])){
    $stmt = $db->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$_GET['status'], $_GET['id']]);
    header("Location: orders.php");
    exit();
}


$orders = $db->query("
    SELECT o.*, u.name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders – Admin</title>
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../../assets/css/admin_orders.css">
</head>
<body>


<div class="navbar">
    <div class="navbar-brand">
        <span>Medicine Shop</span>
        <span class="admin-badge">Admin</span>
    </div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <!-- <a href="customers.php">Customers</a>
        <a href="medicines.php">Medicines</a>
        <a href="catagories.php">Categories</a> -->
        <a href="../../views/logout.php">Logout</a>
    </div>
</div>

<div class="container">


    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span>/</span>
        <span>Orders</span>
    </div>

 
    <div class="page-header">
        <h2 class="page-title">Order Management</h2>
        <span class="order-count"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?></span>
    </div>

    <?php if(empty($orders)): ?>

        <div class="empty-state">No orders placed yet.</div>

    <?php else: ?>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Ship To</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
            <?php
                $status     = strtolower($o['status']);
                $badgeClass = 'status-' . $status;
                $isDone     = in_array($status, ['accepted', 'rejected', 'delivered']);

                $date = isset($o['order_date'])
                    ? date('d M Y', strtotime($o['order_date']))
                    : '—';
            ?>
            <tr>

                <td>
                    <div class="order-id">#<?= $o['id'] ?></div>
                    <div style="font-size:12px; color:#7a9590; margin-top:2px;"><?= $date ?></div>
                </td>

        
                <td>
                    <div class="customer-cell">
                        <div class="customer-avatar">
                            <?= strtoupper(mb_substr($o['name'], 0, 1)) ?>
                        </div>
                        <span class="customer-name"><?= htmlspecialchars($o['name']) ?></span>
                    </div>
                </td>

              
                <td class="amount-col">৳ <?= htmlspecialchars($o['total_amount']) ?></td>

              
                <td style="text-transform:capitalize;"><?= htmlspecialchars($o['payment_method'] ?? '—') ?></td>

             
                <td>
                    <div class="address-text" title="<?= htmlspecialchars($o['shipping_address'] ?? '') ?>">
                        <?= htmlspecialchars($o['shipping_address'] ?? '—') ?>
                    </div>
                </td>

             
                <td>
                    <span class="status-badge <?= $badgeClass ?>">
                        <?= htmlspecialchars($o['status']) ?>
                    </span>
                </td>

           
                <td>
                    <?php if($isDone): ?>
                        <span class="btn-disabled">Done</span>
                    <?php else: ?>
                        <div class="action-btns">
                            <a href="?id=<?= $o['id'] ?>&status=accepted"
                               class="btn-accept"
                               onclick="return confirm('Accept order #<?= $o['id'] ?>?')">
                               Accept
                            </a>
                            <a href="?id=<?= $o['id'] ?>&status=rejected"
                               class="btn-reject"
                               onclick="return confirm('Reject order #<?= $o['id'] ?>?')">
                               Reject
                            </a>
                        </div>
                    <?php endif; ?>
                </td>

            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>

</div>

</body>
</html>