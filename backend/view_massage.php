<?php
session_start();
include 'connect.php';
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$me = (int) $_SESSION['user_id'];

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) { echo "Message not found"; exit; }

// جلب الرسالة والتأكد أن المستلم هو المستخدم
$stmt = $pdo->prepare("
  SELECT m.*, u.name AS sender_name
  FROM messages m
  JOIN users u ON u.id = m.sender_id
  WHERE m.id = :id AND m.receiver_id = :me
");
$stmt->execute([':id' => $id, ':me' => $me]);
$msg = $stmt->fetch();
if (!$msg) { echo "لا تملك صلاحية عرض هذه الرسالة."; exit; }

// وسمها كمقروء إن لم تكن كذلك
if (!$msg['is_read']) {
  $u = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = :id");
  $u->execute([':id' => $id]);
}
?>
<!doctype html>
<html lang="ar">
<head><meta charset="utf-8"><title>عرض الرسالة</title></head>
<body>
  <h2><?php echo htmlspecialchars($msg['subject'] ?: '(بدون عنوان)'); ?></h2>
  <p>من: <?php echo htmlspecialchars($msg['sender_name']); ?> — <?php echo $msg['created_at']; ?></p>
  <hr>
  <div><?php echo nl2br(htmlspecialchars($msg['body'])); ?></div>
  <p><a href="inbox.php">عودة لصندوق الوارد</a></p>
</body>
</html>