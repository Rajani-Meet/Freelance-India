<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'freelancer') {
    header('Location: ../auth/login.php');
    exit;
}

$job_id = $_GET['job_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO proposals (job_id, freelancer_id, proposal_text, amount) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$_POST['job_id'], $_SESSION['user_id'], $_POST['proposal_text'], $_POST['amount']])) {
        ?>
        <div class="container mt-4">
            <div class="alert alert-success">
                <h4 class="alert-heading">Proposal Submitted!</h4>
                <p>Your proposal for <strong>Job #<?= htmlspecialchars($_POST['job_id']) ?></strong> has been submitted successfully. The client will review and contact you if interested.</p>
                <hr>
                <a href="../dashboard/freelancer-dashboard.php" class="btn btn-outline-primary">Go to Dashboard</a>
                <a href="../jobs/browse-jobs.php" class="btn btn-outline-success">Browse More Jobs</a>
            </div>
        </div>
        <?php
        include '../includes/footer.php';
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to submit your proposal. Please try again.</div>";
    }
}
