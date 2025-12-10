<?php
session_start();
include 'connect.php';
include 'header.php';  // unified header

$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($book_id <= 0) { die("Invalid book ID."); }

// Fetch book details
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) { die("Book not found."); }

$seller_id = (int) $book['user_id'];
$category = $book['category'];

// Fetch suggestions from same category (excluding current book)
$suggestions = [];
$stmt2 = $conn->prepare("SELECT id, title, image FROM books WHERE category = ? AND id != ? LIMIT 4");
$stmt2->bind_param("si", $category, $book_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) {
    $suggestions[] = $row;
}
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($book['title']) ?></title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background:#f4f5f7; margin:0; padding:0; }
    .container { max-width:1000px; margin:30px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
    .book-section { display:flex; gap:30px; }
    .book-image img { width:100%; max-width:300px; border-radius:8px; }
    .book-details h2 { margin-top:0; color:#007bff; }
    .book-details p { font-size:16px; margin:8px 0; }
    .price { font-size:20px; color:#28a745; font-weight:bold; margin-top:10px; }
    .btn { display:inline-block; margin-top:15px; padding:10px 20px; background:#007bff; color:#fff; text-decoration:none; border-radius:6px; font-weight:bold; }
    .btn:hover { background:#005fd1; }
    .suggestions { margin-top:40px; }
    .suggestions h3 { margin-bottom:20px; }
    .suggestion-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:20px; }
    .suggestion-card { background:#f9f9f9; padding:10px; border-radius:8px; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
    .suggestion-card img { width:100%; max-height:180px; object-fit:cover; border-radius:6px; }
    .suggestion-card a { text-decoration:none; color:#007bff; font-weight:bold; display:block; margin-top:10px; }
  </style>
</head>
<body>

<div class="container">
  <div class="book-section">
    <div class="book-image">
      <img src="uploads/<?= htmlspecialchars($book['image']) ?>" alt="Book Cover">
    </div>
    <div class="book-details">
      <h2><?= htmlspecialchars($book['title']) ?></h2>
      <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
      <p><strong>Publisher:</strong> <?= htmlspecialchars($book['publisher'] ?? 'Not specified') ?></p>
      <p><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn'] ?? 'Not available') ?></p>
      <p><strong>Category:</strong> <?= htmlspecialchars($book['category'] ?? 'Not specified') ?></p>
      <p><strong>Edition:</strong> <?= htmlspecialchars($book['edition'] ?? 'Not specified') ?></p>
      <p class="price"><?= htmlspecialchars($book['price']) ?> SAR</p>

      <a href="add_to_cart.php?book=<?= $book_id ?>" class="btn">Add to Cart</a>
      <a href="chat.php?user=<?= $seller_id ?>&book=<?= $book_id ?>" class="btn">Contact Seller</a>
    </div>
  </div>

  <?php if (!empty($suggestions)): ?>
    <div class="suggestions">
      <h3>Other Books in the Same Category</h3>
      <div class="suggestion-grid">
        <?php foreach ($suggestions as $s): ?>
          <div class="suggestion-card">
            <img src="uploads/<?= htmlspecialchars($s['image']) ?>" alt="Cover">
            <a href="book_details.php?id=<?= $s['id'] ?>"><?= htmlspecialchars($s['title']) ?></a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>  <!-- unified footer -->

</body>
</html>