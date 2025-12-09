<?php
// الاتصال بقاعدة البيانات
include 'connect.php';

// جلب جميع الكتب من قاعدة البيانات
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>قائمة الكتب</title>
    <style>
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #888;
            text-align: center;
        }
        th {
            background: #222;
            color: white;
        }
        h2 {
            text-align: center;
            margin-top: 30px;
        }
        a.book-link {
            color: #1a237e;
            text-decoration: none;
            font-weight: bold;
        }
        a.book-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>📚 قائمة الكتب المتاحة</h2>

<table>
    <tr>
        <th>عنوان الكتاب</th>
        <th>المؤلف</th>
        <th>الطبعة</th>
        <th>السعر</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            // العنوان كرابط يفتح صفحة التفاصيل
            echo "<td><a class='book-link' href='book_details.php?id=" . (int)$row['id'] . "'>" . htmlspecialchars($row['title']) . "</a></td>";
            echo "<td>" . htmlspecialchars($row['author']) . "</td>";
            echo "<td>" . htmlspecialchars($row['edition']) . "</td>";
            echo "<td>" . htmlspecialchars($row['price']) . " ﷼</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>لا توجد كتب حالياً</td></tr>";
    }
    ?>
</table>

</body>
</html>