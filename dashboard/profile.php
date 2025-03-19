
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$username, $user_id]);
    $user['username'] = $username;
    $message = "Profile updated successfully!";
}
?>

<div class="container mt-5">
    <h1>My Profile</h1>
    <?php if (isset($message)) : ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <input type="text" class="form-control" value="<?= $user['role'] ?>" disabled>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
