<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit; 
}

$me = (int) $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { 
    echo "Message not found"; 
    exit; 
}

// Fetch the message and ensure the recipient is the user
$stmt = $conn->prepare("
  SELECT m.*, u.name AS sender_name
  FROM messages m
  JOIN users u ON u.id = m.sender_id
  WHERE m.id = ? AND m.receiver_id = ?
");
$stmt->bind_param("ii", $id, $me);
$stmt->execute();
$result = $stmt->get_result();
$msg = $result->fetch_assoc();
$stmt->close();

if (!$msg) { 
    echo "You do not have permission to view this message."; 
    exit; 
}

// Mark as read if not already
if (!$msg['is_read']) {
    $u = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $u->bind_param("i", $id);
    $u->execute();
    $u->close();
}
?>

<?php include 'header.php'; ?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>View Message</title>
</head>
<body>
  <div class="container">
      <h2><?php echo htmlspecialchars($msg['subject'] ?: '(No Subject)'); ?></h2>
      <p>From: <?php echo htmlspecialchars($msg['sender_name']); ?> — <?php echo $msg['created_at']; ?></p>
      <hr>
      <div><?php echo nl2br(htmlspecialchars($msg['body'])); ?></div>
      <p><a href="inbox.php">Back to Inbox</a></p>
  </div>
</body>
</html>

<?php include 'footer.php'; ?>