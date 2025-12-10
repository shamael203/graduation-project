<?php
session_start();
include "connect.php"; // Database connection

if(!isset($_SESSION['user_id'])){
    die("You must log in first");
}
$user_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['save'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $edition = $_POST['edition'];
    $price = $_POST['price'];

    // Upload image
    $imagePath = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $fileName; // only filename
        } else {
            $message = "<div class='alert error'>An error occurred while uploading the image</div>";
        }
    }

    // Insert data into database
    $sql = "INSERT INTO books (title, author, edition, price, user_id, image)
            VALUES ('$title', '$author', '$edition', '$price', '$user_id', '$imagePath')";

    if ($conn->query($sql)) {
        header("Location: view_books.php");
        exit;
    } else {
        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Add Book - BookSwap</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { font-family: "Segoe UI", Arial; direction: ltr; background: #f0f4ff; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; }
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

<?php include 'header.php'; ?>

<div class="container">
  <h2>Add New Book</h2>
  <?php echo $message; ?>
  <form method="POST" enctype="multipart/form-data">
    <label for="title">Book Title</label>
    <input id="title" name="title" type="text" required maxlength="255" placeholder="Enter book title">

    <label for="author">Author</label>
    <input id="author" name="author" type="text" required maxlength="255" placeholder="Enter author's name">

    <label for="edition">Edition</label>
    <input id="edition" name="edition" type="text" maxlength="100" placeholder="e.g. Second Edition">

    <label for="price">Price (SAR)</label>
    <input id="price" name="price" type="number" step="0.01" required placeholder="Enter price">

    <label for="image">Book Image</label>
    <input id="image" name="image" type="file" accept="image/*">

    <button type="submit" name="save">Save Book</button>
  </form>
</div>

<?php include 'footer.php'; ?>

</body>
</html>