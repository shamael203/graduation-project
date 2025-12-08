<?php
session_start();
include 'connect.php'; 

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'You must be logged in.']));
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT m.id, m.sender_id, u.username AS sender_name, m.book_id, m.message, m.date
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = $user_id
        ORDER BY m.date DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die(json_encode(['status' => 'error', 'message' => mysqli_error($conn)]));
}

$inbox_messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $inbox_messages[] = [
        'id' => $row['id'],
        'sender_id' => $row['sender_id'],
        'sender_name' => $row['sender_name'],
        'book_id' => $row['book_id'],
        'message' => $row['message'],
        'date' => $row['date']
    ];
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'messages' => $inbox_messages]);
exit;
?>