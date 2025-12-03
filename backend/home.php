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

/* Dropdown */
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
.user-btn { cursor:pointer; }

/* محتوى رئيسي */
.main-content { text-align:center; padding:60px 20px; background:#e8eaf6; }
.main-content h2 { color:#1a237e; font-size:32px; margin-bottom:20px; }
.buttons a { text-decoration:none; padding:12px 25px; margin:5px; border-radius:8px; display:inline-block; background:#3f51b5; color:white; }

/* 🔍 تصميم شريط البحث الجديد */
.search-box {
    max-width: 600px;
    margin: 30px auto;
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 14px 50px 14px 15px;
    border-radius: 40px;
    border: 2px solid #b9c4ff;
    font-size: 17px;
    outline: none;
    transition: 0.3s;
}

.search-box input:focus {
    border-color: #3f51b5;
    box-shadow: 0 0 6px rgba(63,81,181,0.4);
}

.search-box button {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: #3f51b5;
    border: none;
    color: white;
    padding: 10px 18px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 15px;
}

.search-box button:hover {
    background:#283593;
}

/* عرض الكتب */
.features { max-width:1100px; margin:40px auto; display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px; padding:0 20px; }
.feature { background:white; padding:15px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; }
.feature img { width:150px; border-radius:8px; }

footer { text-align:center; background:#3f51b5; color:white; padding:15px; margin-top:40px; }
</style>
</head>

<body>

<header>
    <nav class="navbar">
        <h1>📚 BookSwap</h1>
        <ul class="nav-links">
            <li><a href="index.php">الرئيسية</a></li>
            <li><a href="view_books.php">جميع الكتب</a></li>

            <?php if ($username): ?>
            <li><a href="add_book.php">إضافة كتاب</a></li>
            <li class="user-dropdown">
                <a class="user-btn"><?= htmlspecialchars($username) ?> ▼</a>
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
    <p>مرحباً بك في <strong>BookSwap</strong>— المكان الأفضل لشراء وبيع كتبك.</p>

    <form method="GET" action="index.php" class="search-box">
        <input type="text" name="search" placeholder="ابحث عن كتاب أو مؤلف..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">بحث</button>
    </form>
</section>

<section class="features">
    <h2 style="grid-column:1/-1; text-align:center;">أحدث الكتب</h2>

    <?php if ($books->num_rows > 0): ?>
        <?php while($row = $books->fetch_assoc()): ?>
        <div class="feature">
            <img src="uploads/<?= htmlspecialchars($row['image'] ?: 'default.png') ?>">
            <h3>
                <h3>
    <a href="book_details.php?id=<?= $row['id'] ?>" style="text-decoration:none; color:#1a237e;">
        <?= htmlspecialchars($row['title']) ?>
    </a>
</h3>

            <p>المؤلف: <?= htmlspecialchars($row['author']) ?></p>
            <p>السعر: <?= htmlspecialchars($row['price']) ?> ر.س</p>

            <a href="cart.php?id=<?= $row['id'] ?>" style="text-decoration:none;">
                <button style="background:#3f51b5; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer;">
                    إضافة إلى السلة 🛒
                </button>
            </a>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column:1/-1; text-align:center;">لا توجد نتائج.</p>
    <?php endif; ?>
</section>

<footer>
    <p>© 2025 BookSwap. جميع الحقوق محفوظة</p>
</footer>

<script>
const userBtn = document.querySelector('.user-btn');
const dropdown = document.querySelector('.user-dropdown');

if(userBtn){
    userBtn.addEventListener('click', () => {
        dropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) dropdown.classList.remove('active');
    });
}
</script>

</body>
</html>
