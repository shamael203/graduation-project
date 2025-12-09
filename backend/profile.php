<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// تضمين الهيدر
include 'header.php';

// جلب بيانات المستخدم
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// جلب معلومات البروفايل
$stmt2 = $conn->prepare("SELECT bio, phone, avatar FROM  profile  WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$profile = $stmt2->get_result()->fetch_assoc();
$stmt2->close();

// صورة افتراضية
$avatar = "uploads/default-avatar.png";
if (!empty($profile["avatar"]) && file_exists($profile["avatar"])) {
    $avatar = $profile["avatar"];
}
// جلب كتب المستخدم
$stmt3 = $conn->prepare("SELECT title, author, edition, price, image FROM books WHERE user_id = ?");
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$books = $stmt3->get_result();
$stmt3->close();
?>

<div class="container">
    <h2>الملف الشخصي</h2>

    <img src="<?= $avatar ?>" alt="الصورة">

    <p><strong>الاسم:</strong> <?= $user['name'] ?></p>
    <p><strong>البريد:</strong> <?= $user['email'] ?></p>
    <p><strong>الهاتف:</strong> <?= $profile['phone'] ?? '-' ?></p>
    <p><strong>نبذة:</strong> <?= nl2br($profile['bio'] ?? '-') ?></p>

    <br>
    <a class="button" href="edit_profile.php">تعديل البروفايل</a>
    <hr>
    <h3>📚 كتبي</h3>
<?php while($book = $books->fetch_assoc()): ?>
    <div style="margin-bottom:20px;">
        <?php if(!empty($book['image'])): ?>
            <img src="uploads/<?= $book['image'] ?>" style="width:100px; height:140px; border-radius:6px;">
        <?php endif; ?>

        <p><strong>العنوان:</strong> <?= $book['title'] ?></p>
        <p><strong>المؤلف:</strong> <?= $book['author'] ?></p>
        <p><strong>الطبعة:</strong> <?= $book['edition'] ?></p>
        <p><strong>السعر:</strong> <?= $book['price'] ?> ر.س</p>
        <hr>
    </div>
<?php endwhile; ?>
</div>

<?php
// تضمين الفوتر
include 'footer.php';
?>
