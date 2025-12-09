<?php
session_start();
include 'connect.php';
include 'header.php'; // الهيدر الموحد

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

<?php include 'footer.php'; ?> <!-- الفوتر الموحد -->
