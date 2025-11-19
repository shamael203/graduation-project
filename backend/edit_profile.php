<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

$user_id = $_SESSION['user_id'];

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// جلب البيانات الحالية
$stmt = $conn->prepare("SELECT bio, phone, avatar FROM profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bio = $_POST["bio"];
    $phone = $_POST["phone"];
    $avatarPath = $profile["avatar"] ?? null;

    // رفع الصورة إن وجدت
    if (!empty($_FILES["avatar"]["name"])) {
        $file = $_FILES["avatar"];
        $allowed = ['image/jpeg','image/png'];

        if (!in_array(mime_content_type($file["tmp_name"]), $allowed)) {
            $errors[] = "نوع الصورة غير مسموح";
        } else {
            $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
            $newName = $uploadDir . "avatar_" . $user_id . "_" . time() . "." . $ext;

            move_uploaded_file($file["tmp_name"], $newName);
            $avatarPath = $newName;
        }
    }

    if (empty($errors)) {

        // هل الملف موجود سابقاً؟
        $check = $conn->prepare("SELECT user_id FROM profiles WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $stmt = $conn->prepare("UPDATE profiles SET bio=?, phone=?, avatar=? WHERE user_id=?");
            $stmt->bind_param("sssi", $bio, $phone, $avatarPath, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO profiles (bio, phone, avatar, user_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $bio, $phone, $avatarPath, $user_id);
        }

        $stmt->execute();
        $stmt->close();

        header("Location: profile.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>تعديل البروفايل</title>
<style>
body { direction: rtl; font-family: Arial; }
form { width: 60%; margin: auto; background:white; padding:20px; border-radius:8px; }
input, textarea { width: 100%; margin-top: 8px; padding: 8px; }
button { margin-top: 10px; padding:10px; background:#28a745; color:white; border:none; border-radius:5px; }
</style>
</head>
<body>

<h2 style="text-align:center">تعديل البيانات</h2>

<form method="POST" enctype="multipart/form-data">
    <label>الهاتف:</label>
    <input type="text" name="phone" value="<?= $profile['phone'] ?? '' ?>">

    <label>نبذة:</label>
    <textarea name="bio"><?= $profile['bio'] ?? '' ?></textarea>

    <label>الصورة الشخصية:</label>
    <input type="file" name="avatar">

    <button type="submit">حفظ التغييرات</button>
</form>

</body>
</html>