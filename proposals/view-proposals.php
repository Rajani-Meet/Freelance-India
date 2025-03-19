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
            <div class="alert alert-success shadow-sm border-0">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="alert-heading mb-1">Proposal Accepted!</h4>
                        <p class="mb-2">You have accepted <strong>Proposal #<?= $proposal_id ?></strong>. Payment has been held in escrow.</p>
                        <a href="view-proposals.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-sync-alt me-1"></i> View All Proposals
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger shadow-sm border-0'><i class='fas fa-exclamation-circle me-2'></i>Proposal not found.</div>";
    }
}

// Handle Reject Proposal
if (isset($_GET['reject_proposal'])) {
    $proposal_id = $_GET['reject_proposal'];
    ?>
    <div class="container mt-4">
        <div class="alert alert-warning shadow-sm border-0">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <div>
                    <h4 class="alert-heading mb-1">Proposal Rejected</h4>
                    <p class="mb-2">You have rejected <strong>Proposal #<?= $proposal_id ?></strong>.</p>
                    <a href="view-proposals.php" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-sync-alt me-1"></i> View All Proposals
                    </a>
                </div>
            </div>
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
            <div class="alert alert-success shadow-sm border-0">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-money-bill-wave fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="alert-heading mb-1">Payment Released!</h4>
                        <p class="mb-2">The payment for <strong>Proposal #<?= $proposal_id ?></strong> has been successfully released to the freelancer.</p>
                        <a href="view-proposals.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-sync-alt me-1"></i> View All Proposals
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger shadow-sm border-0'><i class='fas fa-exclamation-circle me-2'></i>Payment not found or already released.</div>";
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

// Count proposals by status
$pending_count = 0;
$accepted_count = 0;
$completed_count = 0;

foreach ($proposals as $proposal) {
    // Check if there's a payment for this proposal
    $paymentStmt = $pdo->prepare("
        SELECT * FROM payments 
        WHERE job_id = ? AND freelancer_id = ?
    ");
    $paymentStmt->execute([$proposal['job_id'], $proposal['freelancer_id']]);
    $payment = $paymentStmt->fetch();
    
    // Check if there's a work submission
    $workStmt = $pdo->prepare("SELECT * FROM work_submissions WHERE proposal_id = ?");
    $workStmt->execute([$proposal['id']]);
    $work = $workStmt->fetch();
    
    if ($payment && $payment['status'] === 'released') {
        $completed_count++;
    } elseif ($payment) {
        $accepted_count++;
    } else {
        $pending_count++;
    }
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Manage Proposals</h1>
        <a href="../dashboard/client-dashboard.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body d-flex align-items-center">
                    <div class="text-primary me-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Pending</h5>
                        <h2 class="mb-0"><?= $pending_count ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body d-flex align-items-center">
                    <div class="text-success me-3">
                        <i class="fas fa-check fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Accepted</h5>
                        <h2 class="mb-0"><?= $accepted_count ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body d-flex align-items-center">
                    <div class="text-info me-3">
                        <i class="fas fa-flag-checkered fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Completed</h5>
                        <h2 class="mb-0"><?= $completed_count ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Proposals</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Job Title</th>
                            <th>Freelancer</th>
                            <th>Proposal</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($proposals) > 0): ?>
                            <?php foreach ($proposals as $proposal) : 
                                // Check payment status
                                $paymentStmt = $pdo->prepare("
                                    SELECT * FROM payments 
                                    WHERE job_id = ? AND freelancer_id = ?
                                ");
                                $paymentStmt->execute([$proposal['job_id'], $proposal['freelancer_id']]);
                                $payment = $paymentStmt->fetch();
                                
                                // Check work submission
                                $stmtWork = $pdo->prepare("SELECT * FROM work_submissions WHERE proposal_id = ?");
                                $stmtWork->execute([$proposal['id']]);
                                $work = $stmtWork->fetch();
                                
                                // Determine status
                                $status = "Pending";
                                $status_class = "bg-warning";
                                
                                if ($payment && $payment['status'] === 'released') {
                                    $status = "Completed";
                                    $status_class = "bg-success";
                                } elseif ($payment) {
                                    $status = "In Progress";
                                    $status_class = "bg-info";
                                }
                            ?>
                                <tr>
                                    <td class="align-middle">
                                        <span class="fw-medium"><?= htmlspecialchars($proposal['job_title']) ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <span class="text-white"><?= substr(htmlspecialchars($proposal['freelancer']), 0, 1) ?></span>
                                            </div>
                                            <?= htmlspecialchars($proposal['freelancer']) ?>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#proposalModal-<?= $proposal['id'] ?>">
                                            <i class="fas fa-eye me-1"></i> View Details
                                        </button>
                                        
                                        <!-- Proposal Modal -->
                                        <div class="modal fade" id="proposalModal-<?= $proposal['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Proposal Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6>Job: <?= htmlspecialchars($proposal['job_title']) ?></h6>
                                                        <h6>Freelancer: <?= htmlspecialchars($proposal['freelancer']) ?></h6>
                                                        <hr>
                                                        <p><?= nl2br(htmlspecialchars($proposal['proposal_text'])) ?></p>
                                                        <div class="d-flex justify-content-between mt-3">
                                                            <span class="text-muted">Proposal #<?= $proposal['id'] ?></span>
                                                            <span class="badge <?= $status_class ?>"><?= $status ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="fw-bold">₹<?= number_format($proposal['amount'], 2) ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge <?= $status_class ?>"><?= $status ?></span>
                                        
                                        <?php if ($work): ?>
                                            <span class="badge bg-info ms-1" data-bs-toggle="modal" data-bs-target="#workModal-<?= $proposal['id'] ?>" style="cursor: pointer;">
                                                <i class="fas fa-file-alt me-1"></i> Work Submitted
                                            </span>
                                            
                                            <!-- Work Modal -->
                                            <div class="modal fade" id="workModal-<?= $proposal['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Work Submission</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h6>Description:</h6>
                                                            <p><?= nl2br(htmlspecialchars($work['work_description'])) ?></p>
                                                            
                                                            <?php if ($work['file_path']): ?>
                                                                <a href="<?= htmlspecialchars($work['file_path']) ?>" class="btn btn-sm btn-primary" download>
                                                                    <i class="fas fa-download me-1"></i> Download Files
                                                                </a>
                                                            <?php endif; ?>
                                                            
                                                            <?php if ($payment && $payment['status'] === 'pending'): ?>
                                                                <div class="mt-3">
                                                                    <a href="?release_payment=<?= $proposal['id'] ?>" class="btn btn-success" onclick="return confirm('Release payment to freelancer?');">
                                                                        <i class="fas fa-money-bill-wave me-1"></i> Release Payment
                                                                    </a>
                                                                </div>
                                                            <?php elseif ($payment && $payment['status'] === 'released'): ?>
                                                                <div class="alert alert-success mt-3">
                                                                    <i class="fas fa-check-circle me-1"></i> Payment released successfully
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php if (!$payment): ?>
                                            <div class="btn-group">
                                                <a href="?accept_proposal=<?= $proposal['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Accept this proposal and fund payment?');">
                                                    <i class="fas fa-check me-1"></i> Accept
                                                </a>
                                                <a href="?reject_proposal=<?= $proposal['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this proposal?');">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </a>
                                            </div>
                                        <?php elseif ($payment['status'] === 'pending' && $work): ?>
                                            <a href="?release_payment=<?= $proposal['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Release payment to freelancer?');">
                                                <i class="fas fa-money-bill-wave me-1"></i> Release
                                            </a>
                                        <?php elseif ($payment['status'] === 'released'): ?>
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-info"><i class="fas fa-hourglass-half me-1"></i> Awaiting Work</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>No proposals received yet</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome in footer if not already included -->
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

<?php include '../includes/footer.php'; ?>