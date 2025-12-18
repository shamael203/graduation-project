<?php
// الاتصال بقاعدة البيانات
session_start();
include 'connect.php';

// جلب جميع الكتب من قاعدة البيانات
$sql = "SELECT b.*, u.name AS user_name 
        FROM books b 
        JOIN users u ON b.user_id = u.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Available Books - BookSwap</title>
<style>
body { font-family:"Segoe UI", Arial, sans-serif; margin:0; padding:0; background:#f0f4ff; direction:ltr; }

/* Main content */
.main-content { text-align:center; padding:60px 20px; background:#e8eaf6; }
.main-content h2 { color:#1a237e; font-size:32px; margin-bottom:20px; }

/* Search */
.search-box { max-width: 600px; margin: 30px auto; position: relative; }
.search-box input {
    width: 100%; padding: 14px 50px 14px 15px; border-radius: 40px;
    border: 2px solid #b9c4ff; font-size: 17px; outline: none; transition: 0.3s;
}
.search-box input:focus { border-color: #3f51b5; box-shadow: 0 0 6px rgba(63,81,181,0.4); }
.search-box button {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    background: #3f51b5; border: none; color: white; padding: 10px 18px;
    border-radius: 30px; cursor: pointer; font-size: 15px;
}
.search-box button:hover { background:#283593; }

/* Books grid */
.features { max-width:1100px; margin:40px auto; display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px; padding:0 20px; }
.feature { background:white; padding:15px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; display:flex; flex-direction:column; justify-content:space-between; gap:10px; height:100%; }
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
</style>
</head>
<body>

<section class="main-content">
    <h2>📚 Available Books</h2>
    <p>Welcome to <strong>BookSwap</strong> — the best place to buy and sell your books.</p>
    <form method="GET" action="home.php" class="search-box">
        <input type="text" name="search" placeholder="Search for a book or author..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>
</section>

<section class="features">
    <h2 style="grid-column:1/-1; text-align:center;">Latest Books</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <?php $imageFile = !empty($row['image']) ? "uploads/".$row['image'] : "uploads/default.png"; ?>
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
                    <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="grid-column:1/-1; text-align:center;">No books available.</p>
    <?php endif; ?>
</section>

<a href="cart.php" class="floating-cart">🛒 View Cart</a>

</body>
</html>