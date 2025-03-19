
<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $redirect = match($user['role']) {
            'admin' => '../admin/dashboard.php',
            'client' => '../dashboard/client-dashboard.php',
            default => '../dashboard/freelancer-dashboard.php',
        };
        header("Location: $redirect");
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="card p-4 mx-auto" style="max-width: 400px;">
        <h2 class="text-center">Login</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
        