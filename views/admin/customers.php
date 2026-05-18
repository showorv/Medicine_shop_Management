<?php
require_once "../../controllers/AdminMiddleware.php";
require_once "../../config/database.php";

$db = (new Database())->connect();


if(isset($_GET['delete'])){
    $stmt = $db->prepare("DELETE FROM users WHERE id=? AND role='customer'");
    $stmt->execute([$_GET['delete']]);
    header("Location: customers.php");
    exit();
}


$customers = $db->query("SELECT * FROM users WHERE role='customer'")
                ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customers – Admin</title>
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../../assets/css/customers.css">
</head>
<body>


<div class="navbar">
    <div class="navbar-brand">
        <span>Medicine Shop</span>
        <span class="admin-badge">Admin</span>
    </div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <!-- <a href="medicines.php">Medicines</a>
        <a href="catagories.php">Categories</a>
        <a href="orders.php">Orders</a> -->
        <a href="../../views/logout.php">Logout</a>
    </div>
</div>

<div class="container">

    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span>/</span>
        <span>Customers</span>
    </div>

    <div class="page-header">
        <h2 class="page-title">👥 Customer Management</h2>
        <span class="customer-count"><?= count($customers) ?> customer<?= count($customers) !== 1 ? 's' : '' ?></span>
    </div>

    <?php if(empty($customers)): ?>

        <div class="empty-state">No customers registered yet.</div>

    <?php else: ?>

        <table class="customer-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $c): ?>
                <tr>

         
                    <td>
                        <div class="customer-name-cell">
                            <div class="customer-avatar">
                                <?= strtoupper(mb_substr($c['name'], 0, 1)) ?>
                            </div>
                            <span class="customer-name"><?= htmlspecialchars($c['name']) ?></span>
                        </div>
                    </td>

                    <td><?= htmlspecialchars($c['email']) ?></td>

                    <td><?= htmlspecialchars($c['phone'] ?? '—') ?></td>

                    <td><?= htmlspecialchars($c['address'] ?? '—') ?></td>

                
                    <td>
                        <a href="?delete=<?= $c['id'] ?>"
                           class="btn-delete"
                           onclick="return confirm('Delete <?= htmlspecialchars($c['name']) ?>? This cannot be undone.')">
                           🗑 Delete
                        </a>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

</body>
</html>