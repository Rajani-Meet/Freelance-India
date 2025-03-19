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
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>My Profile</h1>
                <a href="../dashboard/<?= $_SESSION['role'] ?>-dashboard.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
            
            <?php if (isset($message)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white mx-auto mb-3" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                        <h3><?= htmlspecialchars($user['username']) ?></h3>
                        <span class="badge bg-success"><?= $user['role'] ?></span>
                    </div>
                    
                    <form method="POST" class="mt-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user-tag"></i></span>
                                <input type="text" class="form-control" value="<?= $user['role'] ?>" disabled>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Member Since</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar"></i></span>
                                <input type="text" class="form-control" value="March 10, 2025" disabled>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2">
                                <i class="fas fa-save me-2"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px;">
                <div class="card-header bg-white py-3" style="border-radius: 15px 15px 0 0;">
                    <h4 class="mb-0">Account Security</h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-outline-primary" onclick="alert('Password change functionality would be implemented here')">
                            <i class="fas fa-key me-2"></i> Change Password
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="alert('Account deletion functionality would be implemented here')">
                            <i class="fas fa-trash-alt me-2"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome if not already included in header -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>

<?php include '../includes/footer.php'; ?>