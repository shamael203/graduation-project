<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

$user_id = $_SESSION['user_id'];

// Get current profile data
$stmt = $conn->prepare("SELECT bio, phone FROM profile WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bio = $_POST["bio"];
    $phone = $_POST["phone"];

    // Check if profile exists
    $check = $conn->prepare("SELECT user_id FROM profile WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;
    $check->close();

    if ($exists) {
        $stmt = $conn->prepare("UPDATE profile SET bio=?, phone=? WHERE user_id=?");
        $stmt->bind_param("ssi", $bio, $phone, $user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO profile (bio, phone, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $bio, $phone, $user_id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: profile.php");
        exit();
    } else {
        $errors[] = "❌ Database error: " . $stmt->error;
        $stmt->close();
    }
}
?>
<?php include 'header.php'; ?> <!-- Common header -->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile</title>
<style>
body { font-family: Arial, sans-serif; background:#f9f9f9; }
form { width: 60%; margin: auto; background:white; padding:20px; border-radius:8px; }
input, textarea { width: 100%; margin-top: 8px; padding: 8px; border:1px solid #ccc; border-radius:5px; }
button { margin-top: 10px; padding:10px; background:#28a745; color:white; border:none; border-radius:5px; cursor:pointer; }
.alert { padding:10px; margin:10px auto; border-radius:5px; text-align:center; }
.alert.error { background:#ffe5e5; color:#b00020; }
</style>
</head>
<body>

<h2 style="text-align:center">Edit Profile</h2>

<!-- Show errors -->
<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $err): ?>
        <div class="alert error"><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="POST">
    <label>📞 Phone:</label>
    <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">

    <label>📝 Bio:</label>
    <textarea name="bio"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>

    <button type="submit">💾 Save Changes</button>
</form>

<?php include 'footer.php'; ?> <!-- Common footer -->

</body>
</html>