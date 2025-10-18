<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>قائمة الكتب</title>
</head>
<body>

<h2>🔍 البحث عن كتاب</h2>
<form method="GET" action="books.php">
    <input type="text" name="search" placeholder="ادخل عنوان الكتاب أو اسم المؤلف" required>
    <button type="submit">بحث</button>
</form>

<hr>

<h2>📚 قائمة الكتب</h2>

<?php
// الاتصال بقاعدة البيانات
$host = "localhost";
$user = "root"; // غيّرها إذا كان اسم المستخدم مختلف
$password = ""; // غيّرها إذا كان فيه كلمة مرور
$dbname = "book_exchange";

$conn = new mysqli($host, $user, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// تنفيذ البحث أو عرض جميع الكتب
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM books";
}

$result = $conn->query($sql);

// عرض النتائج
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<strong>📖 العنوان:</strong> " . htmlspecialchars($row['title']) . "<br>";
        echo "<strong>✍️ المؤلف:</strong> " . htmlspecialchars($row['author']) . "<br>";
        echo "<strong>📝 الطبعة:</strong> " . htmlspecialchars($row['edition']) . "<br>";
        echo "<strong>💰 السعر:</strong> " . htmlspecialchars($row['price']) . " ريال<br>";
        echo "</div><hr>";
    }
} else {
    echo "<p>❌ لا توجد نتائج مطابقة.</p>";
}

$conn->close();
?>

</body>
</html>
