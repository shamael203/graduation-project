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

<?php
// تضمين الفوتر
include 'footer.php';
?>
