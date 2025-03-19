
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Example disputes table could be extended later in the database
$disputes = [
    ['id' => 1, 'job_id' => 101, 'freelancer' => 'JohnDoe', 'client' => 'ACME Corp', 'reason' => 'Payment Delay', 'status' => 'Open'],
    ['id' => 2, 'job_id' => 102, 'freelancer' => 'DevMaster', 'client' => 'TechCo', 'reason' => 'Scope Change', 'status' => 'Resolved']
];

?>
<div class="container mt-5">
    <h1>Manage Disputes</h1>
    <table class="table table-bordered">
        <tr><th>ID</th><th>Job ID</th><th>Freelancer</th><th>Client</th><th>Reason</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($disputes as $dispute) : ?>
        <tr>
            <td><?= $dispute['id'] ?></td>
            <td><?= $dispute['job_id'] ?></td>
            <td><?= $dispute['freelancer'] ?></td>
            <td><?= $dispute['client'] ?></td>
            <td><?= $dispute['reason'] ?></td>
            <td><?= $dispute['status'] ?></td>
            <td>
                <?php if ($dispute['status'] === 'Open'): ?>
                    <a href="#" class="btn btn-success btn-sm">Resolve</a>
                <?php else: ?>
                    <span class="text-muted">Resolved</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include '../includes/footer.php'; ?>
