<?php
include 'connect.php';

// تضمين الهيدر
include 'header.php';

$message = ""; // متغير لتخزين الرسائل

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // تحقق من الحقول
    if (empty($name) || empty($email) || empty($password)) {
        $message = "<div class='alert error'>⚠️ جميع الحقول مطلوبة.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert error'>⚠️ البريد الإلكتروني غير صالح.</div>";
    } elseif (strlen($password) < 8) {
        $message = "<div class='alert error'>⚠️ يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل.</div>";
    } else {
        // تحقق إذا البريد موجود مسبقًا
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<div class='alert error'>⚠️ البريد الإلكتروني مسجل مسبقًا. يرجى استخدام بريد آخر.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                $message = "<div class='alert success'>✅ تم التسجيل بنجاح! مرحبًا بك في BookSwap.</div>";
                header("Location: login.php");
                exit;
            } else {
                $message = "<div class='alert error'>⚠️ حدث خطأ أثناء التسجيل. حاول مرة أخرى لاحقًا.</div>";
            }
            $stmt_insert->close();
        }

        $stmt->close();
    }
}
?>

<div class="container">
  <h2>تسجيل مستخدم جديد</h2>

  <?php if (!empty($message)) echo $message; ?>

  <form action="register.php" method="POST" novalidate>
    <label for="name">الاسم</label>
    <input id="name" name="name" type="text" required minlength="2" maxlength="100" placeholder="اكتب اسمك الكامل">

    <label for="email">البريد الإلكتروني</label>
    <input id="email" name="email" type="email" required maxlength="255" placeholder="example@email.com" autocomplete="off">

    <label for="password">كلمة المرور</label>
    <input id="password" name="password" type="password" required minlength="8" placeholder="أدخل كلمة مرور قوية" required autocomplete="new-password">

    <button type="submit">سجّل الآن</button>
  </form>
</div>

<?php
// تضمين الفوتر
include 'footer.php';
?>
