<?php
session_start();
include 'connect.php';
include 'header.php';  // الهيدر الموحد

// جلب جميع الكتب من قاعدة البيانات
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>

<div class="container">
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
</div>

<?php include 'footer.php'; ?> <!-- الفوتر الموحد -->
