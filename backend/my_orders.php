<?php
session_start();
include 'connect.php';

// التأكد من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// جلب طلبات المستخدم
$stmt = $conn->prepare("
    SELECT * FROM payments 
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>طلباتي</title>
<style>
body { direction: rtl; font-family: Arial; background:#f4f6f8; }
.container { width:80%; margin:40px auto; background:#fff; padding:20px; border-radius:10px; }
table { width:100%; border-collapse: collapse; }
th, td { padding:10px; border-bottom:1px solid #ddd; text-align:center; }
th { background:#3f51b5; color:#fff; }
.empty { text-align:center; color:#777; }
</style>
</head>
<body>

<div class="container">
<h2>📦 طلباتي</h2>

<?php if ($result->num_rows > 0): ?>
<table>
    <tr>
        <th>طريقة الدفع</th>
        <th>شركة الشحن</th>
        <th>الموقع</th>
        <th>الهاتف</th>
        <th>تاريخ الطلب</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['payment_method']) ?></td>
        <td><?= htmlspecialchars($row['shipping_company']) ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= $row['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p class="empty">لا توجد طلبات حتى الآن.</p>
<?php endif; ?>

</div>

</body>
</html>