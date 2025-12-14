<?php
session_start();
include 'connect.php';
include 'header.php';

$user_id = $_SESSION['user_id'] ?? null;
$total = 0;

if ($user_id) {
    $sql = "SELECT cart.id AS cart_id, books.title, books.price, books.image, cart.quantity
            FROM cart
            JOIN books ON cart.book_id = books.id
            WHERE cart.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shopping Cart</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f1f1;
    margin: 0;
    padding: 0;
}

.cart-container {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    padding: 20px;
}

.cart-products,
.cart-invoice {
    flex: 1 1 400px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
}

.cart-products h3,
.cart-invoice h3 {
    margin-bottom: 15px;
    color: #333;
}

/* المنتج */
.cart-item {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 15px;
}

.cart-item img {
    width: 80px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
}

.item-details {
    flex: 1;
}

.item-details h4 {
    margin: 0 0 5px;
    font-size: 16px;
    color: #222;
}

.item-details p {
    margin: 3px 0;
    font-size: 14px;
    color: #555;
}

/* زر الحذف (ستايل متاجر) */
.delete-form {
    margin-left: auto;
}

.delete-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #ddd;
    background: #f8f8f8;
    color: #555;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.delete-btn:hover {
    background: #dc3545;
    color: #fff;
    border-color: #dc3545;
}

/* الفاتورة */
.cart-invoice p {
    font-size: 15px;
    margin: 8px 0;
    color: #444;
}

.cart-invoice strong {
    color: #000;
}

/* كوبون */
.coupon-form {
    margin-top: 20px;
}

.coupon-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.coupon-form input {
    padding: 8px;
    width: 70%;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.coupon-form button {
    padding: 8px 15px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* أزرار */
.cart-buttons {
    margin-top: 25px;
    display: flex;
    gap: 15px;
}

.cart-buttons .btn {
    flex: 1;
    padding: 10px;
    text-decoration: none;
    color: #fff;
    border-radius: 5px;
    font-weight: bold;
    text-align: center;
}

.cart-buttons .proceed {
    background: #28a745;
}

.cart-buttons .back {
    background: #6c757d;
}

/* Popup */
.popup {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}

.popup-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}
</style>
</head>

<body>

<div class="cart-container">

<!-- المنتجات -->
<div class="cart-products">
<h3>Products</h3>

<?php if ($user_id && $result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()):
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $image = !empty($row['image']) ? "uploads/".$row['image'] : "uploads/default.png";
?>
<div class="cart-item">

<img src="<?= htmlspecialchars($image) ?>" alt="Book">

<div class="item-details">
    <h4><?= htmlspecialchars($row['title']) ?></h4>
    <p>Quantity: <?= $row['quantity'] ?></p>
    <p>Price: <?= number_format($row['price'],2) ?> SAR</p>
    <p>Total: <?= number_format($subtotal,2) ?> SAR</p>
</div>

<form method="POST" action="remove_from_cart.php" class="delete-form">
    <input type="hidden" name="cart_id" value="<?= (int)$row['cart_id'] ?>">
    <button type="submit" class="delete-btn" title="Remove item">✕</button>
</form>

</div>
<?php endwhile; else: ?>
<p style="text-align:center;">The cart is currently empty.</p>
<?php endif; ?>

</div>

<!-- الفاتورة -->
<div class="cart-invoice">
<h3>Invoice Details</h3>

<?php
$vat = $total * 0.15;
$grand_total = $total + $vat;
?>

<p>Subtotal: <?= number_format($total,2) ?> SAR</p>
<p>VAT (15%): <?= number_format($vat,2) ?> SAR</p>
<p><strong>Total: <?= number_format($grand_total,2) ?> SAR</strong></p>

<form method="post" action="apply_coupon.php" class="coupon-form">
<label>Discount Coupon</label>
<input type="text" name="coupon">
<button type="submit">Apply</button>
</form>

<div class="cart-buttons">
<?php if ($user_id): ?>
<a href="checkout.php" class="btn proceed">Proceed to Purchase</a>
<?php else: ?>
<a href="login.php" class="btn proceed">Login to Continue</a>
<?php endif; ?>
<a href="books.php" class="btn back">Return to Shopping</a>
</div>

</div>
</div>

</body>
</html>
