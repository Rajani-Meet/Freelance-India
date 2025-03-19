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
$stmt = $pdo->prepare("SELECT j.*, u.username AS client_name FROM jobs j 
                      LEFT JOIN users u ON j.client_id = u.id 
                      WHERE j.id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    echo "<div class='container mt-5'><h1>Job Not Found</h1></div>";
    include '../includes/footer.php';
    exit;
}

// Check if user already submitted a proposal
$hasProposal = false;
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'freelancer') {
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM proposals WHERE job_id = ? AND freelancer_id = ?");
    $checkStmt->execute([$job_id, $_SESSION['user_id']]);
    $hasProposal = ($checkStmt->fetchColumn() > 0);
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Back button -->
            <a href="browse-jobs.php" class="btn btn-outline-secondary mb-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Jobs
            </a>
            
            <!-- Job details card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <span class="badge bg-success mb-3">Rs.<?= number_format($job['budget']) ?></span>
                    <h1 class="display-6 fw-bold mb-3"><?= htmlspecialchars($job['title']) ?></h1>
                    
                    <div class="d-flex align-items-center text-muted mb-4">
                        <div class="me-4">
                            <i class="bi bi-person-circle me-2"></i>
                            Posted by <?= htmlspecialchars($job['client_name'] ?? 'Anonymous') ?>
                        </div>
                        <div>
                            <i class="bi bi-calendar me-2"></i>
                            <?= date('F d, Y', strtotime($job['created_at'] ?? 'now')) ?>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-3">Project Description</h5>
                    <div class="job-description mb-4">
                        <?= nl2br(htmlspecialchars($job['description'])) ?>
                    </div>
                    
                    <div class="d-flex flex-wrap">
                        <div class="me-4 mb-3">
                            <div class="text-muted small">Budget</div>
                            <div class="fw-bold">Rs.<?= number_format($job['budget']) ?></div>
                        </div>
                        <div class="me-4 mb-3">
                            <div class="text-muted small">Status</div>
                            <div class="fw-bold">
                                <span class="text-success">
                                    <i class="bi bi-circle-fill me-1 small"></i>Open for proposals
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Proposal submission -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'freelancer') : ?>
                <?php if ($hasProposal): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Proposal Submitted</strong>
                        <p class="mb-0">You have already submitted a proposal for this job.</p>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Submit Your Proposal</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST" action="../proposals/submit-proposal.php">
                                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Your Approach</label>
                                    <textarea name="proposal_text" class="form-control" rows="5" 
                                    placeholder="Describe how you would handle this project..." required></textarea>
                                    <div class="form-text">Be specific about your relevant experience and approach.</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Your Bid (Rs.)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="number" name="amount" class="form-control" 
                                        placeholder="0" min="1" value="<?= $job['budget'] ?>" required>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send me-2"></i>Submit Proposal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-lock-fill text-muted display-4"></i>
                        <h5 class="mt-3">Freelancer Access Only</h5>
                        <p class="text-muted">You need to be logged in as a freelancer to submit a proposal for this job.</p>
                        <a href="../auth/login.php" class="btn btn-outline-primary">Sign In</a>
                        <a href="../auth/register.php" class="btn btn-link">Create Account</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.job-description {
    line-height: 1.7;
    white-space: pre-wrap;
}
</style>

<?php include '../includes/footer.php'; ?>