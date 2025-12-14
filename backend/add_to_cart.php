<?php
session_start();
include 'connect.php';

// استلام book_id من POST أو GET (للدعم الكامل)
$book_id = 0;
if (isset($_POST['book_id'])) {
    $book_id = (int) $_POST['book_id'];
} elseif (isset($_GET['book'])) {
    $book_id = (int) $_GET['book'];
}

if ($book_id <= 0) {
    die("Invalid book ID.");
}

// التحقق من المستخدم
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    // إذا المستخدم مسجل → أضف للسلة في قاعدة البيانات
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND book_id=?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // تحديث الكمية إذا الكتاب موجود مسبقًا
        $newQty = $row['quantity'] + 1;
        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $update->bind_param("ii", $newQty, $row['id']);
        $update->execute();
        $update->close();
    } else {
        // إضافة كتاب جديد للسلة
        $insert = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, 1)");
        $insert->bind_param("ii", $user_id, $book_id);
        $insert->execute();
        $insert->close();
    }

    $stmt->close();
} else {
    // إذا ضيف → أضف للسلة في السيشن
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id] += 1;
    } else {
        $_SESSION['cart'][$book_id] = 1;
    }
}

// بعد الإضافة → رجوع للسلة
header("Location: cart.php");
exit;