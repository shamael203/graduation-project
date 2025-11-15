<?php
session_start();
include "connect.php"; // الاتصال بقاعدة البيانات

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$message = "";

if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $edition = $_POST['edition'];
    $price = $_POST['price'];

    // رفع الصورة
    $imagePath = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $fileName; // فقط اسم الملف
        } else {
            $message = "<div class='alert error'>❌ حدث خطأ أثناء رفع الصورة</div>";
        }
    }

    // إدخال البيانات في قاعدة البيانات
    $sql = "INSERT INTO books (title, author, edition, price, user_id, image)
            VALUES ('$title', '$author', '$edition', '$price', '$user_id', '$imagePath')";

    if ($conn->query($sql)) {
        header("Location: view_books.php");
        exit;
    } else {
        $message = "<div class='alert error'>❌ حدث خطأ: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="utf-8">
<title>إضافة كتاب - BookSwap</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { font-family: "Tajawal", Arial; direction: rtl; background: #f0f4ff; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; }
.container { background:#fff; padding:35px 40px; border-radius:16px; max-width:440px; width:100%; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#1a237e; margin-bottom:25px; }
label { display:block; margin-bottom:6px; font-weight:600; }
input { width:100%; padding:10px; margin-bottom:18px; border:1px solid #ccc; border-radius:8px; font-size:15px; }
input:focus { border-color:#3f51b5; outline:none; }
button { width:100%; padding:12px; background:#3f51b5; color:white; border:none; border-radius:8px; font-size:16px; font-weight:bold; cursor:pointer; }
button:hover { background:#2c3e9a; }
.alert { padding:12px; margin-bottom:20px; border-radius:10px; font-weight:600; text-align:center; }
.alert.success { background:#e9f7ef; color:#006400; border:1px solid #a9dfbf; }
.alert.error { background:#ffe5e5; color:#b00020; border:1px solid #f5b7b1; }
</style>
</head>
<body>
<div class="container">
  <h2>📚 إضافة كتاب جديد</h2>
  <?php echo $message; ?>
  <form method="POST" enctype="multipart/form-data">
    <label for="title">عنوان الكتاب</label>
    <input id="title" name="title" type="text" required maxlength="255" placeholder="أدخل عنوان الكتاب">

    <label for="author">اسم المؤلف</label>
    <input id="author" name="author" type="text" required maxlength="255" placeholder="اسم المؤلف">

    <label for="edition">الطبعة</label>
    <input id="edition" name="edition" type="text" maxlength="100" placeholder="مثلاً: الطبعة الثانية">

    <label for="price">السعر (ر.س)</label>
    <input id="price" name="price" type="number" step="0.01" required placeholder="أدخل السعر">

    <label for="image">صورة الكتاب</label>
    <input id="image" name="image" type="file" accept="image/*">

    <button type="submit" name="save">💾 حفظ الكتاب</button>
  </form>
</div>
</body>
</html>
