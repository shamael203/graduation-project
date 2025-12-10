<?php
// تشغيل الجلسة بشكل آمن بدون أخطاء
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>BookSwap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="margin:0; padding:0; font-family:'Segoe UI', sans-serif; direction:rtl;">

<header style="background-color:#007bff; color:#fff; padding:12px 30px;
               display:flex; justify-content:space-between; align-items:center;">

  <div style="font-size:20px; font-weight:bold;">
    📚 BookSwap
  </div>

  <nav style="display:flex; gap:20px; align-items:center;">
    <a href="index.php" style="color:#fff; text-decoration:none;">الرئيسية</a>
    <a href="books.php" style="color:#fff; text-decoration:none;">جميع الكتب</a>
    <a href="add_book.php" style="color:#fff; text-decoration:none;">إضافة كتاب</a>

    <div style="position:relative;">
      <span style="cursor:pointer; color:#fff;">
        ▼ <?= htmlspecialchars($_SESSION['user_name'] ?? 'الحساب') ?>
      </span>
      <!-- تقدر تضيف قائمة منسدلة هنا لاحقًا -->
    </div>
  </nav>

</header>