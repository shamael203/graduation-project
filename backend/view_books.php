<?php
session_start();
include "connect.php";
include "header.php";

$username   = $_SESSION['user_name'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;
$user_id    = $_SESSION['user_id'] ?? 1;

// handle add to cart
if (isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);
    $quantity = 1;

    $stmt = $conn->prepare("SELECT id FROM books WHERE id=?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $exists = $stmt->get_result();
    if ($exists->num_rows === 0) {
        header("Location: view_books.php");
        exit;
    }

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

// handle search
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT b.*, u.name AS user_name FROM books b JOIN users u ON b.user_id=u.id WHERE b.title LIKE ? OR b.author LIKE ? ORDER BY b.id DESC");
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT b.*, u.name AS user_name FROM books b JOIN users u ON b.user_id=u.id ORDER BY b.id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📚 Browse Books</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family:"Segoe UI", Arial, sans-serif;
  background:#f0f4ff;
  margin:0;
  padding:0;
  direction:ltr;
}
.main {
  max-width:1200px;
  margin:40px auto;
  padding:0 20px;
}
h2 {
  color:#1a237e;
  text-align:center;
  margin-bottom:20px;
}

/* search bar */
.search-box {
  max-width:600px;
  margin:20px auto 30px auto;
  display:flex;
  box-shadow:0 2px 6px rgba(0,0,0,0.1);
  border-radius:8px;
  overflow:hidden;
}
.search-box input {
  flex:1;
  padding:12px 15px;
  border:none;
  font-size:16px;
  outline:none;
}
.search-box button {
  padding:12px 18px;
  background:#3f51b5;
  color:white;
  border:none;
  cursor:pointer;
  font-size:16px;
  transition:0.3s;
}
.search-box button:hover {
  background:#2c3e9a;
}

/* books grid */
.books-grid {
  display:grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap:20px;
}

/* book cards */
.book {
  background:#fff;
  border-radius:10px;
  overflow:hidden;
  box-shadow:0 2px 6px rgba(0,0,0,0.1);
  display:flex;
  flex-direction:column;
  transition:0.3s;
  height:100%;
}
.book:hover {
  transform: translateY(-3px);
  box-shadow:0 4px 12px rgba(0,0,0,0.2);
}
.book img {
  width:100%;
  height:250px;
  object-fit:cover;
}
.book-info {
  padding:15px;
  flex:1;
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:space-between;
}
.book-info h3 {
  margin:0 0 8px 0;
  color:#1a237e;
  font-size:18px;
  text-align:center;
}
.book-info p {
  margin:4px 0;
  font-size:14px;
  color:#333;
  text-align:center;
}
.book-info .price {
  color:#e91e63;
  font-weight:bold;
  font-size:15px;
  margin-top:8px;
}
.book-info .seller {
  color:#555;
  font-size:13px;
  margin-bottom:10px;
}
.book-info .btn {
  background:#3f51b5;
  color:white;
  border:none;
  padding:10px;
  border-radius:6px;
  cursor:pointer;
  font-weight:bold;
  text-align:center;
  text-decoration:none;
  margin-top:10px;
  transition:0.3s;
  width:100%;
  max-width:220px;
}
.book-info .btn:hover {
  background:#283593;
}

@media(max-width:768px){
  .search-box {
    flex-direction: column;
  }
  .search-box button {
    border-radius:0 0 8px 8px;
    margin-top:5px;
  }
}
</style>
</head>
<body>

<div class="main">
  <!-- search -->
  <form method="GET" action="view_books.php" class="search-box">
    <input type="text" name="search" placeholder="Search by title or author..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
  </form>

  <!-- books -->
  <div class="books-grid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <?php $imageFile = !empty($row['image']) ? "uploads/" . $row['image'] : "uploads/default.png"; ?>
        <div class="book">
          <a href="book_details.php?id=<?= (int)$row['id'] ?>">
            <img src="<?= htmlspecialchars($imageFile) ?>" alt="Book Cover">
          </a>
          <div class="book-info">
            <h3>
              <a href="book_details.php?id=<?= (int)$row['id'] ?>" style="text-decoration:none; color:#1a237e;">
                📖 <?= htmlspecialchars($row['title']) ?>
              </a>
            </h3>
            <p>Author: <?= htmlspecialchars($row['author']) ?></p>
            <?php if(!empty($row['edition'])): ?>
              <p>Edition: <?= htmlspecialchars($row['edition']) ?></p>
            <?php endif; ?>
            <p class="price">Price: <?= number_format($row['price'],2) ?> SAR</p>
            <p class="seller">Added by: <?= htmlspecialchars($row['user_name']) ?></p>

            <a href="chat.php?user=<?= (int)$row['user_id'] ?>&book=<?= (int)$row['id'] ?>" class="btn">Contact Seller</a>

            <form method="POST" action="view_books.php" style="width:100%; display:flex; justify-content:center;">
              <input type="hidden" name="book_id" value="<?= (int)$row['id'] ?>">
              <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;">No books available.</p>
    <?php endif; ?>
  </div>
</div>

<?php include "footer.php"; ?>
</body>
</html>