<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'freelancer') {
    header('Location: ../auth/login.php');
    exit;
}

$job_id = $_GET['job_id'] ?? null;

// Fetch job details
$jobStmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$jobStmt->execute([$job_id]);
$job = $jobStmt->fetch();


// Check if user already submitted a proposal
$checkStmt = $pdo->prepare("SELECT * FROM proposals WHERE job_id = ? AND freelancer_id = ?");
$checkStmt->execute([$job_id, $_SESSION['user_id']]);
$existingProposal = $checkStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO proposals (job_id, freelancer_id, proposal_text, amount) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$_POST['job_id'], $_SESSION['user_id'], $_POST['proposal_text'], $_POST['amount']])) {
        ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h2 class="card-title mb-3">Proposal Submitted Successfully!</h2>
                            <p class="card-text text-muted mb-4">Your proposal has been submitted successfully. The client will review your proposal and contact you if interested.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="../dashboard/freelancer-dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                                <a href="../jobs/browse-jobs.php" class="btn btn-outline-primary">Browse More Jobs</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../includes/footer.php';
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to submit your proposal. Please try again.</div>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard/freelancer-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="../jobs/browse-jobs.php">Browse Jobs</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Submit Proposal</li>
                </ol>
            </nav>

            <?php if ($existingProposal): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    You have already submitted a proposal for this job.
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h3 class="mb-0">Job Details</h3>
                </div>
                <div class="card-body">
                    <h4><?= htmlspecialchars($job['title']) ?></h4>
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">Budget: ₹<?= $job['budget'] ?></span>
                        <span class="badge bg-info me-2">Category: <?= htmlspecialchars($job['category']) ?></span>
                    </div>
                    <p class="card-text"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h3 class="mb-0">Submit Your Proposal</h3>
                </div>
                <div class="card-body">
                    <form method="POST" id="proposalForm" novalidate>
                        <input type="hidden" name="job_id" value="<?= $job_id ?>">
                        
                        <div class="mb-4">
                            <label for="amount" class="form-label fw-bold">Your Bid (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" name="amount" id="amount" class="form-control" required step="0.01" min="1" value="<?= $job['budget'] ?>">
                            </div>
                            <div class="form-text">Enter your bid amount for this project.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="proposal_text" class="form-label fw-bold">Proposal Details</label>
                            <textarea name="proposal_text" id="proposal_text" class="form-control" rows="10" required 
                                placeholder="Introduce yourself, explain why you're the best fit for this job, and detail your approach to completing this project successfully."></textarea>
                            <div class="form-text">Be specific about your skills and experience relevant to this project.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"<?= $existingProposal ? ' disabled' : '' ?>>
                                <?= $existingProposal ? 'Proposal Already Submitted' : 'Submit Proposal' ?>
                            </button>
                            <a href="../jobs/browse-jobs.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('proposalForm');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?php include '../includes/footer.php'; ?>