<?php
session_start();
include "connect.php";

// جلب اسم المستخدم والبريد إذا سجل الدخول
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

// التعامل مع البحث
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY id DESC");
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM books ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>📚 استعراض الكتب</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { font-family:"Tajawal", Arial, sans-serif; background:#f0f4ff; margin:0; padding:0; direction:rtl; }

/* الهيدر */
header { background:#3f51b5; color:white; padding:15px 20px; position: sticky; top:0; z-index:100; box-shadow:0 2px 5px rgba(0,0,0,0.2); }
.navbar { display:flex; justify-content: space-between; align-items:center; }
.navbar h1 { margin:0; font-size:22px; }
.nav-links { list-style:none; display:flex; gap:15px; padding:0; margin:0; align-items:center; }
.nav-links li a { color:white; text-decoration:none; padding:6px 12px; border-radius:5px; cursor:pointer; font-weight:bold; }
.nav-links li a:hover { background:#283593; }
.user-dropdown { position: relative; }
.user-dropdown-content { display:none; position:absolute; top:35px; right:0; background:white; color:#333; min-width:180px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2); padding:10px; z-index:100; }
.user-dropdown-content p { margin:0 0 10px 0; text-align:right; font-size:14px; }
.user-dropdown-content a { display:block; text-decoration:none; color:#3f51b5; padding:8px; border-radius:5px; text-align:center; font-weight:bold; margin-bottom:5px; }
.user-dropdown-content a.logout { background:red; color:white; }
.user-dropdown-content a:hover { background:#e0e0ff; }
.user-dropdown:hover .user-dropdown-content { display:block; }

/* المحتوى الرئيسي */
.main { max-width:1200px; margin:40px auto; padding:0 20px; }
h2 { color:#1a237e; text-align:center; margin-bottom:20px; }

/* شريط البحث */
.search-box { max-width:600px; margin:20px auto 30px auto; display:flex; box-shadow:0 2px 6px rgba(0,0,0,0.1); border-radius:8px; overflow:hidden; }
.search-box input { flex:1; padding:12px 15px; border:none; font-size:16px; outline:none; }
.search-box button { padding:12px 18px; background:#3f51b5; color:white; border:none; cursor:pointer; font-size:16px; transition:0.3s; }
.search-box button:hover { background:#2c3e9a; }

/* شبكة الكتب */
.books-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px; }

/* مربعات الكتب */
.book { background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); display:flex; flex-direction:column; transition:0.3s; }
.book:hover { transform: translateY(-3px); box-shadow:0 4px 12px rgba(0,0,0,0.2); }
.book img { width:100%; height:250px; object-fit:cover; }
.book-info { padding:15px; flex:1; display:flex; flex-direction:column; justify-content:space-between; }
.book-info h3 { margin:0 0 8px 0; color:#1a237e; font-size:18px; }
.book-info p { margin:4px 0; font-size:14px; color:#333; }
.book-info .price { color:#e91e63; font-weight:bold; margin-top:10px; }
.book-info button { background:#3f51b5; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer; margin-top:10px; transition:0.3s; }
.book-info button:hover { background:#283593; }

@media(max-width:768px){
    .search-box { flex-direction: column; }
    .search-box button { border-radius:0 0 8px 8px; margin-top:5px; }
}
</style>
</head>
<body>

<header>
    <nav class="navbar">
        <h1>📚 BookSwap</h1>
        <ul class="nav-links">
            <li><a href="index.php">الرئيسية</a></li>
            <li><a href="cart.php">🛒 السلة</a></li>

            <?php if ($username): ?>
            <li class="user-dropdown">
                <a><?= htmlspecialchars($username) ?></a>
                <div class="user-dropdown-content">
                    <p><?= htmlspecialchars($user_email) ?></p>
                    <a class="logout" href="logout.php">تسجيل الخروج</a>
                </div>
            </li>
            <?php else: ?>
            <li><a href="login.php">تسجيل الدخول</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<div class="main">
    <!-- البحث -->
    <form method="GET" action="view_books.php" class="search-box">
        <input type="text" name="search" placeholder="اكتب عنوان الكتاب أو اسم المؤلف..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">بحث</button>
    </form>

    <!-- الكتب -->
    <div class="books-grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php $imageFile = !empty($row['image']) ? "uploads/" . $row['image'] : "uploads/default.png"; ?>
            <div class="book">
                <img src="<?= htmlspecialchars($imageFile) ?>" alt="صورة الكتاب">
                <div class="book-info">
                    <h3>📖 <?= htmlspecialchars($row['title']) ?></h3>
                    <p>المؤلف: <?= htmlspecialchars($row['author']) ?></p>
                    <?php if(!empty($row['edition'])): ?>
                        <p>الطبعة: <?= htmlspecialchars($row['edition']) ?></p>
                    <?php endif; ?>
                    <p class="price">السعر: <?= htmlspecialchars($row['price']) ?> ر.س</p>
                    <button>إضافة إلى السلة</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">لا توجد كتب حالياً.</p>
    <?php endif; ?>
    </div>
</div>

</body>
</html>
