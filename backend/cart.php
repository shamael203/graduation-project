<?php
session_start();
include 'connect.php';
include 'header.php';

$user_id = $_SESSION['user_id'] ?? null;
$total = 0;

// جلب بيانات السلة
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
        body { font-family: Arial, sans-serif; background: #f1f1f1; margin:0; padding:0; }
        .cart-container { display:flex; flex-wrap:wrap; gap:30px; padding:20px; }
        .cart-products, .cart-invoice { flex:1 1 400px; background:#fff; padding:20px; border-radius:10px; }
        .cart-products h3, .cart-invoice h3 { margin-bottom:15px; color:#333; }
        .cart-item { display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid #ddd; padding-bottom:15px; }
        .cart-item img { width:80px; height:100px; object-fit:cover; border-radius:5px; }
        .item-details h4 { margin:0 0 5px; font-size:16px; color:#222; }
        .item-details p { margin:3px 0; font-size:14px; color:#555; }
        .cart-invoice p { font-size:15px; margin:8px 0; color:#444; }
        .cart-invoice strong { color:#000; }
        .coupon-form { margin-top:20px; }
        .coupon-form label { display:block; margin-bottom:5px; font-weight:bold; }
        .coupon-form input { padding:8px; width:70%; margin-right:10px; border:1px solid #ccc; border-radius:5px; }
        .coupon-form button { padding:8px 15px; background:#007bff; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        .cart-buttons { margin-top:25px; display:flex; gap:15px; }
        .cart-buttons .btn { flex:1; padding:10px 20px; text-decoration:none; color:#fff; border-radius:5px; font-weight:bold; text-align:center; cursor:pointer; }
        .cart-buttons .proceed { background:#28a745; }
        .cart-buttons .back { background:#6c757d; }
        /* Popup */
        .popup { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; }
        .popup-content { background:#fff; padding:20px; border-radius:8px; text-align:center; max-width:400px; }
        .popup-content a { display:inline-block; margin:10px; padding:10px 15px; background:#007bff; color:#fff; border-radius:5px; text-decoration:none; }
        .popup-content a:hover { background:#0056b3; }
    </style>
</head>
<body>
<div class="cart-container">
    <div class="cart-products">
        <h3>Products</h3>
        <?php
        if ($user_id && $result->num_rows > 0):
            while($row = $result->fetch_assoc()):
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
                $image = !empty($row['image']) ? "uploads/".$row['image'] : "uploads/default.png";
        ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($image) ?>" alt="Book Image">
                <div class="item-details">
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <p>Quantity: <?= $row['quantity'] ?></p>
                    <p>Price: <?= number_format($row['price'], 2) ?> SAR</p>
                    <p>Total: <?= number_format($subtotal, 2) ?> SAR</p>
                </div>
            </div>
        <?php endwhile; elseif (!$user_id && !empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $book_id => $qty):
                $stmt = $conn->prepare("SELECT title, price, image FROM books WHERE id=?");
                $stmt->bind_param("i", $book_id);
                $stmt->execute();
                $book = $stmt->get_result()->fetch_assoc();
                $subtotal = $book['price'] * $qty;
                $total += $subtotal;
                $image = !empty($book['image']) ? "uploads/".$book['image'] : "uploads/default.png";
            ?>
                <div class="cart-item">
                    <img src="<?= htmlspecialchars($image) ?>" alt="Book Image">
                    <div class="item-details">
                        <h4><?= htmlspecialchars($book['title']) ?></h4>
                        <p>Quantity: <?= $qty ?></p>
                        <p>Price: <?= number_format($book['price'], 2) ?> SAR</p>
                        <p>Total: <?= number_format($subtotal, 2) ?> SAR</p>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <p style="text-align:center;">The cart is currently empty.</p>
        <?php endif; ?>
    </div>

    <div class="cart-invoice">
        <h3>Invoice Details</h3>
        <?php
        $vat = $total * 0.15;
        $grand_total = $total + $vat;
        ?>
        <p>Subtotal: <?= number_format($total, 2) ?> SAR</p>
        <p>VAT (15%): <?= number_format($vat, 2) ?> SAR</p>
        <p><strong>Total: <?= number_format($grand_total, 2) ?> SAR including VAT</strong></p>

        <form method="post" action="apply_coupon.php" class="coupon-form">
            <label for="coupon">Discount Coupon:</label>
            <input type="text" name="coupon" id="coupon" placeholder="Enter code">
            <button type="submit">Submit</button>
        </form>

        <div class="cart-buttons">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <!-- إذا المستخدم مسجل دخول -->
                <a href="checkout.php" class="btn proceed">Proceed to Purchase</a>
            <?php else: ?>
                <!-- إذا المستخدم غير مسجل -->
                <button class="btn proceed" onclick="showPopup()">Proceed to Purchase</button>
            <?php endif; ?>
            <a href="books.php" class="btn back">Return to Shopping</a>
        </div>
    </div>
</div>

<!-- Popup -->
<div class="popup" id="loginPopup">
    <div class="popup-content">
        <p>You must log in or register to proceed with purchase.</p>
        <a href="login.php?redirect=cart.php">Login</a>
        <a href="register.php?redirect=cart.php">Register</a>
        <br><br>
        <button onclick="closePopup()">Close</button>
    </div>
</div>

<script>
function showPopup() {
    document.getElementById('loginPopup').style.display = 'flex';
}
function closePopup() {
    document.getElementById('loginPopup').style.display = 'none';
}
</script>
</body>
</html>