<?php
session_start();
include "connect.php";

if (!isset($_SESSION['user_id'])) {
    die("You must log in first");
}

$user_id = $_SESSION['user_id'];
$message = "";

// ----------------------
// 1) جلب بيانات الكتاب
// ----------------------
if (!isset($_GET['id'])) {
    die("Book not found");
}

$book_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM books WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    die("You are not allowed to edit this book");
}

// ----------------------
// 2) حفظ التعديل
// ----------------------
if (isset($_POST['update'])) {

    $title   = trim($_POST['title']);
    $author  = trim($_POST['author']);
    $edition = trim($_POST['edition']);
    $price   = floatval($_POST['price']);

    // الصورة القديمة
    $oldImage = $book['image'];
    $newImage = $oldImage;

    // إذا رفع صورة جديدة
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (in_array(strtolower($extension), $allowed)) {

            $fileName = time() . "_" . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {

                // حذف الصورة القديمة إذا موجودة
                if (!empty($oldImage) && file_exists("uploads/" . $oldImage)) {
                    unlink("uploads/" . $oldImage);
                }

                $newImage = $fileName;

            } else {
                $message = "<div class='alert error'>❌ Failed to upload image</div>";
            }

        } else {
            $message = "<div class='alert error'>❌ Unsupported image format</div>";
        }
    }

    // تحديث البيانات
    $update = $conn->prepare("
        UPDATE books 
        SET title=?, author=?, edition=?, price=?, image=?
        WHERE id=? AND user_id=?
    ");

    $update->bind_param("sssdsii", $title, $author, $edition, $price, $newImage, $book_id, $user_id);

    if ($update->execute()) {
        header("Location: profile.php");
        exit;
    } else {
        $message = "<div class='alert error'>❌ Error: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Book</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { font-family: Tahoma, sans-serif; background:#f0f4ff; margin:0; }
.container { background:#fff; padding:35px 40px; border-radius:16px; max-width:440px; width:100%; margin:40px auto; box-shadow:0 8px 20px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#1a237e; margin-bottom:25px; }
label { display:block; margin-bottom:6px; font-weight:600; }
input { width:100%; padding:10px; margin-bottom:18px; border:1px solid #ccc; border-radius:8px; font-size:15px; }
input:focus { border-color:#3f51b5; outline:none; }
button { width:100%; padding:12px; background:#3f51b5; color:white; border:none; border-radius:8px; font-size:16px; font-weight:bold; cursor:pointer; }
button:hover { background:#2c3e9a; }
.alert { padding:12px; margin-bottom:20px; border-radius:10px; font-weight:600; text-align:center; }
.alert.error { background:#ffe5e5; color:#b00020; border:1px solid #f5b7b1; }
.book-img { width:100%; border-radius:10px; margin-bottom:15px; }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
  <h2>✏️ Edit Book</h2>
  <?php echo $message; ?>

  <!-- عرض الصورة الحالية -->
  <?php if (!empty($book['image'])): ?>
      <img src="uploads/<?= $book['image'] ?>" class="book-img">
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">

    <label>Book Title</label>
    <input name="title" type="text" required value="<?= htmlspecialchars($book['title']) ?>">

    <label>Author Name</label>
    <input name="author" type="text" required value="<?= htmlspecialchars($book['author']) ?>">

    <label>Edition</label>
    <input name="edition" type="text" value="<?= htmlspecialchars($book['edition']) ?>">

    <label>Price (SAR)</label>
    <input name="price" type="number" step="0.01" required value="<?= $book['price'] ?>">

    <label>Change Image (optional)</label>
    <input name="image" type="file" accept="image/*">

    <button type="submit" name="update">💾 Save Changes</button>
  </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
