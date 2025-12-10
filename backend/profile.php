<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connect.php';

// Include header
include 'header.php';

// Fetch user data
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch profile info
$stmt2 = $conn->prepare("SELECT bio, phone, avatar FROM profile WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$profile = $stmt2->get_result()->fetch_assoc();
$stmt2->close();

// Default avatar
$avatar = "uploads/default-avatar.png";
if (!empty($profile["avatar"]) && file_exists($profile["avatar"])) {
    $avatar = $profile["avatar"];
}

// Fetch user's books
$stmt3 = $conn->prepare("SELECT title, author, edition, price, image FROM books WHERE user_id = ?");
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$books = $stmt3->get_result();
$stmt3->close();
?>

<div class="container">
    <h2>Profile</h2>

    <img src="<?= $avatar ?>" alt="Avatar">

    <p><strong>Name:</strong> <?= $user['name'] ?></p>
    <p><strong>Email:</strong> <?= $user['email'] ?></p>
    <p><strong>Phone:</strong> <?= $profile['phone'] ?? '-' ?></p>
    <p><strong>Bio:</strong> <?= nl2br($profile['bio'] ?? '-') ?></p>

    <br>
    <a class="button" href="edit_profile.php">Edit Profile</a>
    <hr>
    <h3>My Books</h3>
<?php while($book = $books->fetch_assoc()): ?>
    <div style="margin-bottom:20px;">
        <?php if(!empty($book['image'])): ?>
            <img src="uploads/<?= $book['image'] ?>" style="width:100px; height:140px; border-radius:6px;">
        <?php endif; ?>

        <p><strong>Title:</strong> <?= $book['title'] ?></p>
        <p><strong>Author:</strong> <?= $book['author'] ?></p>
        <p><strong>Edition:</strong> <?= $book['edition'] ?></p>
        <p><strong>Price:</strong> <?= $book['price'] ?> SAR</p>
        <hr>
    </div>
<?php endwhile; ?>
</div>

<?php
// Include footer
include 'footer.php';
?>