<?php include 'db.php'; ?>

<!doctype html>
<html lang="ar">
<head>
  <meta charset="utf-8">
  <title>إضافة كتاب - BookSwap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    body {
      font-family: "Tajawal", "Segoe UI", Arial, sans-serif;
      direction: rtl;
      background: linear-gradient(135deg, #f0f4ff, #e0ebff);
      color: #333;
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .container {
      background: #fff;
      padding: 35px 40px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 440px;
      width: 100%;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .container:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    h2 {
      text-align: center;
      color: #1a237e;
      margin-bottom: 25px;
      font-size: 1.6rem;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
      color: #333;
    }

    input {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      box-sizing: border-box;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus {
      border-color: #3f51b5;
      box-shadow: 0 0 4px rgba(63, 81, 181, 0.4);
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #3f51b5;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.1s ease;
    }

    button:hover {
      background: #2c3e9a;
      transform: scale(1.02);
    }

    .alert {
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 10px;
      font-weight: 600;
      text-align: center;
      animation: fadeIn 0.4s ease;
    }

    .alert.error {
      background: #ffe5e5;
      color: #b00020;
      border: 1px solid #f5b7b1;
    }

    .alert.success {
      background: #e9f7ef;
      color: #006400;
      border: 1px solid #a9dfbf;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 500px) {
      .container {
        padding: 25px 20px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>📚 إضافة كتاب جديد</h2>

    <!-- رسائل النجاح أو الخطأ -->
    <?php
    if (isset($_POST['save'])) {
      $title = $_POST['title'];
      $author = $_POST['author'];
      $edition = $_POST['edition'];
      $price = $_POST['price'];

      $sql = "INSERT INTO books (title, author, edition, price)
              VALUES ('$title', '$author', '$edition', '$price')";

      if ($conn->query($sql)) {
        echo "<div class='alert success'>✅ تم حفظ الكتاب بنجاح!</div>";
      } else {
        echo "<div class='alert error'>❌ حدث خطأ: " . $conn->error . "</div>";
      }
    }
    ?>

    <form method="POST">
      <label for="title">عنوان الكتاب</label>
      <input id="title" name="title" type="text" required maxlength="255" placeholder="أدخل عنوان الكتاب">

      <label for="author">اسم المؤلف</label>
      <input id="author" name="author" type="text" required maxlength="255" placeholder="اسم المؤلف">

      <label for="edition">الطبعة</label>
      <input id="edition" name="edition" type="text" maxlength="100" placeholder="مثلاً: الطبعة الثانية">

      <label for="price">السعر (ر.س)</label>
      <input id="price" name="price" type="number" step="0.01" required placeholder="أدخل السعر">

      <button type="submit" name="save">💾 حفظ الكتاب</button>
    </form>
  </div>
</body>
</html>
