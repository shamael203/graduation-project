<?php
session_start();
include 'connect.php';
include 'header.php'; // unified header

$user_id = $_SESSION['user_id'] ?? 1;

// Fetch cart data
$sql = "SELECT books.title, books.price, cart.quantity
        FROM cart
        JOIN books ON cart.book_id = books.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate totals
$total = 0;
$items = [];
while($row = $result->fetch_assoc()){
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = $row;
}
$vat = $total * 0.15;
$grand_total = $total + $vat;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<style>
body { font-family: Arial, sans-serif; background:#f9f9f9; direction:ltr; }
.container { max-width:800px; margin:40px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#3f51b5; margin-bottom:20px;}
table { width:100%; border-collapse:collapse; margin-bottom:20px;}
table th, table td { border:1px solid #ddd; padding:10px; text-align:center;}
.total { font-size:18px; font-weight:bold; color:#e91e63; text-align:right;}
form { margin-top:20px;}
input, select { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:5px;}
button { background:#4caf50; color:white; padding:12px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;}
button:hover { background:#388e3c;}
</style>
</head>
<body>
<div class="container">
    <h2>Checkout</h2>

    <!-- Products Table -->
    <table>
        <tr><th>Book</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
        <?php foreach($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['title']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price'],2) ?> SAR</td>
            <td><?= number_format($item['price']*$item['quantity'],2) ?> SAR</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Invoice Details -->
    <p>Subtotal: <?= number_format($total,2) ?> SAR</p>
    <p>VAT (15%): <?= number_format($vat,2) ?> SAR</p>
    <p class="total">Grand Total: <?= number_format($grand_total,2) ?> SAR</p>

    <!-- Customer Form -->
    <form method="POST" action="confirm_order.php">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="address" placeholder="Address" required>
        <select name="payment_method" required>
            <option value="">Select Payment Method</option>
            <option value="cash">Cash on Delivery</option>
            <option value="card">Credit/Debit Card</option>
        </select>
        <button type="submit">Confirm Order</button>
    </form>
</div>

<?php include 'footer.php'; ?> <!-- unified footer -->

</body>
</html>