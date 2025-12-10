
<?php
// الاتصال بقاعدة البيانات
$host = "localhost";
$user = "root";
$password = "";
$dbname = "book_exchange";

$conn = new mysqli($host, $user, $password, $dbname);
session_start();
include 'connect.php';

// تضمين الهيدر
include 'header.php';

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// استعلام البحث
$search = "";
if (isset($_GET["search"])) {
    $search = $_GET["search"];
    $sql = "SELECT * FROM books 
            WHERE title LIKE '%$search%' 
            OR author LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM books";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>📚 قائمة الكتب - BookSwap</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
  font-family: "Tajawal", sans-serif;
  direction: rtl;
  background: #eef2ff;
  margin: 0;
  padding: 25px;
}

.container {
  max-width: 850px;
  margin: auto;
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

h2 {
  color: #1a237e;
  margin-bottom: 15px;
  text-align:center;
}

/* شريط البحث */
.search-box {
  display: flex;
  margin-bottom: 25px;
}

.search-box input {
  flex: 1;
  padding: 12px;
  border: 1px solid #c7c7c7;
  font-size: 16px;
  border-radius: 8px 0 0 8px;
}

.search-box button {
  padding: 12px 18px;
  background: #3f51b5;
  color: white;
  border: none;
  font-size: 16px;
  border-radius: 0 8px 8px 0;
  cursor: pointer;
}

.search-box button:hover {
  background: #2c3e9a;
}

/* بطاقة الكتاب */
.book-card {
  background: #f7f8ff;
  border: 1px solid #d6d9ff;
  padding: 18px;
  border-radius: 12px;
  margin-bottom: 15px;
}

.book-card p {
  margin: 5px 0;
  font-size: 15px;
}
</style>

</head>
<body>

<div class="container">

<h2>🔍 البحث عن كتاب</h2>

<form method="GET" action="books.php" class="search-box">
    <input type="text" name="search" placeholder="اكتب عنوان الكتاب أو اسم المؤلف..." 
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">بحث</button>
</form>

<hr>

<h2>📚 قائمة الكتب</h2>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='book-card'>
            <p><strong>📘 العنوان:</strong> " . $row["title"] . "</p>
            <p><strong>✍️ المؤلف:</strong> " . $row["author"] . "</p>
            <p><strong>🔢 النسخة:</strong> " . $row["edition"] . "</p>
            <p><strong>💰 السعر:</strong> " . $row["price"] . " ريال</p>
        </div>
        ";
    }
} else {
    echo "<p style='color:#b00020; text-align:center;'>⚠️ لا يوجد كتب مطابقة لبحثك.</p>";
}

$conn->close();
?>

</div>

</body>
</html>
<?php
// تضمين الفوتر
include 'footer.php';
?>