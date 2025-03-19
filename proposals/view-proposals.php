<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'client') {
    header('Location: ../auth/login.php');
    exit;
}

// Handle Accept Proposal (create escrow payment)
if (isset($_GET['accept_proposal'])) {
    $proposal_id = $_GET['accept_proposal'];

    $stmt = $pdo->prepare("
        SELECT proposals.*, jobs.client_id 
        FROM proposals 
        JOIN jobs ON proposals.job_id = jobs.id 
        WHERE proposals.id = ?
    ");
    $stmt->execute([$proposal_id]);
    $proposal = $stmt->fetch();

    if ($proposal) {
        $insertPayment = $pdo->prepare("
            INSERT INTO payments (job_id, freelancer_id, amount, status) 
            VALUES (?, ?, ?, 'pending')
        ");
        $insertPayment->execute([$proposal['job_id'], $proposal['freelancer_id'], $proposal['amount']]);
        ?>
        <div class="container mt-4">
            <div class="alert alert-success">
                <h4 class="alert-heading">Proposal Accepted!</h4>
                <p>You have accepted <strong>Proposal #<?= $proposal_id ?></strong>. Payment has been held in escrow.</p>
                <hr>
                <a href="view-proposals.php" class="btn btn-outline-primary">Refresh Proposals</a>
            </div>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger'>Proposal not found.</div>";
    }
}

// Handle Reject Proposal
if (isset($_GET['reject_proposal'])) {
    $proposal_id = $_GET['reject_proposal'];
    ?>
    <div class="container mt-4">
        <div class="alert alert-warning">
            <h4 class="alert-heading">Proposal Rejected!</h4>
            <p>You have rejected <strong>Proposal #<?= $proposal_id ?></strong>.</p>
            <hr>
            <a href="view-proposals.php" class="btn btn-outline-primary">Refresh Proposals</a>
        </div>
    </div>
    <?php
}

// Handle Release Payment
if (isset($_GET['release_payment'])) {
    $proposal_id = $_GET['release_payment'];

    $paymentStmt = $pdo->prepare("
        SELECT payments.* 
        FROM payments 
        JOIN proposals ON payments.job_id = proposals.job_id 
        WHERE proposals.id = ? 
        AND payments.freelancer_id = proposals.freelancer_id
    ");
    $paymentStmt->execute([$proposal_id]);
    $payment = $paymentStmt->fetch();

    if ($payment && $payment['status'] === 'pending') {
        $releaseStmt = $pdo->prepare("UPDATE payments SET status = 'released' WHERE id = ?");
        $releaseStmt->execute([$payment['id']]);
        ?>
        <div class="container mt-4">
            <div class="alert alert-success">
                <h4 class="alert-heading">Payment Released!</h4>
                <p>The payment for <strong>Proposal #<?= $proposal_id ?></strong> has been successfully released to the freelancer.</p>
                <hr>
                <a href="view-proposals.php" class="btn btn-outline-primary">Refresh Proposals</a>
            </div>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger'>Payment not found or already released.</div>";
    }
}

// Fetch all proposals
$stmt = $pdo->prepare("
    SELECT proposals.*, jobs.title AS job_title, users.username AS freelancer
    FROM proposals
    JOIN jobs ON proposals.job_id = jobs.id
    JOIN users ON proposals.freelancer_id = users.id
    WHERE jobs.client_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$proposals = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h1>View Proposals</h1>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Freelancer</th>
                <th>Proposal</th>
                <th>Amount</th>
                <th>Work Submission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proposals as $proposal) : ?>
                <tr>
                    <td><?= htmlspecialchars($proposal['job_title']) ?></td>
                    <td><?= htmlspecialchars($proposal['freelancer']) ?></td>
                    <td><?= nl2br(htmlspecialchars($proposal['proposal_text'])) ?></td>
                    <td>Rs.<?= $proposal['amount'] ?></td>
                    <td>
                        <?php
                        $stmtWork = $pdo->prepare("SELECT * FROM work_submissions WHERE proposal_id = ?");
                        $stmtWork->execute([$proposal['id']]);
                        $work = $stmtWork->fetch();

                        if ($work) {
                            echo "<strong>Description:</strong> " . nl2br(htmlspecialchars($work['work_description'])) . "<br>";
                            if ($work['file_path']) {
                                echo "<a href='" . htmlspecialchars($work['file_path']) . "' download>Download Work File</a><br>";
                            }

                            $paymentStmt = $pdo->prepare("
                                SELECT * 
                                FROM payments 
                                WHERE job_id = ? 
                                AND freelancer_id = ?
                            ");
                            $paymentStmt->execute([$proposal['job_id'], $proposal['freelancer_id']]);
                            $payment = $paymentStmt->fetch();

                            if ($payment && $payment['status'] === 'pending') {
                                echo "<a href='?release_payment={$proposal['id']}' class='btn btn-success btn-sm mt-2'>Release Payment</a>";
                            } elseif ($payment && $payment['status'] === 'released') {
                                echo "<span class='badge bg-success mt-2'>Payment Released</span>";
                            }
                        } else {
                            echo "<span class='text-muted'>No work submitted yet.</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="?accept_proposal=<?= $proposal['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Accept this proposal and fund payment?');">Accept & Pay</a>
                        <a href="?reject_proposal=<?= $proposal['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this proposal?');">Reject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
