<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

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
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>البروفايل</title>
<style>
body { direction: rtl; font-family: Arial; }
.container { width: 80%; margin: auto; }
img { width: 150px; height:150px; border-radius: 8px; object-fit: cover; }
.button { background:#007bff; color:white; padding:10px; border-radius:6px; text-decoration:none; }
</style>
</head>
<body>

<div class="container">
    <h2>الملف الشخصي</h2>

    <img src="<?= $avatar ?>" alt="الصورة">

    <p><strong>الاسم:</strong> <?= $user['name'] ?></p>
    <p><strong>البريد:</strong> <?= $user['email'] ?></p>
    <p><strong>الهاتف:</strong> <?= $profile['phone'] ?? '-' ?></p>
    <p><strong>نبذة:</strong> <?= nl2br($profile['bio'] ?? '-') ?></p>

    <br>
    <a class="button" href="edit_profile.php">تعديل البروفايل</a>
</div>

</body>
</html>