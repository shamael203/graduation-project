<?php
session_start();
include 'connect.php';

$book_id = isset($_GET['book']) ? (int) $_GET['book'] : 0;
if ($book_id <= 0) {
    die("Invalid book ID.");
}

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    // إذا المستخدم مسجل → أضف للسلة في قاعدة البيانات
    // تحقق إذا الكتاب موجود مسبقًا في السلة
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND book_id=?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        // تحديث الكمية
        $newQty = $row['quantity'] + 1;
        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $update->bind_param("ii", $newQty, $row['id']);
        $update->execute();
        $update->close();
    } else {
        // إضافة جديد
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