
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$job_id = $_GET['id'] ?? null;
if (!$job_id) {
    echo "<div class='container mt-5'><h1>Job Not Found</h1></div>";
    include '../includes/footer.php';
    exit;
}

// Fetch job details
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    echo "<div class='container mt-5'><h1>Job Not Found</h1></div>";
    include '../includes/footer.php';
    exit;
}

?>

<div class="container mt-5">
    <h1><?= htmlspecialchars($job['title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
    <p><strong>Budget:</strong> Rs.<?= $job['budget'] ?></p>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'freelancer') : ?>
        <div class="mt-4">
            <h3>Submit Your Proposal</h3>
            <form method="POST" action="../proposals/submit-proposal.php" class="card p-4 shadow-sm">
                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">Proposal Text</label>
                    <textarea name="proposal_text" class="form-control" rows="5" placeholder="Describe your approach to the job" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Proposed Amount (Rs.)</label>
                    <input type="number" name="amount" class="form-control" placeholder="Enter your bid amount" required>
                </div>
                <button type="submit" class="btn btn-success">Submit Proposal</button>
            </form>
        </div>
    <?php else: ?>
        <p class="text-muted">Only freelancers can submit proposals. Please <a href="../auth/login.php">login</a> as a freelancer to apply for this job.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
