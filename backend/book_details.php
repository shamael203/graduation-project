<?php
session_start();
include 'connect.php';
include 'header.php';  // الترويسة الموحدة

$book_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($book_id <= 0) { die("Invalid book ID."); }

// جلب تفاصيل الكتاب
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) { die("Book not found."); }

$seller_id = (int) $book['user_id'];
$category = $book['category'];

// جلب اقتراحات كتب من نفس التصنيف (غير الكتاب الحالي)
$suggestions = [];
$stmt2 = $conn->prepare("SELECT id, title, price, image FROM books WHERE category = ? AND id != ? LIMIT 5");
$stmt2->bind_param("si", $category, $book_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($row = $res2->fetch_assoc()) {
    $suggestions[] = $row;
}
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($book['title']) ?></title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background:#f4f5f7; margin:0; padding:0; direction:rtl; }
    .container { max-width:1000px; margin:30px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
    .book-section { display:flex; gap:30px; }
    .book-image img { width:100%; max-width:300px; border-radius:8px; }
    .book-details h2 { margin-top:0; color:#007bff; }
    .book-details p { font-size:16px; margin:8px 0; }
    .price { font-size:20px; color:#28a745; font-weight:bold; margin-top:10px; }
    .btn { display:inline-block; margin-top:15px; padding:10px 20px; background:#007bff; color:#fff; text-decoration:none; border-radius:6px; font-weight:bold; }
    .btn:hover { background:#005fd1; }
    .suggestions { margin-top:40px; }
    .suggestions h3 { margin-bottom:20px; font-size:20px; }
    .suggestion-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:20px; }
    .suggestion-card { background:#f9f9f9; padding:15px; border-radius:8px; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.05); }
    .suggestion-card img { width:100%; max-height:180px; object-fit:cover; border-radius:6px; }
    .suggestion-card h4 { font-size:16px; margin:10px 0 5px; color:#333; }
    .suggestion-card .price { font-size:15px; color:#28a745; font-weight:bold; }
    .suggestion-card .btn { display:inline-block; margin-top:10px; padding:8px 16px; background:#007bff; color:#fff; text-decoration:none; border-radius:6px; font-weight:bold; }
    .suggestion-card .btn:hover { background:#005fd1; }
  </style>
</head>
<body>

<div class="container">
  <!-- تفاصيل الكتاب -->
  <div class="book-section">
    <div class="book-image">
      <img src="uploads/<?= htmlspecialchars($book['image']) ?>" alt="غلاف الكتاب">
    </div>
    <div class="book-details">
      <h2><?= htmlspecialchars($book['title']) ?></h2>
      <p><strong>المؤلف:</strong> <?= htmlspecialchars($book['author']) ?></p>
    
      <p><strong>الطبعة:</strong> <?= htmlspecialchars($book['edition'] ?? 'غير محدد') ?></p>
      <p class="price"><?= htmlspecialchars($book['price']) ?> ﷼</p>

     <form method="POST" action="index.php" style="display:inline;">
    <input type="hidden" name="book_id" value="<?= $book_id ?>">
    <button type="submit" name="add_to_cart" class="btn">🛒 أضف للسلة</button>
</form>
<a href="chat.php?user=<?= $seller_id ?>&book=<?= $book_id ?>" class="btn">💬 تواصل مع البائع</a>
    </div>
  </div>

  <!-- الاقتراحات -->
  <?php if (!empty($suggestions)): ?>
    <div class="suggestions">
      <h3>📚 كتب أخرى في نفس التصنيف</h3>
      <div class="suggestion-grid">
        <?php foreach ($suggestions as $s): ?>
          <div class="suggestion-card">
            <img src="uploads/<?= htmlspecialchars($s['image']) ?>" alt="غلاف">
            <h4><?= htmlspecialchars($s['title']) ?></h4>
            <p class="price"><?= htmlspecialchars($s['price']) ?> ﷼</p>
            <form method="POST" action="index.php" style="display:inline;">
    <input type="hidden" name="book_id" value="<?= $s['id'] ?>">
    <button type="submit" name="add_to_cart" class="btn">أضف للسلة</button>
</form>
<a href="book_details.php?id=<?= $s['id'] ?>" class="btn">عرض التفاصيل</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>  <!-- التذييل الموحد -->

</body>
</html>