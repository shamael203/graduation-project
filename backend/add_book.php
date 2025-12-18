<?php
session_start();
include "connect.php";

if (!isset($_SESSION['user_id'])) {
    die("You must log in first");
}
$user_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['save'])) {
    $title   = trim($_POST['title']);
    $author  = trim($_POST['author']);
    $edition = trim($_POST['edition']);
    $price   = floatval($_POST['price']);

    // Upload image
    $imagePath = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Create folder if not exists
        }

        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array(strtolower($extension), $allowed)) {
            $fileName = time() . "_" . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $fileName; // Save only file name
            } else {
                $message = "<div class='alert error'>❌ Failed to upload image</div>";
            }
        } else {
            $message = "<div class='alert error'>❌ Unsupported image format</div>";
        }
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO books (title, author, edition, price, user_id, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdis", $title, $author, $edition, $price, $user_id, $imagePath);

    if ($stmt->execute()) {
        header("Location: view_books.php");
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
<title>Add New Book</title>
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
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
  <h2>📚 Add New Book</h2>
  <?php echo $message; ?>
  <form method="POST" enctype="multipart/form-data">
    <label for="title">Book Title</label>
    <input id="title" name="title" type="text" required maxlength="255" placeholder="Enter book title">

    <label for="author">Author Name</label>
    <input id="author" name="author" type="text" required maxlength="255" placeholder="Enter author name">

    <label for="edition">Edition</label>
    <input id="edition" name="edition" type="text" maxlength="100" placeholder="e.g. Second Edition">

    <label for="price">Price (SAR)</label>
    <input id="price" name="price" type="number" step="0.01" required placeholder="Enter price">

    <label for="image">Book Image</label>
    <input id="image" name="image" type="file" accept="image/*">

    <button type="submit" name="save">💾 Save Book</button>
  </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>