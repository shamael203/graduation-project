<?php
session_start();
include 'connect.php';

$user_id = $_SESSION['user_id'] ?? 1;

// تحديث الكمية
if (isset($_POST['update'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    $stmt = $conn->prepare("UPDATE cart SET quantity =? WHERE id =?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    $stmt->execute();
}

// حذف عنصر
if (isset($_GET['delete'])) {
    $cart_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE id =?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
}

// جلب البيانات
$sql = "SELECT cart.id AS cart_id, books.title, books.price, books.image, cart.quantity
        FROM cart
        JOIN books ON cart.book_id = books.id
        WHERE cart.user_id =?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>🛒 سلة المشتريات</title>
<style>
body { font-family:"Tajawal", sans-serif; background:#f9f9f9; margin:0; padding:0; direction:rtl;}
.container { max-width:1200px; margin:40px auto; padding:20px; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.1); border-radius:10px; display:flex; gap:30px; flex-wrap:wrap;}
.products, .invoice { flex:1; min-width:300px;}
h2, h3 { color:#3f51b5; text-align:center; margin-bottom:20px;}
.cart-item { display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:15px;}
.cart-item img { width:100px; height:140px; object-fit:cover; border-radius:8px;}
.item-details h4 { margin:0; font-size:18px; color:#1a237e;}
.item-details p { margin:5px 0; font-size:14px; color:#555;}
.item-actions { margin-top:10px;}
input[type=number] { width:60px; padding:5px;}
input[type=submit], .delete-btn, .btn { padding:8px 12px; border:none; border-radius:5px; cursor:pointer; font-weight:bold;}
input[type=submit] { background:#3f51b5; color:white;}
.delete-btn { background:#f44336; color:white; text-decoration:none;}
.invoice { background:#fff; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.invoice p { font-size:15px; margin:10px 0;}
.invoice strong { color:#1a237e;}
.total { font-size:18px; font-weight:bold; color:#e91e63;}
.coupon-form input[type=text] { width:100%; padding:8px; margin-top:5px;}
.coupon-form button { margin-top:10px; background:#3f51b5; color:white;}
.actions { margin-top:30px; display:flex; flex-direction:column; gap:10px;}
.actions a { text-align:center; padding:10px; border-radius:6px; text-decoration:none; font-weight:bold;}
.actions .checkout { background:#4caf50; color:white;}
.actions .back { background:#607d8b; color:white;}
</style>
</head>
<body>

<div class="container">
    <!-- المنتجات -->
    <div class="products">
        <h3>📦 المنتجات</h3>
        <?php
        $total = 0;
        if ($result->num_rows > 0):
            while($row = $result->fetch_assoc()):
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
                $image = !empty($row['image']) ? "uploads/".$row['image'] : "uploads/default.png";
        ?>
        <div class="cart-item">
            <img src="<?= htmlspecialchars($image) ?>" alt="صورة الكتاب">
            <div class="item-details">
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <p>الكمية: <?= $row['quantity'] ?></p>
                <p>السعر: <?= number_format($row['price'], 2) ?> ر.س</p>
                <p>الإجمالي: <?= number_format($subtotal, 2) ?> ر.س</p>
                <div class="item-actions">
                    <form method="POST">
                        <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                        <input type="number" name="quantity" value="<?= $row['quantity'] ?>" min="1">
                        <input type="submit" name="update" value="تحديث">
                    </form>
                    <a class="delete-btn" href="?delete=<?= $row['cart_id'] ?>" onclick="return confirm('هل تريد حذف هذا الكتاب؟')">حذف</a>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
        <p style="text-align:center;">السلة فارغة حالياً.</p>
        <?php endif; ?>
    </div>

    <!-- الفاتورة -->
    <div class="invoice">
        <h3>🧾 تفاصيل الفاتورة</h3>
        <?php
        $vat = $total * 0.15;
        $grand_total = $total + $vat;
        ?>
        <p>المجموع الفرعي (<?= $result->num_rows ?> منتج): <strong><?= number_format($total, 2) ?> ر.س</strong></p>
        <p>ضريبة القيمة المضافة (15%): <strong><?= number_format($vat, 2) ?> ر.س</strong></p>
        <p class="total">المجموع شامل الضريبة: <?= number_format($grand_total, 2) ?> ر.س</p>

        <!-- كوبون الخصم -->
        <form method="POST" class="coupon-form">
            <label for="coupon">🎁 كوبون الخصم:</label>
            <input type="text" name="coupon" id="coupon">
            <button type="submit" name="apply_coupon">إرسال</button>
        </form>

        <!-- الأزرار -->
        <div class="actions">
            <a href="checkout.php" class="checkout">✅ متابعة الشراء</a>
            <a href="index.php" class="back">🏠 العودة للتسوق</a>
        </div>
    </div>
</div>

</body>
</html>