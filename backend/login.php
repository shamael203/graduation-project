<?php
session_start();
include 'book_exchange.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

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

<h2>تسجيل الدخول</h2>
<form method="POST">
    البريد الإلكتروني: <input type="email" name="email" required><br><br>
    كلمة المرور: <input type="password" name="password" required><br><br>
    <button type="submit">دخول</button>
</form>