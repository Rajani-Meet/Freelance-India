<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'client') {
    header('Location: ../auth/login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT jobs.title, users.username AS freelancer, work_submissions.*
    FROM work_submissions
    JOIN proposals ON work_submissions.proposal_id = proposals.id
    JOIN jobs ON proposals.job_id = jobs.id
    JOIN users ON proposals.freelancer_id = users.id
    WHERE jobs.client_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$workSubmissions = $stmt->fetchAll();
?>

<div class="container mt-5">
    <!-- Quick action buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Client Dashboard</h1>
        <div>
            <a href="../jobs/post-job.php" class="btn btn-success me-2">
                <i class="fas fa-plus-circle"></i> Post New Job
            </a>
            <a href="../proposals/view-proposals.php" class="btn btn-primary">
                <i class="fas fa-list"></i> View Proposals
            </a>
        </div>
    </div>
    
    <!-- Profile card with shadow and better styling -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="bg-primary text-white p-3 rounded-circle">
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                </div>
                <div class="col">
                    <h3 class="mb-1">Welcome, <?= htmlspecialchars($user['username']) ?></h3>
                    <p class="text-muted mb-0">
                        <span class="badge bg-success me-2"><?= $user['role'] ?></span>
                        ID: <?= $user['id'] ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Work history section with better table styling -->
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(0,0,0,.125);">
            <h3 class="mb-0">Work Submissions</h3>
            <span class="badge bg-primary rounded-pill"><?= count($workSubmissions) ?> Submissions</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Job Title</th>
                            <th>Freelancer</th>
                            <th>Description</th>
                            <th>File</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workSubmissions as $work) : ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($work['title']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2"><i class="fas fa-user-circle text-secondary"></i></span>
                                        <?= htmlspecialchars($work['freelancer']) ?>
                                    </div>
                                </td>
                                <td class="text-truncate" style="max-width: 200px;"><?= nl2br(htmlspecialchars($work['work_description'])) ?></td>
                                <td>
                                    <?php if ($work['file_path']) : ?>
                                        <a href="<?= htmlspecialchars($work['file_path']) ?>" download class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> Download
                                        </a>
                                    <?php else : ?>
                                        <span class="badge bg-light text-dark">No file</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($work['submitted_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="approveWork(<?= $work['id'] ?>)">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectWork(<?= $work['id'] ?>)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($workSubmissions) === 0) : ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No work submissions yet.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome if not already included in header -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>

<!-- Add some basic JS for the approve/reject buttons -->
<script>
function approveWork(id) {
    if(confirm('Are you sure you want to approve this work?')) {
        // In a real implementation, this would make an AJAX request
        alert('Work approval functionality would be implemented here');
    }
}

function rejectWork(id) {
    if(confirm('Are you sure you want to reject this work?')) {
        // In a real implementation, this would make an AJAX request
        alert('Work rejection functionality would be implemented here');
    }
}
</script>

<?php include '../includes/footer.php'; ?>