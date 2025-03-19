
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

if (isset($_GET['delete_user_id'])) {
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$_GET['delete_user_id']]);
    echo "<div class='alert alert-success'>User Deleted Successfully</div>";
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>
<div class="container mt-5">
    <h1>Manage Users</h1>
    <table class="table table-bordered">
        <tr><th>ID</th><th>Username</th><th>Role</th><th>Action</th></tr>
        <?php foreach ($users as $user) : ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= $user['role'] ?></td>
            <td>
                <a href="manage-users.php?delete_user_id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
