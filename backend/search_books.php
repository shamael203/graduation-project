<?php
session_start();
include 'connect.php';

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