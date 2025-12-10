<?php
include 'connect.php';

// Include header
include 'header.php';

$message = ""; // Variable to store messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validate fields
    if (empty($name) || empty($email) || empty($password)) {
        $message = "<div class='alert error'>All fields are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert error'>Invalid email address.</div>";
    } elseif (strlen($password) < 8) {
        $message = "<div class='alert error'>Password must be at least 8 characters long.</div>";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<div class='alert error'>Email is already registered. Please use another email.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                $message = "<div class='alert success'>Registration successful! Welcome to BookSwap.</div>";
                header("Location: login.php");
                exit;
            } else {
                $message = "<div class='alert error'>An error occurred during registration. Please try again later.</div>";
            }
            $stmt_insert->close();
        }

        $stmt->close();
    }
}
?>

<div class="container">
  <h2>Register New User</h2>

  <?php if (!empty($message)) echo $message; ?>

  <form action="register.php" method="POST" novalidate>
    <label for="name">Name</label>
    <input id="name" name="name" type="text" required minlength="2" maxlength="100" placeholder="Enter your full name">

    <label for="email">Email</label>
    <input id="email" name="email" type="email" required maxlength="255" placeholder="example@email.com" autocomplete="off">

    <label for="password">Password</label>
    <input id="password" name="password" type="password" required minlength="8" placeholder="Enter a strong password" autocomplete="new-password">

    <button type="submit">Register Now</button>
  </form>
</div>

<?php
// Include footer
include 'footer.php';
?>