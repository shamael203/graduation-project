<?php
session_start();
include "connect.php";

// Include header
include "header.php";

// User info
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);
    $quantity = 1;

    // Check if book exists
    $stmt = $conn->prepare("SELECT id FROM books WHERE id=?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $exists = $stmt->get_result();
    if ($exists->num_rows === 0) {
        header("Location: view_books.php");
        exit;
    }

    // Check if book already in cart
    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id=? AND book_id=?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $resultCheck = $stmt->get_result();

    if ($resultCheck->num_rows > 0) {
        $row = $resultCheck->fetch_assoc();
        $cart_id = $row['id'];
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id=?");
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $book_id, $quantity);
        $stmt->execute();
    }

    header("Location: view_books.php");
    exit;
}

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY id DESC");
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM books ORDER BY id DESC");
}
?>

<div class="main">
    <!-- Search -->
    <form method="GET" action="view_books.php" class="search-box">
        <input type="text" name="search" placeholder="Enter book title or author name..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Books -->
    <div class="books-grid">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php $imageFile = !empty($row['image']) ? "uploads/" . $row['image'] : "uploads/default.png"; ?>
            <div class="book">
                <img src="<?= htmlspecialchars($imageFile)?>" alt="Book Image">
                <div class="book-info">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p>Author: <?= htmlspecialchars($row['author']) ?></p>
                    <?php if(!empty($row['edition'])): ?>
                        <p>Edition: <?= htmlspecialchars($row['edition']) ?></p>
                    <?php endif; ?>
                    <p class="price">Price: <?= htmlspecialchars($row['price']) ?> SAR</p>

                    <!-- Add to cart form -->
                    <form method="POST" action="view_books.php">
                        <input type="hidden" name="book_id" value="<?= (int)$row['id'] ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">No books available.</p>
    <?php endif; ?>
    </div>
</div>

<?php
// Include footer
include "footer.php";
?>