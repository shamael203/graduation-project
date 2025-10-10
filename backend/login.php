<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();


    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_name"] = $row["name"];
            header("Location: welcome.php");
            exit();
        } else {
            echo "<p style='color:red;'>❌ كلمة المرور غير صحيحة</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ البريد الإلكتروني غير موجود</p>";
    }
}
?>


<!doctype html>
<html lang="ar">
<head>
  <meta charset="utf-8">
  <title>تسجيل الدخول - BookSwap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: Arial, sans-serif;
      direction: rtl;
      background-color: #f5f5f5;
      padding: 20px;
    }

    .container {
      max-width: 400px;
      margin: 40px auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #1a237e;
      margin-bottom: 25px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #333;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      box-sizing: border-box;
    }

    input:focus {
      border-color: #3f51b5;
      box-shadow: 0 0 4px rgba(63, 81, 181, 0.3);
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
    }

    button:hover {
      background: #303f9f;
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
  </style>
</head>
<body>
  <div class="container">
    <h2>تسجيل الدخول</h2>
    <form action="login.php" method="POST">
      <label for="email">البريد الإلكتروني</label>
     <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" autocomplete="off" required>


      <label for="password">كلمة المرور</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">دخول</button>

      <div class="note">
        ليس لديك حساب؟ <a href="register.html">سجّل الآن</a>
      </div>
    </form>
  </div>
</body>
</html>
