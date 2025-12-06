<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>📥 صندوق الرسائل - BookSwap</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: "Tajawal", Arial, sans-serif;
  direction: rtl;
  background: #f2f6ff;
  margin: 0;
  padding: 20px;
}

.container {
  max-width: 800px;
  margin: auto;
  background: #fff;
  padding: 25px 30px;
  border-radius: 15px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

h2 {
  text-align: center;
  color: #1a237e;
  margin-bottom: 20px;
}

.message-card {
  background: #f7f8ff;
  border: 1px solid #d6d9ff;
  padding: 15px 20px;
  border-radius: 10px;
  margin-bottom: 15px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.message-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.message-card p {
  margin: 6px 0;
  font-size: 15px;
}

.message-card .sender {
  font-weight: bold;
  color: #3f51b5;
}

.message-card .time {
  font-size: 13px;
  color: #888;
}
</style>
</head>
<body>

<div class="container">
    <h2>📥 صندوق الرسائل</h2>

    <!-- مثال على رسالة -->
    <div class="message-card">
        <p class="sender">من: أحمد</p>
        <p>مرحبا! أردت إعلامك بوجود كتاب جديد متاح للمبادلة.</p>
        <p class="time">📅 14 نوفمبر 2025 15:30</p>
    </div>

    <div class="message-card">
        <p class="sender">من: سارة</p>
        <p>هل لا زال كتاب "تعلم PHP" متاحًا؟</p>
        <p class="time">📅 13 نوفمبر 2025 12:10</p>
    </div>

    <!-- يمكن تكرار div.message-card لكل رسالة -->
</div>

</body>
</html>
