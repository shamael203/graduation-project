<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Book not found");
}

$book_id = (int)$_GET['id'];

// جلب الكتاب والتأكد أنه تابع للمستخدم
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    die("You are not allowed to edit this book");
}

// حفظ التعديل
if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $update = $conn->prepare("
        UPDATE books 
        SET title = ?, author = ?, price = ?, description = ?
        WHERE id = ? AND user_id = ?
    ");
    $update->bind_param("ssd sii", $title, $author, $price, $description, $book_id, $user_id);
    $update->execute();

    header("Location: view_books.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title> Editing the book</title>
<style>
body { direction: rtl; font-family: Arial; background:#f5f5f5; }
.box { width:420px; margin:40px auto; background:#fff; padding:20px; border-radius:10px; }
input, textarea, button { width:100%; margin-bottom:10px; padding:8px; }
button { background:#4CAF50; color:#fff; border:none; }
</style>
</head>
<body>

<div class="box">
<h3> Editing the book </h3>

<form method="POST">
    <label> Book title</label>
    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>

    <label>Author name</label>
    <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>

    <label>Price</label>
    <input type="number" step="0.01" name="price" value="<?= $book['price'] ?>" required>

    <label>Description</label>
    <textarea name="description"><?= htmlspecialchars($book['description']) ?></textarea>

    <button type="submit" name="update">حفظ التعديل</button>
</form>
</div>

</body>
</html>