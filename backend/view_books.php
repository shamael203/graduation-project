<?php
session_start();
include "connect.php";

// تضمين الهيدر
include "header.php";

// معلومات المستخدم
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// معالجة إضافة للسلة
if (isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);
    $quantity = 1;

    // تأكد أن الكتاب موجود فعلاً
    $stmt = $conn->prepare("SELECT id FROM books WHERE id=?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $exists = $stmt->get_result();
    if ($exists->num_rows === 0) {
        header("Location: view_books.php");
        exit;
    }

    // هل الكتاب موجود مسبقاً في السلة؟
    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id=? AND book_id=?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $resultCheck = $stmt->get_result();

    if ($resultCheck->num_rows > 0) {
        $row = $resultCheck->fetch_assoc();
        $cart_id = $row['id'];
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id=?");
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $book_id, $quantity);
        $stmt->execute();
    }

    header("Location: view_books.php");
    exit;
}

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

<div class="main">
    <!-- البحث -->
    <form method="GET" action="view_books.php" class="search-box">
        <input type="text" name="search" placeholder="اكتب عنوان الكتاب أو اسم المؤلف..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">بحث</button>
    </form>

    <!-- الكتب -->
    <div class="books-grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php $imageFile = !empty($row['image']) ? "uploads/" . $row['image'] : "uploads/default.png"; ?>
            <div class="book">
                <img src="<?= htmlspecialchars($imageFile)?>" alt="صورة الكتاب">
                <div class="book-info">
                    <h3>📖 <?= htmlspecialchars($row['title']) ?></h3>
                    <p>المؤلف: <?= htmlspecialchars($row['author']) ?></p>
                    <?php if(!empty($row['edition'])): ?>
                        <p>الطبعة: <?= htmlspecialchars($row['edition']) ?></p>
                    <?php endif; ?>
                    <p class="price">السعر: <?= htmlspecialchars($row['price']) ?> ر.س</p>

                    <!-- نموذج إضافة للسلة -->
                    <form method="POST" action="view_books.php">
                        <input type="hidden" name="book_id" value="<?= (int)$row['id'] ?>">
                        <button type="submit" name="add_to_cart">إضافة إلى السلة</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">لا توجد كتب حالياً.</p>
    <?php endif; ?>
    </div>
</div>

<?php
// تضمين الفوتر
include "footer.php";
?>
