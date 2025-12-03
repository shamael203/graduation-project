<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {

    $sender_id   = $_SESSION['user_id'];
    $receiver_id = (int) $_POST['receiver_id'];
    $book_id     = (int) $_POST['book_id'];
    $message     = trim($_POST['message']);

    if ($message == '') {
        die("Message cannot be empty.");
    }

    $message_safe = mysqli_real_escape_string($conn, $message);

    $sql = "INSERT INTO messages (sender_id, receiver_id, book_id, message, date)
            VALUES ($sender_id, $receiver_id, $book_id, '$message_safe', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: book_details.php?id=" . $book_id . "&sent=1");
        exit;
    } else {
        echo "Error saving message: " . mysqli_error($conn);
    }

} else {
    echo "Invalid request.";
}
