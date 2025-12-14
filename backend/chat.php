<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connect.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$me = (int) $_SESSION['user_id'];
$other = isset($_GET['user']) ? (int) $_GET['user'] : 0;
$book_id = isset($_GET['book']) ? (int) $_GET['book'] : null;

if ($other <= 0) {
    $conn->query("UPDATE messages SET seen=1 WHERE receiver_id=$me");
}

include 'header.php';

if ($other > 0) {
    if (!empty($_POST['msg'])) {
        $book_id = $book_id ?: NULL;
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, date, book_id, seen) VALUES (?, ?, ?, NOW(), ?, 0)");
        $stmt->bind_param("iisi", $me, $other, $_POST['msg'], $book_id);
        $stmt->execute();
        $stmt->close();
        header("Location: chat.php?user=".$other.($book_id ? "&book=".$book_id : ""));
        exit;
    }

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

// قائمة المحادثات مع آخر رسالة + اسم المرسل + عداد غير مقروء
$sidebar = $conn->query("
    SELECT u.id, u.name,
           (SELECT COUNT(*) FROM messages 
            WHERE receiver_id=$me AND sender_id=u.id AND seen=0) AS unread_count,
           (SELECT CONCAT(
                CASE WHEN sender_id=$me THEN 'Me: ' ELSE (SELECT name FROM users WHERE id=sender_id) END,
                ' ',
                message
            )
            FROM messages 
            WHERE (sender_id=u.id AND receiver_id=$me) OR (sender_id=$me AND receiver_id=u.id)
            ORDER BY id DESC LIMIT 1) AS last_message
    FROM users u 
    WHERE u.id != $me
");
?>

<style>
body {margin:0;font-family:Arial,sans-serif;direction:ltr;background:#f4f5f7;}
.chat-layout {display:flex;height:calc(100vh - 120px);}
.chat-sidebar {width:250px;background:#f0f0f0;padding:20px;border-right:1px solid #ddd;overflow-y:auto;}
.chat-sidebar h3 {margin-bottom:10px;font-size:16px;color:#333;}
.chat-sidebar ul {list-style:none;padding:0;}
.chat-sidebar li {border-bottom:1px solid #ccc;padding:10px 0;}
.chat-sidebar a {text-decoration:none;color:#007bff;font-weight:bold;}
.unread-badge {
  background:#ccc;
  color:#000;
  font-size:13px;
  font-weight:bold;
  border-radius:50%;
  padding:6px 10px;
  min-width:28px;
  text-align:center;
  display:inline-block;
}
.active-chat {background:#e3f2fd;border-radius:8px;}
.last-message {font-size:13px;color:#555;margin-top:4px;}
.chat-main {flex:1;display:flex;flex-direction:column;background:#fff;}
.chat-header {background:#007bff;color:white;padding:16px;display:flex;justify-content:space-between;align-items:center;}
.chat-window {flex:1;padding:16px;overflow-y:auto;background:#f4f5f7;display:flex;flex-direction:column;gap:10px;}
.message {max-width:70%;padding:10px 14px;border-radius:16px;font-size:14px;display:flex;gap:8px;}
.bubble {padding:10px;border-radius:12px;}
.message.user {align-self:flex-end;flex-direction:row-reverse;}
.message.user .bubble {background:#dcf8c6;}
.message.seller {align-self:flex-start;}
.message.seller .bubble {background:#fff;border:1px solid #dde1e7;}
.time {font-size:11px;color:#888;margin-top:4px;display:block;}
.chat-input {display:flex;padding:10px;border-top:1px solid #dde1e7;background:#fff;}
.chat-input input {flex:1;padding:10px;border-radius:999px;border:1px solid #ccc;}
.chat-input button {padding:10px 20px;border-radius:999px;background:#007bff;color:white;border:none;font-weight:bold;}
</style>

<div class="chat-layout">
  <!-- Sidebar -->
  <aside class="chat-sidebar">
    <h3>Conversations</h3>
    <hr>
    <ul>
      <?php while ($user = $sidebar->fetch_assoc()): ?>
        <li class="<?= $user['id'] == $other ? 'active-chat' : '' ?>">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
              <a href="chat.php?user=<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></a>
              <div class="last-message"><?= htmlspecialchars($user['last_message'] ?? '') ?></div>
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
        <div>Chat with <?= htmlspecialchars($otherUser['name']) ?></div>
      </header>

      <div class="chat-window">
        <?php foreach ($messages as $msg): ?>
          <div class="message <?= $msg['sender_id'] == $me ? 'user' : 'seller' ?>">
            <div>
              <div class="bubble">
                <?php if ($msg['sender_id'] == $me): ?>
                  <strong>Me:</strong>
                <?php else: ?>
                  <strong><?= htmlspecialchars($msg['name']) ?>:</strong>
                <?php endif; ?>
                <?= htmlspecialchars($msg['message']) ?>
              </div>
              <span class="time"><?= date('H:i', strtotime($msg['date'])) ?></span>
            </div>
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