<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'freelancer') {
    header('Location: ../auth/login.php');
    exit;
}

$freelancer_id = $_SESSION['user_id'];

// Fetch released payments
$stmt = $pdo->prepare("
    SELECT * FROM payments 
    WHERE freelancer_id = ? 
    AND status = 'released'
");
$stmt->execute([$freelancer_id]);
$payments = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h1>Withdraw Funds</h1>
    <table class="table table-bordered">
        <thead>
            <tr><th>Job ID</th><th>Amount</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php if (count($payments) > 0): ?>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= $payment['job_id'] ?></td>
                        <td>Rs.<?= $payment['amount'] ?></td>
                        <td><?= ucfirst($payment['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">No funds available for withdrawal.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
