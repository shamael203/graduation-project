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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Welcome Page</title>
<style>
  body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
   background: linear-gradient(135deg, #f0f4ff, #e0ebff);
    margin: 0;
    font-family: 'Poppins', sans-serif;
    color: #fff;
    text-align: center;
    flex-direction: column;
    padding: 20px;
  }

  h1 {
  font-size: 32px;
  margin-bottom: 10px;
  color: #000; /* لون العنوان أسود */
}

  p {
    font-size: 18px;
    margin-bottom: 25px;
    color: #040000ff;
  }

  .logout-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px 22px;
    background: #ff3e3e;
    color: #fff;
    font-size: 18px;
    font-weight: bold;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 10px 20px rgba(255, 62, 62, 0.5);
    text-decoration: none; /* مهم للروابط */
  }

  .logout-btn .icon {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    background: url('https://img.icons8.com/ios-filled/50/ffffff/logout-rounded-left.png') no-repeat center center / contain;
    transition: transform 0.4s ease;
  }

  .logout-btn .text {
    position: relative;
    z-index: 1;
    transition: transform 0.4s ease;
  }

  .logout-btn::before, .logout-btn::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 140%;
    height: 140%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15), transparent);
    transition: all 0.6s ease;
    border-radius: 50%;
    z-index: 0;
    transform: translate(-50%, -50%) scale(0);
  }

  .logout-btn:hover::before {
    transform: translate(-50%, -50%) scale(1);
  }

  .logout-btn:hover .icon {
    transform: translateX(-10px) rotate(-360deg);
  }

  .logout-btn:hover .text {
    transform: translateX(10px);
  }

  .logout-btn:hover {
    background: #ff1e1e;
    transform: scale(1.08);
  }
</style>
</head>
<body>

  <h1>👋 Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
  <p>We’re happy to see you again 🌸</p>
  <p>Your registered email: <strong><?php echo htmlspecialchars($email); ?></strong></p>

  <!-- زر تسجيل الخروج -->
  <a href="logout.php" class="logout-btn">
    <div class="icon"></div>
    <div class="text">تسجيل خروج</div>
  </a>

</body>
</html>
