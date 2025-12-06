<?php
session_start();
include 'connect.php';

// بيانات المستخدم
$username   = $_SESSION['user_name'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;
$user_id    = $_SESSION['user_id'] ?? null;

// البحث
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $books = $stmt->get_result();
} else {
    $sql = "SELECT * FROM books ORDER BY id DESC LIMIT 12";
    $books = $conn->query($sql);
}

// رسائل المستخدم إذا مسجل دخول
$messages = [];
if ($user_id) {
    $msg_sql = "SELECT * FROM messages WHERE receiver_id = ? ORDER BY date DESC LIMIT 3";
    $msg_stmt = $conn->prepare($msg_sql);
    $msg_stmt->bind_param("i", $user_id);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();
}

// إضافة للسلة (تعمل بدون تسجيل دخول)
if (isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);
    $quantity = 1;

    if ($user_id) {
        // حفظ في قاعدة البيانات
        $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id=? AND book_id=?");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
        $exists = $stmt->get_result();

        if ($exists->num_rows > 0) {
            $row = $exists->fetch_assoc();
            $cart_id = $row['id'];
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id=?");
            $stmt->bind_param("i", $cart_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $book_id, $quantity);
            $stmt->execute();
        }
    } else {
        // حفظ في Session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$book_id])) {
            $_SESSION['cart'][$book_id] += 1;
        } else {
            $_SESSION['cart'][$book_id] = 1;
        }
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BookSwap - الرئيسية</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family:"Tajawal", Arial, sans-serif; margin:0; padding:0; background:#f0f4ff; direction: rtl; }
header { background:#3f51b5; color:white; padding:15px 20px; position:sticky; top:0; z-index:100; }
.navbar { display:flex; justify-content: space-between; align-items:center; flex-wrap: wrap; }
.navbar h1 { margin:0; font-size:22px; }
.nav-links { list-style:none; display:flex; gap:15px; padding:0; margin:0; align-items:center; position: relative; }
.nav-links li a { color:white; text-decoration:none; padding:6px 12px; border-radius:5px; cursor:pointer; font-weight:bold; }
.nav-links li a:hover { background:#283593; }

/* Dropdown */
.user-dropdown { position: relative; }
.user-btn { cursor:pointer; color:white; text-decoration:none; font-weight:bold; }
.user-dropdown-content {
    display: none;
    position: absolute;
    top: 40px; right: 0;
    background:#3f51b5; color:#333; min-width:220px;
    border-radius:8px; box-shadow:0 4px 10px rgba(255, 255, 255, 0.2);
    padding:10px; z-index:100;
}
.user-dropdown.active .user-dropdown-content { display:block; }
.user-dropdown-content a {
    display:block; text-decoration:none; color: #fff;;
    padding:8px; border-radius:5px; font-weight:bold; margin-bottom:5px;
}
.user-dropdown-content a:hover { background:#e0e0ff; }
.user-dropdown-content a.logout { color:red; }
.user-dropdown-content i { margin-left:8px; }

/* الصفحة الرئيسية */
.main-content { text-align:center; padding:60px 20px; background:#e8eaf6; }
.main-content h2 { color:#1a237e; font-size:32px; margin-bottom:20px; }

/* بحث */
.search-box { max-width: 600px; margin: 30px auto; position: relative; }
.search-box input {
    width: 100%; padding: 14px 50px 14px 15px; border-radius: 40px;
    border: 2px solid #b9c4ff; font-size: 17px; outline: none; transition: 0.3s;
}
.search-box input:focus { border-color: #3f51b5; box-shadow: 0 0 6px rgba(63,81,181,0.4); }
.search-box button {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    background: #3f51b5; border: none; color: white; padding: 10px 18px;
    border-radius: 30px; cursor: pointer; font-size: 15px;
}
.search-box button:hover { background:#283593; }

/* الكتب */
.features { max-width:1100px; margin:40px auto; display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px; padding:0 20px; }
.feature { background:white; padding:15px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; display:flex; flex-direction:column; gap:10px; }
.feature img { width:150px; height:200px; object-fit:cover; border-radius:8px; margin:0 auto; }
.feature h3 { margin:5px 0; color:#1a237e; font-size:18px; }
.feature p { margin:2px 0; color:#333; font-size:14px; }
.feature .price { color:#e91e63; font-weight:bold; }
.feature .btn { background:#3f51b5; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer; font-weight:bold; }
.feature .btn:hover { background:#283593; }

/* زر سلة عائم */
.floating-cart {
    position: fixed; left: 20px; bottom: 20px; background:#ff9800; color:white;
    padding: 12px 16px; border-radius:30px; text-decoration:none; font-weight:bold;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
}
.floating-cart:hover { background:#fb8c00; }

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
            <li><a href="cart.php">🛒 السلة</a></li>
            <?php if ($username): ?>
                <li><a href="add_book.php">إضافة كتاب</a></li>
                <li class="user-dropdown">
                    <a href="#" class="user-btn">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($username) ?> ▼
                    </a>
                    <div class="user-dropdown-content">
                        <p style="margin:0 0 10px; font-size:14px; color:#555;">
                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($user_email) ?>
                        </p>
                        <a href="profile.php"><i class="fas fa-id-card"></i> الملف الشخصي</a>
                        <a href="messages.php"><i class="fas fa-comments"></i> الرسائل</a>
                        <a href="cart.php"><i class="fas fa-shopping-cart"></i> السلة</a>
                        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                    </div>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php" class="login-button" style="background:#283593; border-radius:20px; padding:6px 12px;">
                        <i class="fas fa-user"></i> تسجيل الدخول
                    </a>
                </li>
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

<?php if ($user_id): ?>
<div class="messages-box">
    <h3>📩 آخر رسائلك</h3>
    <?php if ($messages->num_rows == 0): ?>
        <p>لا توجد رسائل.</p>
    <?php else: ?>
        <?php while($msg = $messages->fetch_assoc()): ?>
            <div class="msg-item">
                <strong><?= htmlspecialchars($msg['message']) ?></strong><br>
                <small><?= htmlspecialchars($msg['date']) ?></small>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
    <a href="messages.php" style="color:#3f51b5;">عرض جميع الرسائل →</a>
</div>
<?php endif; ?>

<section class="features">
    <h2 style="grid-column:1/-1; text-align:center;">أحدث الكتب</h2>

    <?php if ($books && $books->num_rows > 0): ?>
        <?php while($row = $books->fetch_assoc()): ?>
            <?php $imageFile = !empty($row['image']) ? "uploads/".$row['image'] : "uploads/default.png"; ?>
            <div class="feature">
                <img src="<?= htmlspecialchars($imageFile) ?>" alt="صورة الكتاب">
                <h3>
                    <a href="book_details.php?id=<?= (int)$row['id'] ?>" style="text-decoration:none; color:#1a237e;">
                        <?= htmlspecialchars($row['title']) ?>
                    </a>
                </h3>
                <p>المؤلف: <?= htmlspecialchars($row['author']) ?></p>
                <?php if(!empty($row['edition'])): ?>
                    <p>الطبعة: <?= htmlspecialchars($row['edition']) ?></p>
                <?php endif; ?>
                <p class="price">السعر: <?= number_format($row['price'],2) ?> ر.س</p>

                <!-- زر إضافة إلى السلة -->
                <form method="POST" action="index.php" style="margin:0;">
    <input type="hidden" name="book_id" value="<?= (int)$row['id'] ?>">
    <button type="submit" name="add_to_cart" class="btn">إضافة إلى السلة 🛒</button>
</form>

            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column:1/-1; text-align:center;">لا توجد نتائج.</p>
    <?php endif; ?>
</section>

<!-- زر سلة عائم يظهر دائمًا -->
<a class="floating-cart" href="cart.php">🛒 السلة</a>

<footer>
    <p>© 2025 BookSwap. جميع الحقوق محفوظة</p>
</footer>

<script>
// تفعيل القائمة المنسدلة للمستخدم
const dropdown = document.querySelector('.user-dropdown');
const userBtn = document.querySelector('.user-btn');

if (dropdown && userBtn) {
    userBtn.addEventListener('click', (e) => {
        e.preventDefault();
        dropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
}
</script>

</body>
</html>