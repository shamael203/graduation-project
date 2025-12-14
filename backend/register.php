<?php
include 'connect.php';



$message = ""; // variable to store messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    // validate fields
    if (empty($name) || empty($email) || empty($password)) {
        $message = "<div class='alert error'>⚠️ All fields are required.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert error'>⚠️ Invalid email address.</div>";
    } elseif (strlen($password) < 8) {
        $message = "<div class='alert error'>⚠️ Password must be at least 8 characters long.</div>";
    } else {
        // check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<div class='alert error'>⚠️ Email already registered. Please use another one.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                $message = "<div class='alert success'>✅ Registration successful! Welcome to BookSwap.</div>";
                header("Location: login.php");
                exit;
            } else {
                $message = "<div class='alert error'>⚠️ An error occurred during registration. Please try again later.</div>";
            }
            $stmt_insert->close();
        }

        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>User Registration - BookSwap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      direction: ltr;
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
    <h2>Register New User</h2>
    <?php if (!empty($message)) echo $message; ?>

    <form action="register.php" method="POST" novalidate>
      <label for="name">Full Name</label>
      <input id="name" name="name" type="text" required minlength="2" maxlength="100" placeholder="Enter your full name">

      <label for="email">Email Address</label>
      <input id="email" name="email" type="email" required maxlength="255" placeholder="example@email.com" autocomplete="off">

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required minlength="8" placeholder="Enter a strong password" autocomplete="new-password">

      <button type="submit">Register Now</button>
    </form>
  </div>
</body>
</html>

