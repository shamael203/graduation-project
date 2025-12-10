<?php
session_start();
include 'connect.php';
include 'header.php';  // unified header

// Fetch all books from database
$sql = "SELECT * FROM books";
$result = $conn->query($sql);
?>

<div class="container">
    <h2>Available Books</h2>

    <table>
        <tr>
            <th>Book Title</th>
            <th>Author</th>
            <th>Edition</th>
            <th>Price</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                // Title as a link to open details page
                echo "<td><a class='book-link' href='book_details.php?id=" . (int)$row['id'] . "'>" . htmlspecialchars($row['title']) . "</a></td>";
                echo "<td>" . htmlspecialchars($row['author']) . "</td>";
                echo "<td>" . htmlspecialchars($row['edition']) . "</td>";
                echo "<td>" . htmlspecialchars($row['price']) . " SAR</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No books available</td></tr>";
        }
        ?>
    </table>
</div>

<?php include 'footer.php'; ?> <!-- unified footer -->