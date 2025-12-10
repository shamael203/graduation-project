<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "book_exchange";

$conn = new mysqli($host, $user, $password, $dbname);
session_start();
include 'connect.php';

// Include header
include 'header.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Search query
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

<h2>Search for a Book</h2>

<form method="GET" action="books.php" class="search-box">
    <input type="text" name="search" placeholder="Enter book title or author name..." 
           value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<hr>

<h2>Book List</h2>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "
        <div class='book-card'>
            <p><strong>Title:</strong> " . $row["title"] . "</p>
            <p><strong>Author:</strong> " . $row["author"] . "</p>
            <p><strong>Edition:</strong> " . $row["edition"] . "</p>
            <p><strong>Price:</strong> " . $row["price"] . " SAR</p>
        </div>
        ";
    }
} else {
    echo "<p style='color:#b00020; text-align:center;'>No books match your search.</p>";
}

$conn->close();
?>

</div>

<?php
// Include footer
include 'footer.php';
?>