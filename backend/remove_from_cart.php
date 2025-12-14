<?php
session_start();
include 'connect.php';

if (isset($_POST['cart_id']) && !empty($_SESSION['user_id'])) {
    $cart_id = intval($_POST['cart_id']);

    // حذف العنصر من السلة
    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
    $stmt->execute();
}

// بعد الحذف يرجع لصفحة السلة
header("Location: cart.php");
exit;
?>