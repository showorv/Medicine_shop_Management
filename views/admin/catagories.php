<?php
require_once "../../controllers/AdminMiddleware.php";
require_once "../../models/Category.php";

$cat = new Category();

/* Add category */
if(isset($_POST['name'])){
    $cat->create($_POST['name'], $_POST['type']);
    header("Location: catagories.php");
    exit();
}

/* Delete category */
if(isset($_GET['delete'])){
    $cat->delete($_GET['delete']);
    header("Location: catagories.php");
    exit();
}

$categories = $cat->all();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Categories Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/categories.css">
</head>
<body>


<div class="navbar">
    <div class="navbar-brand">
        <span> Medicine Shop</span>
        <span class="admin-badge">Admin</span>
    </div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <!-- <a href="customers.php">Customers</a>
        <a href="medicines.php">Medicines</a>
        <a href="orders.php">Orders</a> -->
        <a href="../../views/logout.php">Logout</a>
    </div>
</div>

<div class="container">


    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span>/</span>
        <span>Categories</span>
    </div>

    <div class="page-layout">

 
        <div class="form-card">
            <div class="form-card-title"> Add Category</div>

            <form method="POST">

                <div class="form-group">
                    <label class="form-label" for="name">Category Name</label>
                    <input class="form-input" type="text" id="name"
                           name="name" placeholder="e.g. Antibiotics" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="type">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="solid">Solid</option>
                        <option value="liquid">Liquid</option>
                    </select>
                </div>

                <button type="submit" class="btn-add">Add Category</button>

            </form>
        </div>


        <div class="list-section">

            <div class="list-header">
                <div class="list-title">All Categories</div>
                <span class="cat-count"><?= count($categories) ?> categor<?= count($categories) !== 1 ? 'ies' : 'y' ?></span>
            </div>

            <?php if(empty($categories)): ?>
                <div class="empty-state">No categories yet. Add one using the form.</div>
            <?php else: ?>

            <table class="cat-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $i => $c): ?>
                    <tr>
                        <td style="color:#7a9590;"><?= $i + 1 ?></td>

                        <td class="cat-name"><?= htmlspecialchars($c['name']) ?></td>

                        <td>
                            <?php
                                $type      = strtolower($c['category_type'] ?? 'solid');
                                $typeClass = 'type-' . $type;
                            ?>
                            <span class="type-badge <?= $typeClass ?>"><?= htmlspecialchars($c['category_type']) ?></span>
                        </td>

                        <td>
                            <a href="?delete=<?= $c['id'] ?>"
                               class="btn-delete"
                               onclick="return confirm('Delete category &quot;<?= htmlspecialchars($c['name']) ?>&quot;?')">
                               🗑 Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php endif; ?>
        </div>

    </div>
</div>

</body>
</html>