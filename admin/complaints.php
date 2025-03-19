
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Handle status update
if (isset($_GET['mark_reviewed'])) {
    $stmt = $pdo->prepare("UPDATE complaints SET status = 'reviewed' WHERE id = ?");
    $stmt->execute([$_GET['mark_reviewed']]);
    echo "<div class='alert alert-success'>Complaint marked as reviewed.</div>";
}

if (isset($_GET['mark_resolved'])) {
    $stmt = $pdo->prepare("UPDATE complaints SET status = 'resolved' WHERE id = ?");
    $stmt->execute([$_GET['mark_resolved']]);
    echo "<div class='alert alert-success'>Complaint marked as resolved.</div>";
}

// Fetch all complaints
$stmt = $pdo->query("SELECT * FROM complaints ORDER BY created_at DESC");
$complaints = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h1>Manage Complaints</h1>
    <table class="table table-bordered table-striped">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($complaints as $complaint) : ?>
        <tr>
            <td><?= $complaint['id'] ?></td>
            <td><?= htmlspecialchars($complaint['name']) ?></td>
            <td><?= htmlspecialchars($complaint['email']) ?></td>
            <td><?= htmlspecialchars($complaint['message']) ?></td>
            <td><?= ucfirst($complaint['status']) ?></td>
            <td>
                <?php if ($complaint['status'] === 'pending'): ?>
                    <a href="?mark_reviewed=<?= $complaint['id'] ?>" class="btn btn-warning btn-sm">Mark Reviewed</a>
                <?php endif; ?>
                <?php if ($complaint['status'] !== 'resolved'): ?>
                    <a href="?mark_resolved=<?= $complaint['id'] ?>" class="btn btn-success btn-sm">Mark Resolved</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
