<?php
session_start();
include 'connect.php';

// Include header
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

            // Add session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_email'] = $row['email'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "Email not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login - BookSwap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    /* Keep your original CSS here */
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>

    <!-- Display error messages -->
    <?php if (!empty($error)) : ?>
      <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" autocomplete="off">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required autocomplete="off" />

      <label for="password">Password</label>
      <input type="password" name="password" id="password" required autocomplete="new-password" />

      <button type="submit">Login</button>

      <div class="note">
        Don't have an account? <a href="register.php">Register now</a>
      </div>
    </form>
  </div>

  <?php
  // Include footer
  include 'footer.php';
  ?>
</body>
</html>