
<?php
session_start();
include '../includes/header.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<div class="container mt-5">
    <h1>Admin Dashboard</h1>
    <a href="manage-users.php" class="btn btn-primary">Manage Users</a>
    <a href="complaints.php" class="btn btn-warning">Manage Complaints</a>
</div>

<?php include '../includes/footer.php'; ?>
