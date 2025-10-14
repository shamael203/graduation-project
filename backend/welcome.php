<?php
session_start();

if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['user_name'];
$email = $_SESSION['user_email'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome Page</title>
<style>
    body {
        background: linear-gradient(120deg, #89f7fe, #66a6ff);
        font-family: 'Poppins', sans-serif;
        text-align: center;
        color: #333;
        padding-top: 100px;
    }

    .welcome-box {
        background-color: #fff;
        width: 380px;
        margin: auto;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
    }

    h1 {
        color: #333;
        font-size: 28px;
        margin-bottom: 10px;
    }

    p {
        color: #555;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .logout-btn {
        background-color: #ff4b5c;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: 0.3s;
    }

    .logout-btn:hover {
        background-color: #ff1e2b;
        transform: scale(1.05);
    }
</style>
</head>
<body>

    <div class="welcome-box">
        <h1>👋 Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
        <p>We’re happy to see you again 🌸</p>
        <p>Your registered email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
    </div>

</body>
</html>
