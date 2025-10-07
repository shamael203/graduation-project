<?php
include 'boo_exchange.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // تشفير كلمة المرور

    $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>✅ تم التسجيل بنجاح</p>";
    } else {
        echo "<p style='color:red;'>⚠️ حدث خطأ: " . $conn->error . "</p>";
    }
}
?>

<h2>تسجيل مستخدم جديد</h2>
<form method="POST">
    الاسم: <input type="text" name="name" required><br><br>
    البريد الإلكتروني: <input type="email" name="email" required><br><br>
    كلمة المرور: <input type="password" name="password" required><br><br>
    <button type="submit">تسجيل</button>
</form>