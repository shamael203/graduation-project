<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // تحقق إذا البريد موجود مسبقًا
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<p style='color:red;'>⚠️ هذا البريد الإلكتروني مستخدم مسبقًا</p>";
    } else {
        // البريد غير موجود، نسجل المستخدم

        // تشفير كلمة المرور
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // الاستعلام المحضر للإضافة
        $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt_insert->execute()) {
            echo "<p style='color:green;'>✅ تم التسجيل بنجاح</p>";
        } else {
            echo "<p style='color:red;'>⚠️ حدث خطأ: " . $stmt_insert->error . "</p>";
        }
        $stmt_insert->close();
    }

    $stmt->close();
}
?>


<!doctype html>
<html lang="ar">
<head>
  <meta charset="utf-8">
  <title>تسجيل مستخدم - BookSwap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    /* الأساسيات */
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

    /* الصندوق الرئيسي */
    .container {
      background: #fff;
      padding: 35px 40px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 420px;
      width: 100%;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .container:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* العنوان */
    h2 {
      text-align: center;
      color: #1a237e;
      margin-bottom: 25px;
      font-size: 1.6rem;
    }

    /* التسميات والحقول */
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

    /* الزر */
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

    /* الرسائل */
    .error {
      color: #b00020;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .success {
      color: #006400;
      font-size: 14px;
      margin-bottom: 10px;
    }

    /* للهواتف */
    @media (max-width: 500px) {
      .container {
        padding: 25px 20px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>تسجيل مستخدم جديد</h2>
    <form action="register.php" method="POST" novalidate>
      <label for="name">الاسم</label>
      <input id="name" name="name" type="text" required minlength="2" maxlength="100" placeholder="اكتب اسمك الكامل">

      <label for="email">البريد الإلكتروني</label>
      <input id="email" name="email" type="email" required maxlength="255" placeholder="example@email.com">

      <label for="password">كلمة المرور</label>
      <input id="password" name="password" type="password" required minlength="8" placeholder="أدخل كلمة مرور قوية">

      <button type="submit">سجّل الآن</button>
    </form>
  </div>
</body>
</html>
