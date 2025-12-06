<?php
session_start();
include 'connect.php';

$user_id = $_SESSION['user_id'] ?? 1;

// استلام بيانات النموذج من checkout.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $address  = $_POST['address'];
    $payment  = $_POST['payment_method'];

    // جلب بيانات السلة
    $sql = "SELECT books.id AS book_id, books.title, books.price, cart.quantity
            FROM cart
            JOIN books ON cart.book_id = books.id
            WHERE cart.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0;
    $items = [];
    while($row = $result->fetch_assoc()){
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal;
        $items[] = $row;
    }
    $vat = $total * 0.15;
    $grand_total = $total + $vat;

    // حفظ الطلب في جدول orders
    $stmt = $conn->prepare("INSERT INTO orders (user_id, name, email, address, payment_method, total_amount) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssd", $user_id, $name, $email, $address, $payment, $grand_total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // حفظ تفاصيل المنتجات في جدول order_items
    foreach($items as $item){
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['book_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // تفريغ السلة بعد تأكيد الطلب
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>✅ تأكيد الطلب</title>
<style>
body { font-family:"Tajawal", sans-serif; background:#f9f9f9; direction:rtl; }
.container { max-width:700px; margin:50px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#4caf50; margin-bottom:20px;}
p { font-size:16px; color:#333; margin:10px 0;}
.details { margin-top:20px; }
.details strong { color:#1a237e; }
.actions { text-align:center; margin-top:30px;}
.actions a { background:#3f51b5; color:white; padding:10px 20px; border-radius:6px; text-decoration:none; font-weight:bold;}
</style>
</head>
<body>
<div class="container">
    <h2>🎉 تم تأكيد طلبك بنجاح</h2>
    <p>شكراً لك <strong><?= htmlspecialchars($name) ?></strong> على الشراء.</p>
    <p>سيتم إرسال الطلب إلى العنوان: <strong><?= htmlspecialchars($address) ?></strong></p>
    <div class="details">
        <p>طريقة الدفع: <strong><?= htmlspecialchars($payment) ?></strong></p>
        <p>المبلغ الإجمالي: <strong><?= number_format($grand_total,2) ?> ر.س</strong></p>
    </div>
    <div class="actions">
        <a href="index.php">🏠 العودة للرئيسية</a>
    </div>
</div>
</body>
</html>