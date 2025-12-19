<?php
session_start();
include 'connect.php';
include 'header.php'; // الهيدر الموحد

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for this user
$stmt = $conn->prepare("
    SELECT id, payment_method, address, total_amount, created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders</title>

<style>
body {
    font-family: Arial, sans-serif;
    background:#f4f6f8;
    direction:ltr;
    margin:0;
    padding:0;
}

.container {
    width:80%;
    margin:40px auto;
    background:#fff;
    padding:25px;
    border-radius:10px;
}

.order-box {
    border:1px solid #ddd;
    padding:15px;
    border-radius:8px;
    margin-bottom:25px;
    background:#fafafa;
}

.order-box h3 {
    margin:0 0 10px;
    color:#3f51b5;
}

.book-item {
    display:flex;
    align-items:center;
    gap:15px;
    margin:10px 0;
    padding-bottom:10px;
    border-bottom:1px solid #eee;
}

.book-item img {
    width:70px;
    height:90px;
    object-fit:cover;
    border-radius:6px;
    border:1px solid #ddd;
}

.book-details p {
    margin:3px 0;
    font-size:14px;
    color:#444;
}

.empty {
    text-align:center;
    color:#777;
    font-size:18px;
    margin-top:40px;
}
</style>
</head>

<body>

<div class="container">
<h2> My Orders</h2>

<?php if ($orders->num_rows > 0): ?>

    <?php while ($order = $orders->fetch_assoc()): ?>

        <div class="order-box">
            <h3>Order #<?= $order['id'] ?></h3>

            <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
            <p><strong>Total:</strong> <?= number_format($order['total_amount'],2) ?> SAR</p>
            <p><strong>Date:</strong> <?= $order['created_at'] ?></p>

            <h4>Books in this order:</h4>

            <?php
            // Fetch books inside this order
            $stmt2 = $conn->prepare("
                SELECT books.title, books.image, order_items.quantity, order_items.price
                FROM order_items
                JOIN books ON order_items.book_id = books.id
                WHERE order_items.order_id = ?
            ");
            $stmt2->bind_param("i", $order['id']);
            $stmt2->execute();
            $items = $stmt2->get_result();

            while ($item = $items->fetch_assoc()):
                $image = !empty($item['image']) ? 'uploads/' . $item['image'] : 'uploads/default.png';
            ?>

                <div class="book-item">
                    <img src="<?= htmlspecialchars($image) ?>" alt="Book Image">

                    <div class="book-details">
                        <p><strong><?= htmlspecialchars($item['title']) ?></strong></p>
                        <p>Quantity: <?= $item['quantity'] ?></p>
                        <p>Price: <?= number_format($item['price'],2) ?> SAR</p>
                    </div>
                </div>

            <?php endwhile; ?>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p class="empty">No orders found.</p>

<?php endif; ?>

</div>

</body>
</html>
<?php include 'footer.php'; ?> <!-- الفوتر الموحد -->