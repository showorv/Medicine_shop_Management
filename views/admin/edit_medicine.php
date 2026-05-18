<?php
require_once "../../controllers/AdminMiddleware.php";
require_once "../../models/MedicineAdmin.php";
require_once "../../config/database.php";

$m  = new MedicineAdmin();
$db = (new Database())->connect();


$id   = $_GET['id'];
$stmt = $db->prepare("SELECT * FROM medicines WHERE id=?");
$stmt->execute([$id]);
$medicine = $stmt->fetch(PDO::FETCH_ASSOC);


$categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);


if(isset($_POST['name'])){

    $img = $medicine['image_path'];

    if(!empty($_FILES['image']['name'])){
        $imgName = $_FILES['image']['name'];
        $tmp     = $_FILES['image']['tmp_name'];
        $img     = "uploads/medicines/" . time() . "_" . $imgName;
        move_uploaded_file($tmp, "../../public/" . $img);
    }

    $stmt = $db->prepare("
        UPDATE medicines
        SET name=?, category_id=?, vendor_name=?, price=?, availability=?, description=?, image_path=?
        WHERE id=?
    ");
    $stmt->execute([
        $_POST['name'],
        $_POST['category_id'],
        $_POST['vendor'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['description'],
        $img,
        $id
    ]);

    header("Location: medicines.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Medicine Admin</title>
    <link rel="stylesheet" href="../../assets/css/index.css">
    <link rel="stylesheet" href="../../assets/css/edit_medicine.css">
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
        <a href="medicines.php">Medicines</a>
        <span>/</span>
        <span>Edit</span>
    </div>

    <h2 class="page-title"> Edit Medicine</h2>

    <div class="form-card">
        <div class="form-card-title">Editing: <?= htmlspecialchars($medicine['name']) ?></div>
<!-- 
   
        <div class="current-image-box">
            <?php if(!empty($medicine['image_path'])): ?>
                <img src="../../public/<?= htmlspecialchars($medicine['image_path']) ?>"
                     alt="<?= htmlspecialchars($medicine['name']) ?>">
            <?php else: ?>
                <div class="img-placeholder"></div>
            <?php endif; ?>
            <div class="current-image-info">
                <p>Current Image</p>
                <span>Upload a new file below to replace it</span>
            </div>
        </div> -->

        <form method="POST" enctype="multipart/form-data">

          
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="name">Medicine Name</label>
                    <input class="form-input" type="text" id="name" name="name"
                           value="<?= htmlspecialchars($medicine['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="vendor">Vendor</label>
                    <input class="form-input" type="text" id="vendor" name="vendor"
                           value="<?= htmlspecialchars($medicine['vendor_name']) ?>" required>
                </div>
            </div>

         
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="price">Price (৳)</label>
                    <input class="form-input" type="number" id="price" name="price"
                           value="<?= htmlspecialchars($medicine['price']) ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="stock">Stock</label>
                    <input class="form-input" type="number" id="stock" name="stock"
                           value="<?= htmlspecialchars($medicine['availability']) ?>" required>
                </div>
            </div>

      
            <div class="form-group">
                <label class="form-label" for="category_id">Category</label>
                <select class="form-select" id="category_id" name="category_id">
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= $medicine['category_id'] == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

         
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-textarea" id="description"
                          name="description"><?= htmlspecialchars($medicine['description'] ?? '') ?></textarea>
            </div>

            <hr class="form-divider">

            <div class="form-group">
                <label class="form-label">Replace Image (optional)</label>
                <label class="file-label" for="image">
                    📷 Choose a new image to upload
                </label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-actions">
                <a href="medicines.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-update">Save Changes</button>
            </div>

        </form>
    </div>

</div>

</body>
</html>