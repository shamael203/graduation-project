<header style="background-color:#007bff; color:#555; padding:12px 30px; display:flex; justify-content:space-between; align-items:center; font-family:'Segoe UI', sans-serif;">
  <div style="font-size:20px; font-weight:bold;">
    📚 BookSwap
  </div>
  <nav style="display:flex; gap:20px; align-items:center;">
    <a href="index.php" style="color:#fff; text-decoration:none;">الرئيسية</a>
    <a href="books.php" style="color:#fff; text-decoration:none;">جميع الكتب</a>

    <a href="add_book.php" style="color:#fff; text-decoration:none;">إضافة كتاب</a>
    <div style="position:relative;">
      <span style="cursor:pointer;">▼ <?= htmlspecialchars($_SESSION['user_name'] ?? 'الحساب') ?></span>
      <!-- تقدر تضيف قائمة منسدلة هنا لاحقًا -->
    </div>
  </nav>
</header>