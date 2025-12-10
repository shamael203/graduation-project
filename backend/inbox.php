
<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$me = (int) $_SESSION['user_id'];

// جلب آخر رسالة مع كل مستخدم
$sql = "
    SELECT u.id, u.name, m.message, m.date
    FROM users u
    JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE u.id != ?
    AND m.id IN (
        SELECT MAX(id) FROM messages 
        WHERE sender_id = u.id OR receiver_id = u.id
    )
    ORDER BY m.date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $me);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>صندوق الرسائل</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #ece5dd;
      margin: 0;
      padding: 0;
    }
    .inbox-container {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      min-height: 100vh;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .inbox-header {
      background: #075e54;
      color: #fff;
      padding: 15px;
      font-size: 18px;
      font-weight: bold;
    }
    .chat-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .chat-list li {
      border-bottom: 1px solid #ddd;
    }
    .chat-list a {
      display: flex;
      justify-content: space-between;
      padding: 15px;
      text-decoration: none;
      color: #000;
    }
    .chat-list a:hover {
      background: #f0f0f0;
    }
    .chat-info {
      flex: 1;
    }
    .chat-name {
      font-weight: bold;
    }
    .chat-message {
      font-size: 14px;
      color: #555;
    }
    .chat-date {
      font-size: 12px;
      color: #888;
      margin-left: 10px;
    }
  </style>
</head>
<body>
  <div class="inbox-container">

<?php include 'header.php'; ?>

<div class="inbox-container">
    <div class="inbox-header">📩 الرسائل</div>
    <ul class="chat-list">
      <?php while($row = $result->fetch_assoc()): ?>
        <li>
          <a href="chat.php?user=<?= $row['id'] ?>">
            <div class="chat-info">
              <div class="chat-name"><?= htmlspecialchars($row['name']) ?></div>
              <div class="chat-message"><?= htmlspecialchars($row['message']) ?></div>
            </div>
            <div class="chat-date"><?= date("H:i", strtotime($row['date'])) ?></div>
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
</body>
</html>
</div>

<?php include 'footer.php'; ?>