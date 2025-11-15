<?php
session_start();
include 'connect.php';

// جلب اسم المستخدم والبريد إذا سجل الدخول
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;

// التعامل مع البحث
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM books 
            WHERE title LIKE ? OR author LIKE ?
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $books = $stmt->get_result();
} else {
    $sql = "SELECT * FROM books ORDER BY id DESC LIMIT 6";
    $books = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BookSwap</title>
<style>
body { font-family:"Tajawal", Arial, sans-serif; margin:0; padding:0; background:#f0f4ff; direction: rtl; }
header { background:#3f51b5; color:white; padding:15px 20px; }
.navbar { display:flex; justify-content: space-between; align-items:center; flex-wrap: wrap; }
.navbar h1 { margin:0; }
.nav-links { list-style:none; display:flex; gap:15px; padding:0; margin:0; align-items:center; position: relative; }
.nav-links li a { color:white; text-decoration:none; padding:5px 10px; border-radius:5px; cursor:pointer; }
.nav-links li a:hover { background:#283593; }

/* Dropdown المستخدم */
.user-dropdown { position: relative; }
.user-dropdown-content {
    display: none;
    position: absolute;
    top: 40px;
    right: 0;
    background:white;
    color:#333;
    min-width:200px;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,0.2);
    padding:10px;
    z-index:100;
}
.user-dropdown.active .user-dropdown-content { display:block; }
.user-dropdown-content p { margin:0 0 10px 0; text-align:right; }
.user-dropdown-content a { display:block; text-decoration:none; color:white; padding:8px; border-radius:5px; text-align:center; background:red; }
.user-btn { cursor:pointer; color:white; text-decoration:none; padding:5px 10px; border-radius:5px; }

/* باقي التصميم */
.main-content { text-align:center; padding:60px 20px; background:#e8eaf6; }
.main-content h2 { color:#1a237e; font-size:32px; margin-bottom:20px; }
.main-content p { font-size:18px; max-width:700px; margin:auto; margin-bottom:30px; }
.buttons a { text-decoration:none; padding:12px 25px; margin:5px; border-radius:8px; font-weight:bold; display:inline-block; }
.btn { background:#3f51b5; color:white; }
.search-box { max-width:700px; margin:20px auto; display:flex; }
.search-box input { flex:1; padding:12px; border-radius:8px 0 0 8px; border:1px solid #c7c7c7; font-size:16px; }
.search-box button { padding:12px 18px; background:#3f51b5; color:white; border:none; border-radius:0 8px 8px 0; cursor:pointer; font-size:16px; }
.search-box button:hover { background:#2c3e9a; }
.features { max-width:1100px; margin:40px auto; display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px; padding:0 20px; }
.feature { background:white; padding:15px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; }
.feature img { width:150px; height:auto; border-radius:8px; margin-bottom:10px; }
.feature h3 { margin:10px 0 5px 0; color:#1a237e; }
.feature p { margin:4px 0; }
footer { text-align:center; background:#3f51b5; color:white; padding:15px; margin-top:40px; }
</style>
</head>
<body>

<header>
    <nav class="navbar">
        <h1>📚 BookSwap</h1>
        <ul class="nav-links">
            <li><a href="index.php" class="active">الرئيسية</a></li>
            <li><a href="view_books.php">جميع الكتب</a></li>

            <?php if ($username): ?>
            <li><a href="add_book.php">إضافة كتاب</a></li>
            <li class="user-dropdown">
                <a href="javascript:void(0);" class="user-btn"><?= htmlspecialchars($username) ?> ▼</a>
                <div class="user-dropdown-content">
                    <p><?= htmlspecialchars($user_email) ?></p>
                    <a href="logout.php">تسجيل الخروج</a>
                </div>
            </li>
            <?php else: ?>
            <li><a href="login.php">تسجيل الدخول</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<section class="main-content">
    <h2>تبادل الكتب بسهولة</h2>
    <p>مرحباً بك في <strong>BookSwap</strong> — أسهل طريقة لشراء وبيع كتبك المفضلة. اكتشف مجموعة واسعة من الكتب أو شارك كتبك لتجد لها مالك جديد.</p>
    <div class="buttons">
        <a href="view_books.php" class="btn">استعرض الكتب</a>
    </div>

    <!-- شريط البحث -->
    <form method="GET" action="index.php" class="search-box">
        <input type="text" name="search" placeholder="اكتب عنوان الكتاب أو اسم المؤلف..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">بحث</button>
    </form>
</section>

<section class="features">
    <h2 style="grid-column:1/-1; text-align:center; margin-bottom:20px;">أحدث الكتب</h2>

    <?php if ($books->num_rows > 0): ?>
        <?php while($row = $books->fetch_assoc()): ?>
            <div class="feature">
                <?php if (!empty($row['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="صورة الكتاب">
                <?php else: ?>
                    <img src="uploads/default.png" alt="لا توجد صورة">
                <?php endif; ?>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p>المؤلف: <?= htmlspecialchars($row['author']) ?></p>
                <p>السعر: <?= htmlspecialchars($row['price']) ?> ر.س</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column:1/-1; text-align:center;">لا توجد كتب حالياً.</p>
    <?php endif; ?>
</section>

<footer>
    <p>© 2025 BookSwap. جميع الحقوق محفوظة</p>
</footer>

<script>
const userBtn = document.querySelector('.user-btn');
const userDropdown = document.querySelector('.user-dropdown');

if(userBtn){
    userBtn.addEventListener('click', () => {
        userDropdown.classList.toggle('active');
    });

    // لإغلاق القائمة عند الضغط خارجها
    document.addEventListener('click', function(event) {
        if (!userDropdown.contains(event.target)) {
            userDropdown.classList.remove('active');
        }
    });
}
</script>

</body>
</html>
