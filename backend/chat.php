<?php
session_start();
require_once 'connect.php';

if (empty($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit; 
}

$me = (int) $_SESSION['user_id'];
$other = isset($_GET['user']) ? (int) $_GET['user'] : 0;

if ($other <= 0) { 
    echo "User not found"; 
    exit; 
}

// ارسال رسالة
if (!empty($_POST['msg'])) {
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, body, created_at)
        VALUES (:s, :r, :b, NOW())
    ");
    $stmt->execute([
        ':s' => $me,
        ':r' => $other,
        ':b' => $_POST['msg']
    ]);
    // رجوع لنفس الصفحة يمنع إعادة الإرسال
    header("Location: chat.php?user=".$other);
    exit;
}

// جلب المحادثة بينهم
$stmt = $pdo->prepare("
    SELECT * FROM messages 
    WHERE (sender_id=:me AND receiver_id=:other)
       OR (sender_id=:other AND receiver_id=:me)
    ORDER BY id ASC
");
$stmt->execute([
    ':me' => $me,
    ':other' => $other
]);
$messages = $stmt->fetchAll();

// جلب اسم المستخدم الآخر
$stmt2 = $pdo->prepare("SELECT name FROM users WHERE id=:id");
$stmt2->execute([':id'=>$other]);
$otherUser = $stmt2->fetch();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>دردشة مع <?= htmlspecialchars($otherUser['name'] ?? '') ?></title>
<style>
body{font-family:Arial;direction:rtl;background:#f1f1f1;padding:20px}
.container{max-width:500px;margin:auto;background:#fff;padding:20px;border-radius:10px}
.you{background:#d1ecf1;padding:10px;border-radius:8px;margin-bottom:10px}
.me{background:#f8d7da;padding:10px;border-radius:8px;margin-bottom:10px}
.msg-time{font-size:12px;color:#555}
</style>
</head>
<body>
<div class="container">

<h3>💬 دردشة مع <?= htmlspecialchars($otherUser['name']) ?></h3>

<?php foreach($messages as $m): ?>
<div class="<?= $m['sender_id']==$me ? 'me' : 'you' ?>">
    <?= nl2br(htmlspecialchars($m['body'])) ?>
    <br><small class="msg-time"><?= $m['created_at'] ?></small>
</div>
<?php endforeach; ?>

<form method="post">
    <textarea name="msg" style="width:100%;height:60px" required></textarea>
    <button>إرسال</button>
</form>

</div>
</body>
</html>