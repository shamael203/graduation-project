<?php
session_start();
include 'connect.php';

$user_id = $_SESSION['user_id'] ?? 1;

// جلب بيانات السلة
$sql = "SELECT books.title, books.price, cart.quantity
        FROM cart
        JOIN books ON cart.book_id = books.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// حساب الإجمالي
$total = 0;
while($row = $result->fetch_assoc()){
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    $items[] = $row;
}
$vat = $total * 0.15;
$grand_total = $total + $vat;
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>✅ إتمام الشراء</title>
<style>
body { font-family:"Tajawal", sans-serif; background:#f9f9f9; direction:rtl; }
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
    <h2>🧾 إتمام عملية الشراء</h2>

    <!-- جدول المنتجات -->
    <table>
        <tr><th>الكتاب</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr>
        <?php foreach($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['title']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price'],2) ?> ر.س</td>
            <td><?= number_format($item['price']*$item['quantity'],2) ?> ر.س</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- تفاصيل الفاتورة -->
    <p>المجموع الفرعي: <?= number_format($total,2) ?> ر.س</p>
    <p>ضريبة القيمة المضافة (15%): <?= number_format($vat,2) ?> ر.س</p>
    <p class="total">الإجمالي الكلي: <?= number_format($grand_total,2) ?> ر.س</p>

    <!-- نموذج بيانات العميل -->
    <form method="POST" action="confirm_order.php">
        <input type="text" name="name" placeholder="الاسم الكامل" required>
        <input type="email" name="email" placeholder="البريد الإلكتروني" required>
        <input type="text" name="address" placeholder="العنوان" required>
        <select name="payment_method" required>
            <option value="">اختر طريقة الدفع</option>
            <option value="cash">الدفع عند الاستلام</option>
            <option value="card">بطاقة بنكية</option>
        </select>
        <button type="submit">تأكيد الطلب ✅</button>
    </form>
</div>
</body>
</html>