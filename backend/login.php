<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            // 🔥 مهم جداً — هنا نضيف السشن
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];

            header("Location: index.php");
            exit;
        } else {
            $error = "كلمة المرور غير صحيحة";
        }

    } else {
        $error = "البريد الإلكتروني غير موجود";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8" />
  <title>تسجيل الدخول - BookSwap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      font-family: "Tajawal", "Segoe UI", Arial, sans-serif;
      direction: rtl;
      background: linear-gradient(135deg, #f0f4ff, #e0ebff);
      color: #333;
      margin: 0;
      padding: 20px;
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
      max-width: 420px;
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

    .note {
      text-align: center;
      margin-top: 15px;
    }

    a {
      color: #3f51b5;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }

    .alert {
      padding: 14px 16px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 15px;
      text-align: center;
      line-height: 1.6;
      animation: fadeIn 0.4s ease-in-out;
    }

    .alert.error {
      background-color: #fdecea;
      color: #b71c1c;
      border: 1px solid #f5c6cb;
      box-shadow: 0 2px 6px rgba(183, 28, 28, 0.15);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-5px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
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
    <h2>تسجيل الدخول</h2>

    <!-- ✅ عرض الرسائل هنا -->
    <?php if (!empty($message)) echo $message; ?>

    <form action="login.php" method="POST" autocomplete="off">
      <label for="email">البريد الإلكتروني</label>
      <input type="email" name="email" id="email" required autocomplete="off" />

      <label for="password">كلمة المرور</label>
      <input type="password" name="password" id="password" required autocomplete="new-password" />

      <button type="submit">تسجيل الدخول</button>

      <div class="note">
        ليس لديك حساب؟ <a href="register.php">سجل الآن</a>
      </div>
    </form>
  </div>
</body>
</html>
