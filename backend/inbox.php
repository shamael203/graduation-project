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

<?php include 'footer.php'; ?>
