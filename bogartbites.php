<?php
include "db.php";

// Menu items
$menu = [
    "Burger & Fries" => ["price" => 175, "img" => "burger-fries.webp"],
    "Pizza" => ["price" => 499, "img" => "pizza.webp"],
    "Sisig" => ["price" => 210, "img" => "sisig.webp"],
    "Carbonara" => ["price" => 190, "img" => "carbonara.webp"],
    "Chicken Steak" => ["price" => 220, "img" => "chicken-steak.webp"]
];

// Fetch all previous orders
$allOrders = [];
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $allOrders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bogart Bites</title>
<link rel="stylesheet" href="tastybites.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <img src="logo.webp" class="logo-img">
        <span class="logo-text">Bogart Bites</span>
    </div>
</nav>

<header class="banner" style="background-image:url('bannerimg.webp')">
    <div class="nav-left">
        <img src="logo.webp" class="logo-img">
    </div>
    <h1>Welcome to Bogart Bites</h1>
    <p>Dark. Cozy. Delicious.</p>
</header>

<section class="order-form">
<h2>Place Your Order</h2>

<form method="POST" action="processorder.php">
    <input type="text" name="customer" placeholder="Your Name" required>
    <input type="number" name="table" placeholder="Table Number" required>
    <input type="number" name="people" placeholder="Number of People" required>

    <label for="payment">Payment Method:</label>
    <select name="payment" id="payment" required>
        <option value="Cash">Cash</option>
        <option value="Credit">Credit</option>
        <option value="GCash">GCash</option>
    </select>

    <div class="menu-grid">
        <?php foreach ($menu as $name => $data): ?>
        <label class="menu-card">
            <img src="<?= $data['img'] ?>" alt="<?= $name ?>">
            <h3><?= $name ?></h3>
            <span>Php <?= number_format($data['price'],2) ?></span>
            <input type="checkbox" name="items[]" value="<?= $name ?>|<?= $data['price'] ?>">
        </label>
        <?php endforeach; ?>
    </div>

    <button class="pay-btn" type="submit">Submit Order</button>
</form>
</section>

<section class="order-list">
<h2>All Orders</h2>

<?php if (!empty($allOrders)): ?>
    <div class="orders-container">
    <?php foreach ($allOrders as $o): 
        $statusClass = strtolower($o['status']); // 'PAID' => 'paid', 'Pending' => 'pending'
    ?>
    <div class="success-box <?= $statusClass ?>">
        <h3><?= htmlspecialchars($o['customer']) ?> (Table <?= $o['table_number'] ?>)</h3>
        <p>People: <?= $o['people'] ?></p>
        <p>Items: <?= htmlspecialchars($o['items']) ?></p>
        <p>Total: Php <?= number_format($o['total'],2) ?></p>
        <p>Payment: <?= htmlspecialchars($o['payment_method']) ?></p>
        <p>Status: <?= htmlspecialchars($o['status']) ?></p>
    </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No orders yet.</p>
<?php endif; ?>
</section>

<footer class="footer">
    <p>Designed by <strong>Jose Victor Siong</strong></p>
</footer>

<!-- Live total calculation -->
<script>
const menuCheckboxes = document.querySelectorAll('input[name="items[]"]');
const totalDisplay = document.createElement('p');
totalDisplay.style.fontWeight = 'bold';
totalDisplay.style.fontSize = '18px';
totalDisplay.style.marginTop = '15px';
document.querySelector('.order-form').insertBefore(totalDisplay, document.querySelector('.pay-btn'));

function updateTotal() {
    let total = 0;
    menuCheckboxes.forEach(cb => {
        if (cb.checked) {
            const price = parseFloat(cb.value.split('|')[1]);
            total += price;
        }
    });
    totalDisplay.textContent = "Total: Php " + total.toFixed(2);
}

menuCheckboxes.forEach(cb => cb.addEventListener('change', updateTotal));
updateTotal();
</script>

</body>
</html>
