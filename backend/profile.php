<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';
include 'header.php'; // Common header

$user_id = $_SESSION['user_id'];

// ✅ Delete book
if (isset($_POST['delete_book'])) {
    $book_id = intval($_POST['book_id']);
    $stmtDel = $conn->prepare("DELETE FROM books WHERE id=? AND user_id=?");
    $stmtDel->bind_param("ii", $book_id, $user_id);
    $stmtDel->execute();
    $stmtDel->close();
    header("Location: profile.php");
    exit();
}

// Get user info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get profile info
$stmt2 = $conn->prepare("SELECT bio, phone, join_date, location FROM profile WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$profile = $stmt2->get_result()->fetch_assoc();
$stmt2->close();

// Get books
$stmt3 = $conn->prepare("SELECT id, title, author, edition, price, image FROM books WHERE user_id = ?");
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$books = $stmt3->get_result();
$stmt3->close();

$book_count = $books->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<style>
html, body {
  width: 100%;
  margin: 0;
  padding: 0;
  font-family:"Segoe UI", Arial, sans-serif;
  background:#fff;
  color:#333;
}

/* header & footer */
header, footer {
  width:100%;
  background:#3f51b5;
  color:white;
  padding:15px 30px;
}

/* container */
.page-container {
  max-width:1200px;
  margin:auto;
  padding:40px 20px;
  box-sizing:border-box;
}

/* profile header */
.profile-header {
  display:flex;
  align-items:center;
  gap:20px;
  margin-bottom:20px;
  border-bottom:2px solid #eee;
  padding-bottom:20px;
}
.profile-info h2 { margin:0; font-size:24px; color:#3f51b5; }
.profile-info p { margin:4px 0; font-size:14px; color:#555; }

/* Buttons */
.buttons { margin:20px 0; }
.edit-btn, .add-book-btn {
  display:inline-block;
  margin-right:10px;
  padding:10px 18px;
  border-radius:6px;
  text-decoration:none;
  font-weight:bold;
  color:white;
}
.edit-btn { background:#3f51b5; }
.edit-btn:hover { background:#283593; }
.add-book-btn { background:#d32f2f; }
.add-book-btn:hover { background:#b71c1c; }

/* Books grid */
.books-grid {
  display:grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap:20px;
  margin-top:20px;
}
.book-card {
  background:#fff;
  border:1px solid #ddd;
  border-radius:8px;
  padding:10px;
  box-shadow:0 2px 6px rgba(0,0,0,0.05);
  text-align:center;
}
.book-card img {
  width:100%;
  height:200px;
  object-fit:cover;
  border-radius:6px;
  margin-bottom:10px;
}
.book-card p { margin:5px 0; font-size:14px; }
.empty-message {
  color:#999;
  font-size:16px;
  margin:30px 0;
  text-align:center;
}

/* Delete button */
.book-card button {
  background:#d32f2f;
  color:white;
  border:none;
  padding:8px 12px;
  border-radius:6px;
  cursor:pointer;
  font-weight:bold;
  margin-top:10px;
}
.book-card button:hover { background:#b71c1c; }

/* About section */
.about {
  margin-top:40px;
  padding:20px;
  border-top:2px solid #eee;
}
.about h3 { margin-top:0; color:#3f51b5; }
.about p { white-space:pre-wrap; font-size:14px; color:#444; }
</style>
</head>
<body>

<div class="page-container">

  <!-- Profile header -->
  <div class="profile-header">
    <div class="profile-info">
      <h2><?= htmlspecialchars($user['name']) ?></h2>
      <p><i><?= htmlspecialchars($user['email']) ?></i></p>
      <p>No ratings yet</p>
    </div>
  </div>

  <!-- Buttons -->
  <div class="buttons">
    <a href="edit_profile.php" class="edit-btn">✏️ Edit Profile</a>
    <a href="add_book.php" class="add-book-btn">➕ Add New Book</a>
    <a class="button" href="my_orders.php">📦 My Order</a>
  </div>

  <!-- Books section -->
  <?php if ($book_count == 0): ?>
    <p class="empty-message">📚 Nothing here – yet!</p>
  <?php else: ?>
    <div class="books-grid">
      <?php while($book = $books->fetch_assoc()): ?>
        <div class="book-card">
          <?php if (!empty($book['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($book['image']) ?>" alt="Book Cover">
          <?php endif; ?>
          <p><strong><?= htmlspecialchars($book['title']) ?></strong></p>
          <p><?= htmlspecialchars($book['author']) ?></p>
          <?php if(!empty($book['edition'])): ?>
            <p>Edition: <?= htmlspecialchars($book['edition']) ?></p>
          <?php endif; ?>
          <p style="color:#3f51b5; font-weight:bold;"><?= htmlspecialchars($book['price']) ?> SAR</p>

          <!-- Delete button -->
          <form method="POST" action="profile.php" onsubmit="return confirm('Are you sure you want to delete this book?');">
            <input type="hidden" name="book_id" value="<?= (int)$book['id'] ?>">
            <button type="submit" name="delete_book">🗑️ Delete</button>
          </form>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

  <!-- About Me section -->
  <div class="about">
    <h3>About Me</h3>
    <p><?= !empty($profile['bio']) ? nl2br(htmlspecialchars($profile['bio'])) : "No bio added yet." ?></p>
  </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>