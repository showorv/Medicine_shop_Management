<?php
require_once "../../controllers/AdminMiddleware.php";
require_once "../../models/MedicineAdmin.php";
require_once "../../config/database.php";

$m  = new MedicineAdmin();
$db = (new Database())->connect();


$categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);


if(isset($_POST['name'])){
    $img  = $_FILES['image']['name'];
    $tmp  = $_FILES['image']['tmp_name'];
    $path = "../public/uploads/medicines/" . time() . "_" . $img;
    move_uploaded_file($tmp, "../../public/" . $path);
    $_POST['image'] = $path;
    $m->create($_POST);
    header("Location: medicines.php");
    exit();
}

if(isset($_GET['delete'])){
    $m->delete($_GET['delete']);
    header("Location: medicines.php");
    exit();
}

$data = $m->all();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medicines – Admin</title>
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../../assets/css/medicines_admin.css">
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
        <a href="catagories.php">Categories</a>
        <a href="orders.php">Orders</a> -->
        <a href="../../views/logout.php">Logout</a>
    </div>
</div>

<div class="container">

  
    <div class="breadcrumb">
        <a href="dashboard.php">Dashboard</a>
        <span>/</span>
        <span>Medicines</span>
    </div>

    <div class="page-layout">

        <div class="form-card">
            <div class="form-card-title">Add New Medicine</div>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label class="form-label" for="name">Medicine Name</label>
                    <input class="form-input" type="text" id="name"
                           name="name" placeholder="e.g. Paracetamol 500mg" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="vendor">Vendor</label>
                    <input class="form-input" type="text" id="vendor"
                           name="vendor" placeholder="Vendor name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="price">Price (৳)</label>
                        <input class="form-input" type="number" id="price"
                               name="price" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="stock">Stock</label>
                        <input class="form-input" type="number" id="stock"
                               name="stock" placeholder="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_id">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select category…</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-textarea" id="description"
                              name="description" placeholder="Optional description…"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Image</label>
                    <label class="file-label" for="image">
                        📷 Choose medicine image
                    </label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>

                <button type="submit" class="btn-add">Add Medicine</button>

            </form>
        </div>


        <div class="list-section">

            <div class="list-header">
                <div class="list-title">💊 All Medicines</div>
                <span class="med-count"><?= count($data) ?> medicine<?= count($data) !== 1 ? 's' : '' ?></span>
            </div>

            <?php if(empty($data)): ?>
                <div class="empty-state">No medicines added yet. Use the form to add one.</div>
            <?php else: ?>

            <table class="med-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $item): ?>
                    <tr>
                        <td>
                            <div class="med-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="med-vendor">Vendor: <?= htmlspecialchars($item['vendor_name'] ?? $item['vendor'] ?? '—') ?></div>
                        </td>
                        <td class="price-col">৳ <?= htmlspecialchars($item['price']) ?></td>
                        <td><?= htmlspecialchars($item['availability'] ?? $item['stock'] ?? '—') ?> units</td>
                        <td>
                            <div class="action-btns">
                                <a href="edit_medicine.php?id=<?= $item['id'] ?>" class="btn-edit">✏ Edit</a>
                                <a href="?delete=<?= $item['id'] ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Delete <?= htmlspecialchars($item['name']) ?>?')">🗑 Delete</a>
                            </div>
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