
<?php
session_start();
include 'connect.php';

// تضمين الهيدر
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send'])) {

    $sender_id   = (int) $_SESSION['user_id'];
    $receiver_id = (int) $_POST['receiver_id'];
    $book_id     = (int) $_POST['book_id'];
    $message     = trim($_POST['message']);

    if ($message === '') {
        die("Message cannot be empty.");
    }

    if ($receiver_id <= 0 || $book_id <= 0) {
        die("Invalid receiver or book.");
    }

    // استخدام prepared statement
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, book_id, message, date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $sender_id, $receiver_id, $book_id, $message);

    if ($stmt->execute()) {
        header("Location: book_details.php?id=" . $book_id . "&sent=1");
        exit;
    } else {
        echo "Error saving message.";
        // لو تبغى تشوف الخطأ للتصحيح:
        // echo "Error: " . $stmt->error;
    }

    $stmt->close();

} else {
    echo "Invalid request.";
}
?>

// تضمين الفوتر
include 'footer.php';
?>
