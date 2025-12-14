<?php
session_start();
session_unset();
session_destroy();

// Auto redirect after 3 seconds
header("refresh:3;url=home.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logout</title>
  <style>
    body {
      background: linear-gradient(135deg, #f0f4ff, #e0ebff);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      text-align: center;
      color: #000000ff;
    }

    p {
      font-size: 26px;
      font-weight: bold;
      margin: 15px 0;
    }

    .dots::after {
      content: '';
      display: inline-block;
      animation: dots 1.2s steps(3, end) infinite;
    }

    @keyframes dots {
      0%   { content: ''; }
      33%  { content: '.'; }
      66%  { content: '..'; }
      100% { content: '...'; }
    }
  </style>
</head>
<body>

  <p>You have successfully logged out</p>


  
</body>
</html>