<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit; 
}

$me = (int) $_SESSION['user_id'];
$other = isset($_GET['user']) ? (int) $_GET['user'] : 0;
$book_id = isset($_GET['book']) ? (int) $_GET['book'] : null;

if ($other <= 0) { 
    echo "User not found"; 
    exit; 
}

// Send a message
if (!empty($_POST['msg'])) {
    if ($book_id) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, date, book_id) VALUES (?, ?, ?, NOW(), ?)");
        $stmt->bind_param("iisi", $me, $other, $_POST['msg'], $book_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $me, $other, $_POST['msg']);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: chat.php?user=".$other.($book_id ? "&book=".$book_id : ""));
    exit;
}

// Fetch conversation
$stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?) ORDER BY id ASC");
$stmt->bind_param("iiii", $me, $other, $other, $me);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch other user's name
$stmt2 = $conn->prepare("SELECT name FROM users WHERE id=?");
$stmt2->bind_param("i", $other);
$stmt2->execute();
$otherUser = $stmt2->get_result()->fetch_assoc();
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Chat with <?= htmlspecialchars($otherUser['name']) ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f5f7;
      display: flex;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
    }
    .chat-container {
      background-color: #ffffff;
      width: 100%;
      max-width: 700px;
      display: flex;
      flex-direction: column;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      overflow: hidden;
      margin: 16px;
    }
    .chat-header {
      background-color: #007bff;
      color: #ffffff;
      padding: 16px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .chat-header-title {
      font-size: 18px;
      font-weight: 700;
    }
    .back-link {
      color: #ffffff;
      text-decoration: none;
      font-size: 14px;
    }
    .chat-window {
      padding: 16px;
      background-color: #f4f5f7;
      flex: 1;
      overflow-y: auto;
    }
    .message {
      margin-bottom: 12px;
      display: flex;
      flex-direction: column;
      max-width: 80%;
    }
    .message.seller {
      align-self: flex-start;
    }
    .message.user {
      align-self: flex-end;
      text-align: right;
    }
    .bubble {
      padding: 10px 14px;
      border-radius: 16px;
      font-size: 14px;
      line-height: 1.4;
    }
    .message.seller .bubble {
      background-color: #ffffff;
      border: 1px solid #dde1e7;
    }
    .message.user .bubble {
      background-color: #007bff;
      color: #ffffff;
    }
    .time {
      font-size: 11px;
      color: #888;
      margin-top: 4px;
    }
    .chat-input {
      display: flex;
      border-top: 1px solid #dde1e7;
      padding: 10px;
      background-color: #ffffff;
      gap: 8px;
    }
    .chat-input input {
      flex: 1;
      padding: 10px 12px;
      border-radius: 999px;
      border: 1px solid #ccd0d5;
      font-size: 14px;
      outline: none;
    }
    .chat-input button {
      padding: 10px 20px;
      border-radius: 999px;
      border: none;
      background-color: #007bff;
      color: #ffffff;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
    }
    .chat-input button:hover {
      background-color: #005fd1;
    }
  </style>
</head>
<body>
  <div class="chat-container">
    <header class="chat-header">
      <div class="chat-header-title">Chat with <?= htmlspecialchars($otherUser['name']) ?></div>
      <a href="home.php" class="back-link">← Back</a>
    </header>

    <div class="chat-window">
      <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
          <div class="message <?= $msg['sender_id'] == $me ? 'user' : 'seller' ?>">
            <div class="bubble"><?= htmlspecialchars($msg['message']) ?></div>
            <div class="time">
              <?= $msg['sender_id'] == $me ? 'You' : htmlspecialchars($otherUser['name']) ?>
              • <?= date('H:i', strtotime($msg['date'])) ?>
              <?php if (!empty($msg['book_id'])): ?>
                • Book ID: <?= htmlspecialchars($msg['book_id']) ?>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No messages yet.</p>
      <?php endif; ?>
    </div>

    <form class="chat-input" method="post">
      <input
        type="text"
        name="msg"
        placeholder="Type your message..."
        autocomplete="off"
        required
      />
      <button type="submit">Send</button>
    </form>
  </div>
</body>
</html>