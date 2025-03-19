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
    <div class="card p-4 mx-auto shadow-lg border-0" style="max-width: 400px; border-radius: 15px;">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Welcome Back</h2>
            <p class="text-muted">Enter your credentials to access your account</p>
        </div>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 mb-3" style="border-radius: 8px;">Log In</button>
            <div class="text-center">
                <a href="../auth/register.php" class="text-decoration-none">Don't have an account? Sign up</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>