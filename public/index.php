<?php
session_start();
require_once "../models/Medicine.php";

$m = new Medicine();
$medicines = $m->getAll();
$categories = $m->getCategories();
?>

<link rel="stylesheet" href="../assets/css/index.css">


<div class="navbar">
    <span>Medicine Shop</span>
    <div class="navbar-links">
        <?php if($_SESSION['role'] === 'admin'): ?>
            <a href="../views/admin/dashboard.php" class="admin-link">⚙ Admin</a>
        <?php endif; ?>
        <a href="../views/profile.php">Profile</a>
        <a href="../views/cart.php" class="cart-link">🛒 Cart (<span id="cartCount">0</span>)</a>
        <a href="../views/logout.php">Logout</a>
    </div>
</div>


<div class="container">

  
    <div class="welcome-banner">
        <div class="welcome-avatar">👤</div>
        <div class="welcome-text">
            <h2>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
            <p>Browse and order your medicines below.</p>
        </div>
    </div>

  
    <div class="search-box">
        <input type="text"   id="search" placeholder="Search medicine name…" onkeyup="liveSearch()">
        <input type="text"   id="vendor" placeholder=" Filter by vendor…"      onkeyup="liveSearch()">
    </div>

  
    <div class="filter-section">
        <label>Category</label>
        <select id="categorySelect" onchange="filterCategory(this.value)">
            <option value="">All Categories</option>
            <?php foreach($categories as $c): ?>
                <option value="<?= $c['name'] ?>">
                    <?= $c['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>


    <div class="section-header">
        <h2>Available Medicines</h2>
        <span class="result-count" id="resultCount"><?= count($medicines) ?> items</span>
    </div>

    <div id="box" class="grid">
        <?php foreach($medicines as $m): ?>
            <div class="card">
                <h3><?= $m['name'] ?></h3>

                <div class="data-row">
                    <span class="data-label">Vendor:</span>
                    <span class="data-value"><?= $m['vendor_name'] ?></span>
                </div>

                <div class="data-row">
                    <span class="data-label">Price:</span>
                    <span class="data-value price"><?= htmlspecialchars($m['price']) ?></span>
                </div>

                <div class="data-row">
                    <span class="data-label">Available:</span>
                    <?php
                        $avail = strtolower($m['availability']);
                        $badgeClass = ($avail === 'available') ? 'available' : 'unavailable';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($m['availability']) ?></span>
                </div>

                <div class="card-divider"></div>

                <div class="card-actions">
                    <input  type="number" class="qty-input"
                            id="qty<?= $m['id'] ?>" value="1" min="1">
                    <button class="btn btn-primary"
                            onclick="addToCart(<?= $m['id'] ?>)">Add to Cart</button>
                    <a      href="medicine_details.php?id=<?= $m['id'] ?>"
                            class="btn btn-outline">View</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>


<script>
function liveSearch(){
    fetchData();
}

function filterCategory(genre){
    fetchData(genre);
}

function fetchData(genre=''){
    let q = document.getElementById('search').value;
    let vendor = document.getElementById('vendor').value;

    fetch(`api/search.php?q=${q}&vendor=${vendor}&genre=${genre}`)
    .then(res => res.json())
    .then(data => {
        const countEl = document.getElementById('resultCount');
            if (countEl) countEl.textContent = data.length + ' items';
        let html = '';

        data.forEach(m => {
    html += `
    <div class="card">

        <h3>${m.name}</h3>

        <div class="data-row">
            <span class="data-label">Vendor:</span>
            <span class="data-value">${m.vendor_name}</span>
        </div>

        <div class="data-row">
            <span class="data-label">Price:</span>
            <span class="data-value price">${m.price}</span>
        </div>

        <div class="data-row">
            <span class="data-label">Available:</span>
            <span class="badge">${m.availability}</span>
        </div>

        <div class="card-divider"></div>

        <div class="card-actions">
            <input type="number"
                   class="qty-input"
                   id="qty${m.id}"
                   value="1"
                   min="1">

            <button class="btn btn-primary"
                    onclick="addToCart(${m.id})">
                Add to Cart
            </button>

            <a href="medicine_details.php?id=${m.id}"
               class="btn btn-outline">
                View
            </a>
        </div>

    </div>
    `;
});

        document.getElementById('box').innerHTML = html;
    });
}
function loadCartCount(){
    fetch("api/cart_count.php")
    .then(res => res.json())
    .then(data => {
        document.getElementById("cartCount").innerText = data.count;
    });
}
function addToCart(id){

let qty = document.getElementById("qty"+id).value;

fetch("api/cart_add.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({
        medicine_id: id,
        quantity: qty
    })
})
.then(res=>res.json())
.then(data=>{
    if(data.error){
        alert("Login required");
        window.location="login.php";
    } else {
        
               loadCartCount();
        if(confirm("Added to cart! Go to cart?")){
                window.location = "../views/cart.php";
                
            }
    }
});
}
</script>


