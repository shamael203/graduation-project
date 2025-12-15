<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';
include 'connect.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$me = (int) $_SESSION['user_id'];
$other = isset($_GET['user']) ? (int) $_GET['user'] : 0;

// إرسال رسالة
if ($other > 0 && !empty($_POST['msg'])) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, date, seen) VALUES (?, ?, ?, NOW(), 0)");
    $stmt->bind_param("iis", $me, $other, $_POST['msg']);
    $stmt->execute();
    $stmt->close();
    header("Location: chat.php?user=".$other);
    exit;
}

// جلب الرسائل
$messages = [];
$otherUser = null;
if ($other > 0) {
    $conn->query("UPDATE messages SET seen=1 WHERE receiver_id=$me AND sender_id=$other");

    $stmt = $conn->prepare("SELECT m.*, u.name 
                            FROM messages m
                            JOIN users u ON u.id = m.sender_id
                            WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?)
                            ORDER BY m.id ASC");
    $stmt->bind_param("iiii", $me, $other, $other, $me);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $stmt2 = $conn->prepare("SELECT u.name FROM users u WHERE u.id=?");
    $stmt2->bind_param("i", $other);
    $stmt2->execute();
    $otherUser = $stmt2->get_result()->fetch_assoc();
    $stmt2->close();
}

// قائمة المحادثات
$sidebar = $conn->query("
    SELECT u.id, u.name,
           (SELECT COUNT(*) FROM messages 
            WHERE receiver_id=$me AND sender_id=u.id AND seen=0) AS unread_count,
           (SELECT message
            FROM messages 
            WHERE (sender_id=u.id AND receiver_id=$me) OR (sender_id=$me AND receiver_id=u.id)
            ORDER BY id DESC LIMIT 1) AS last_message
    FROM users u 
    WHERE u.id != $me
");
?>
<style>
body {
  margin:0;
  font-family:Arial,sans-serif;
  background:#fff;
  overflow:hidden; /* يمنع ظهور Scroll للصفحة */
}
.chat-layout {
  display:flex;
  height:calc(100vh - 120px); /* ناقص الهيدر والفوتر */
  width:100%;
}
.chat-sidebar {
  width:250px;
  background:#f9f9f9;
  padding:20px;
  border-right:1px solid #ddd;
  overflow-y:auto;
}
.chat-sidebar h3 {margin-bottom:10px;font-size:16px;color:#333;}
.chat-sidebar ul {list-style:none;padding:0;margin:0;}
.chat-sidebar li {border-bottom:1px solid #eee;padding:10px 0;}
.chat-sidebar a {text-decoration:none;color:#007bff;font-weight:bold;}
.unread-badge {background:#007bff;color:#fff;font-size:12px;border-radius:50%;padding:4px 8px;}
.active-chat {background:#e3f2fd;border-radius:8px;}
.last-message {font-size:13px;color:#555;margin-top:4px;}
.avatar {width:32px;height:32px;border-radius:50%;background:#007bff;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;margin-right:8px;}
.chat-main {flex:1;display:flex;flex-direction:column;}
.chat-header {background:#007bff;color:white;padding:16px;}
.chat-window {
  flex:1;
  padding:16px;
  overflow-y:auto; /* Scroll داخلي فقط */
  display:flex;
  flex-direction:column;
  gap:10px;
}
.message {max-width:70%;padding:10px 14px;border-radius:16px;font-size:14px;}
.message.user {align-self:flex-end;background:#dcf8c6;}
.message.other {align-self:flex-start;background:#eee;}
.time {font-size:11px;color:#888;margin-top:4px;display:block;}
.chat-input {display:flex;padding:10px;border-top:1px solid #ddd;background:#fff;}
.chat-input input {flex:1;padding:10px;border-radius:999px;border:1px solid #ccc;}
.chat-input button {padding:10px 20px;border-radius:999px;background:#007bff;color:white;border:none;font-weight:bold;}
</style>

<div class="chat-layout">
  <!-- Sidebar -->
  <aside class="chat-sidebar">
    <h3>Conversations</h3>
    <ul>
      <?php while ($user = $sidebar->fetch_assoc()): ?>
        <?php $firstLetter = strtoupper(substr($user['name'],0,1)); ?>
        <li class="<?= $user['id'] == $other ? 'active-chat' : '' ?>">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;">
              <div class="avatar"><?= $firstLetter ?></div>
              <div>
                <a href="chat.php?user=<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></a>
                <div class="last-message"><?= htmlspecialchars($user['last_message'] ?? '') ?></div>
              </div>
            </div>
            <?php if ($user['unread_count'] > 0): ?>
              <span class="unread-badge"><?= (int)$user['unread_count'] ?></span>
            <?php endif; ?>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>
  </aside>

  <!-- Chat Window -->
  <main class="chat-main">
    <?php if ($other > 0): ?>
      <header class="chat-header">
        Chat with <?= htmlspecialchars($otherUser['name']) ?>
      </header>
      <div class="chat-window">
        <?php foreach ($messages as $msg): ?>
          <div class="message <?= $msg['sender_id'] == $me ? 'user' : 'other' ?>">
            <?= htmlspecialchars($msg['message']) ?>
            <span class="time"><?= date('H:i', strtotime($msg['date'])) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <form class="chat-input" method="post">
        <input type="text" name="msg" placeholder="Type your message..." required />
        <button type="submit">Send</button>
      </form>
    <?php else: ?>
      <div style="flex:1;display:flex;align-items:center;justify-content:center;color:#666;">
        Select a conversation from the left
      </div>
    <?php endif; ?>
  </main>
</div>

<?php include 'footer.php'; ?>

<script>
  // ينزل تلقائيًا لآخر رسالة داخل نافذة الدردشة فقط
  function scrollToBottom() {
    var chatWindow = document.querySelector('.chat-window');
    if (chatWindow) {
      chatWindow.scrollTop = chatWindow.scrollHeight;
    }
  }

  window.onload = scrollToBottom;

  document.querySelector('.chat-input').addEventListener('submit', function() {
    setTimeout(scrollToBottom, 100);
  });
</script>
</body>
</html>
