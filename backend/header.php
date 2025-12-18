<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// بيانات المستخدم
$username   = $_SESSION['user_name'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;

// عداد السلة + الرسائل
$cart_count = 0;
$unread_count = 0;

if (!empty($_SESSION['user_id'])) {
    include 'connect.php';

    // السلة
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $resultCart = $stmt->get_result();
    if ($row = $resultCart->fetch_assoc()) {
        $cart_count = (int)$row['total'];
    }
    $stmt->close();

    // الرسائل غير المقروءة
    $stmtMsg = $conn->prepare("SELECT COUNT(*) AS total FROM messages WHERE receiver_id=? AND seen=0");
    $stmtMsg->bind_param("i", $_SESSION['user_id']);
    $stmtMsg->execute();
    $resultMsg = $stmtMsg->get_result();
    if ($rowMsg = $resultMsg->fetch_assoc()) {
        $unread_count = (int)$rowMsg['total'];
    }
    $stmtMsg->close();
} elseif (!empty($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BookSwap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {font-family:"Segoe UI", Arial, sans-serif;margin:0;padding:0;background:#f0f4ff;direction:ltr;}
    header {background:#3f51b5;color:white;padding:15px 20px;position:sticky;top:0;z-index:100;}
    .navbar {display:flex;justify-content: space-between;align-items:center;flex-wrap: wrap;}
    .navbar h1 {margin:0;font-size:22px;}
    .nav-links {list-style:none;display:flex;gap:15px;padding:0;margin:0;align-items:center;}
    .nav-links li a {color:white;text-decoration:none;padding:6px 12px;border-radius:5px;font-weight:bold;transition: background 0.3s ease;position:relative;}
    .nav-links li a:hover {background:#283593;}
    .nav-links li a.active {background:#283593;border-radius:5px;color:#fff;}
    .cart-badge {background:red;color:white;font-size:12px;font-weight:bold;border-radius:50%;padding:3px 7px;position:absolute;top:-5px;right:-5px;}
    .user-dropdown { position: relative; }
    .user-btn { cursor:pointer; color:white; font-weight:bold; }
    .user-dropdown-content {display: none;position: absolute;top: 40px; right: 0;background:#3f51b5;min-width:220px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.2);padding:10px;z-index:100;}
    .user-dropdown-content a {display:block;text-decoration:none;color: #fff;padding:8px;border-radius:5px;font-weight:bold;margin-bottom:5px;}
    .user-dropdown-content a:hover { background:#e0e0ff; }
    .user-dropdown-content a.logout { color:red; }
    .login-button {background:#283593;border-radius:20px;padding:6px 12px;}
  </style>
</head>
<body>
<header>
    <nav class="navbar">
        <h1>BookSwap</h1>
        <ul class="nav-links">
            <li><a href="home.php" class="<?= $current_page == 'home.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>

            <?php if ($username): ?>
            <li><a href="chat.php" class="<?= $current_page == 'chat.php' ? 'active' : '' ?>">
                <i class="fas fa-comments"></i> Messages
                <?php if ($unread_count > 0): ?><span class="cart-badge"><?= $unread_count ?></span><?php endif; ?>
            </a></li>
            <?php endif; ?>

            <li><a href="cart.php" class="<?= $current_page == 'cart.php' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Cart
                <?php if ($cart_count > 0): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?>
            </a></li>

            <li><a href="view_books.php" class="<?= $current_page == 'view_books.php' ? 'active' : '' ?>"><i class="fas fa-book"></i> All Books</a></li>

            <?php if ($username): ?>
                <li><a href="add_book.php" class="<?= $current_page == 'add_book.php' ? 'active' : '' ?>"><i class="fas fa-plus"></i> Add Book</a></li>
                <li class="user-dropdown">
                    <a href="#" class="user-btn"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?> ▼</a>
                    <div class="user-dropdown-content">
                        <p style="margin:0 0 10px; font-size:14px; color:#fff;"><i class="fas fa-envelope"></i> <?= htmlspecialchars($user_email) ?></p>
                        <a href="profile.php"><i class="fas fa-id-card"></i> Profile</a>
                        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="login.php" class="login-button <?= $current_page == 'login.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const userBtn = document.querySelector(".user-btn");
  const dropdown = document.querySelector(".user-dropdown-content");
  if(userBtn){
    userBtn.addEventListener("click", function(e) {
      e.preventDefault();
      dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });
  }
  document.addEventListener("click", function(e) {
    if (!e.target.closest(".user-dropdown")) {
      if(dropdown) dropdown.style.display = "none";
    }
  });
});
</script>