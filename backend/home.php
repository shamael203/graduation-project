<?php
session_start();
include 'connect.php';
include 'header.php'; // الهيدر الموحد

// Default query
$sql = "SELECT books.*, users.name AS user_name, users.email AS user_email
        FROM books
        JOIN users ON books.user_id = users.id
        ORDER BY books.id DESC";
$books = $conn->query($sql);

// User data
$username   = $_SESSION['user_name'] ?? null;
$user_email = $_SESSION['user_email'] ?? null;
$user_id    = $_SESSION['user_id'] ?? null;

// Search
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $sql = "SELECT books.*, users.name AS user_name, users.email AS user_email
            FROM books
            JOIN users ON books.user_id = users.id
            WHERE books.title LIKE ? OR books.author LIKE ?
            ORDER BY books.id DESC";
    $stmt = $conn->prepare($sql);
    $likeSearch = "%$search%";
    $stmt->bind_param("ss", $likeSearch, $likeSearch);
    $stmt->execute();
    $books = $stmt->get_result();
}

// Messages preview
$messages = [];
if ($user_id) {
    $msg_sql = "SELECT * FROM messages WHERE receiver_id = ? ORDER BY date DESC LIMIT 3";
    $msg_stmt = $conn->prepare($msg_sql);
    $msg_stmt->bind_param("i", $user_id);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();
}


// Cart count
$cart_count = 0;
if ($user_id) {
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $cart_count = $res['total'] ?? 0;
} else {
    $cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BookSwap - Home</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family:"Segoe UI", Arial, sans-serif; margin:0; padding:0; background:#f0f4ff; direction:ltr; }

/* Dropdown */
.user-dropdown { position: relative; }
.user-btn { cursor:pointer; color:white; text-decoration:none; font-weight:bold; }
.user-dropdown-content {
    display: none;
    position: absolute;
    top: 40px; right: 0;
    background:#3f51b5; min-width:220px;
    border-radius:8px; box-shadow:0 4px 10px rgba(255, 255, 255, 0.2);
    padding:10px; z-index:100;
}
.user-dropdown.active .user-dropdown-content { display:block; }
.user-dropdown-content a {
    display:block; text-decoration:none; color: #fff;
    padding:8px; border-radius:5px; font-weight:bold; margin-bottom:5px;
}
.user-dropdown-content a:hover { background:#e0e0ff; }
.user-dropdown-content a.logout { color:red; }
.user-dropdown-content i { margin-right:8px; }

/* Main content */
.main-content { text-align:center; padding:60px 20px; background:#e8eaf6; }
.main-content h2 { color:#1a237e; font-size:32px; margin-bottom:20px; }

/* Search */

.search-box {
  max-width: 600px;
  margin: 30px auto;
  position: relative;
  width: 100%;
}

.search-box input {
  width: 100%;
  padding: 14px 60px 14px 15px; /* مساحة للزر على اليمين */
  border-radius: 40px;
  border: 2px solid #b9c4ff;
  font-size: 17px;
  outline: none;
  transition: 0.3s;
}

.search-box input:focus {
  border-color: #3f51b5;
  box-shadow: 0 0 6px rgba(63,81,181,0.4);
}

.search-box button {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: #3f51b5;
  border: none;
  color: white;
  padding: 10px 18px;
  border-radius: 30px;
  cursor: pointer;
  font-size: 15px;
}

.search-box button:hover {
  background:#283593;
}
/* Books */
.features { max-width:1100px; margin:40px auto; display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px; padding:0 20px; }
.feature { background:white; padding:15px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; display:flex; flex-direction:column; gap:10px; }
.feature img { width:150px; height:200px; object-fit:cover; border-radius:8px; margin:0 auto; }
.feature h3 { margin:5px 0; color:#1a237e; font-size:18px; }
.feature p { margin:2px 0; color:#333; font-size:14px; }
.feature .price { color:#e91e63; font-weight:bold; }
.feature .btn { background:#3f51b5; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer; font-weight:bold; }
.feature .btn:hover { background:#283593; }

/* Floating cart */
.floating-cart {
    position: fixed; left: 20px; bottom: 20px; background:#ff9800; color:white;
    padding: 12px 16px; border-radius:30px; text-decoration:none; font-weight:bold;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
}
.floating-cart:hover { background:#fb8c00; }

.feature {
  background: white;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: space-between; /* يوزع العناصر ويضمن أن الزر يكون في الأسفل */
  gap: 10px;
  height: 100%; /* يضمن أن كل بطاقة تأخذ نفس الطول داخل الشبكة */
}

.feature .btn {
  margin-top: auto; /* يدفع الزر لأسفل البطاقة */
}
</style>
</head>
<body>



<section class="main-content">
    <h2>Exchange Books Easily</h2>
    <p>Welcome to <strong>BookSwap</strong> — the best place to buy and sell your books.</p>
    <form method="GET" action="home.php" class="search-box">
        <input type="text" name="search" placeholder="Search for a book or author..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
</section>

<section class="features">
    <h2 style="grid-column:1/-1; text-align:center;">Latest Books</h2>

    <?php if ($books && $books->num_rows > 0): ?>
        <?php while($row = $books->fetch_assoc()): ?>
            <?php 
    $imageFile = !empty($row['image']) 
        ? "uploads/".$row['image'] 
        : "assets/no-image.png"; 
  ?>

          <div class="feature">
            <img src="<?= htmlspecialchars($imageFile) ?>" alt="Book Cover">
            <h3>
                <a href="book_details.php?id=<?= (int)$row['id'] ?>" style="text-decoration:none; color:#1a237e;">
                    <?= htmlspecialchars($row['title']) ?>
                </a>
            </h3>
            <p>Author: <?= htmlspecialchars($row['author']) ?></p>
            <?php if(!empty($row['edition'])): ?>
                <p>Edition: <?= htmlspecialchars($row['edition']) ?></p>
            <?php endif; ?>
            <p class="price">Price: <?= number_format($row['price'],2) ?> SAR</p>
            <p class="added-by">Added by: <?= htmlspecialchars($row['user_name']) ?></p>

            <!-- Contact seller button -->
            <a href="chat.php?user=<?= (int)$row['user_id'] ?>&book=<?= (int)$row['id'] ?>" class="btn">Contact Seller</a>

            <!-- Add to Cart button -->
            <form method="POST" action="add_to_cart.php" style="margin:0;">
                <input type="hidden" name="book_id" value="<?= (int)$row['id'] ?>">
                <button type="submit" name="add_to_cart" class="btn"> Add to Cart</button>
            </form>
          </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column:1/-1; text-align:center;">No results found.</p>
    <?php endif; ?>
</section>
<!-- Floating Cart button -->
<a class="floating-cart" href="cart.php">Cart (<?= $cart_count ?>)</a>

<footer>
    <p>© 2025 BookSwap. All rights reserved.</p>
</footer>

<script>
// Toggle user dropdown menu
const dropdown = document.querySelector('.user-dropdown');
const userBtn = document.querySelector('.user-btn');

if (dropdown && userBtn) {
    userBtn.addEventListener('click', (e) => {
        e.preventDefault();
        dropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
}
</script>

</body>
</html>
<?php include 'footer.php'; ?> <!-- الفوتر الموحد -->