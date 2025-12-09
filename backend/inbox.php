<?php
session_start();
include 'connect.php'; 

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// جلب الرسائل من قاعدة البيانات
$sql = "SELECT m.id, m.sender_id, u.name AS sender_name, m.book_id, m.message, m.date
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = ?
        ORDER BY m.date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>📥 صندوق الرسائل - BookSwap</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { font-family:"Tajawal", Arial, sans-serif; direction:rtl; background:#f2f6ff; margin:0; padding:0; }
.navbar { display:flex; justify-content: space-between; align-items:center; background:#3f51b5; color:white; padding:15px 20px; position: sticky; top:0; box-shadow:0 2px 5px rgba(0,0,0,0.2); z-index:100; }
.navbar h1 { margin:0; font-size:22px; }
.nav-links { list-style:none; display:flex; gap:15px; padding:0; margin:0; align-items:center; }
.nav-links li a { color:white; text-decoration:none; padding:6px 12px; border-radius:5px; font-weight:bold; }
.nav-links li a:hover { background:#283593; }
.user-dropdown { position: relative; }
.user-dropdown-content { display:none; position:absolute; top:35px; right:0; background:white; color:#333; min-width:180px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2); padding:10px; z-index:100; }
.user-dropdown-content p { margin:0 0 10px 0; text-align:right; font-size:14px; }
.user-dropdown-content a { display:block; text-decoration:none; color:#3f51b5; padding:8px; border-radius:5px; text-align:center; font-weight:bold; margin-bottom:5px; }
.user-dropdown-content a.logout { background:red; color:white; }
.user-dropdown-content a:hover { background:#e0e0ff; }
.user-dropdown:hover .user-dropdown-content { display:block; }

.container { max-width: 800px; margin: 30px auto; background: #fff; padding: 25px 30px; border-radius: 15px; box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
h2 { text-align:center; color:#1a237e; margin-bottom:20px; }
.message-card { background:#f7f8ff; border:1px solid #d6d9ff; padding:15px 20px; border-radius:10px; margin-bottom:15px; transition: transform 0.2s ease, box-shadow 0.2s ease; }
.message-card:hover { transform: translateY(-3px); box-shadow:0 6px 15px rgba(0,0,0,0.1); }
.message-card p { margin:6px 0; font-size:15px; }
.message-card .sender { font-weight:bold; color:#3f51b5; }
.message-card .time { font-size:13px; color:#888; }
footer { background:#3f51b5; color:white; text-align:center; padding:15px 20px; margin-top:40px; border-top-left-radius:8px; border-top-right-radius:8px; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <h2>📥 صندوق الرسائل</h2>

    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message-card">
                <p class="sender">من: <?= htmlspecialchars($msg['sender_name']) ?></p>
                <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                <p class="time">📅 <?= date("d M Y H:i", strtotime($msg['date'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center;">⚠️ لا توجد رسائل حالياً.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
