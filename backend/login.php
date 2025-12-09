<?php
session_start();
include 'connect.php';

// تضمين الهيدر
include 'header.php';

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
    /* ... هنا يبقى نفس الـ CSS الأصلي بدون أي تغيير ... */
  </style>
</head>
<body>
  <div class="container">
    <h2>تسجيل الدخول</h2>

    <!-- ✅ عرض الرسائل هنا -->
    <?php if (!empty($error)) : ?>
      <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

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

  <?php
  // تضمين الفوتر
  include 'footer.php';
  ?>
</body>
</html>
