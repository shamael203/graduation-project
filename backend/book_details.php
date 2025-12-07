<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php'; // نفس الملف اللي تستخدمه في home.php

echo "<pre>";
echo "DEBUG MODE\n";
echo "GET:\n";
var_dump($_GET);
echo "</pre>";

if (!isset($_GET['id'])) {
    die("No ID in URL.");
}

$book_id = (int) $_GET['id'];
echo "ID from URL = " . $book_id . "<br>";

// كم كتاب موجود في الجدول ككل
$sql_all = "SELECT * FROM books";
$res_all = $conn->query($sql_all);
echo "Total books in DB: " . $res_all->num_rows . "<br>";

// لو عمود رقم الكتاب اسمه id
$sql = "SELECT * FROM books WHERE id = ?";
/*
// لو عندك العمود اسمه book_id بدل id استخدم هذا بدل اللي فوق:
$sql = "SELECT * FROM books WHERE book_id = ?";
*/

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

echo "Rows with this ID: " . $result->num_rows . "<br>";

if (!$book) {
    die("Book not found from DB.");
}

echo "<h2>Book Details</h2>";
echo "<p>Title: " . htmlspecialchars($book['title']) . "</p>";
echo "<p>Author: " . htmlspecialchars($book['author']) . "</p>";
echo "<p>Price: " . htmlspecialchars($book['price']) . "</p>";

// نفترض عندك عمود user_id لصاحب الكتاب
$seller_id = isset($book['user_id']) ? $book['user_id'] : 1;
?>
<h3>Send a message to the seller</h3>

<form action="send_message.php" method="post">
    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
    <input type="hidden" name="receiver_id" value="<?php echo $seller_id; ?>">

    <label for="message">Message:</label><br>
    <textarea name="message" id="message" rows="4" cols="50" required></textarea><br><br>

    <button type="submit" name="send">Send</button>
</form>