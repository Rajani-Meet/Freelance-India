<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'freelancer') {
    header('Location: ../auth/login.php');
    exit;
}
$freelancer_id = $_SESSION['user_id'];

// First check if the user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$freelancer_id]);
$user = $stmt->fetch();

// Get count of active jobs - add error handling
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as active_jobs_count
        FROM jobs
        JOIN proposals ON jobs.id = proposals.job_id
        JOIN payments ON jobs.id = payments.job_id AND payments.freelancer_id = proposals.freelancer_id
        WHERE proposals.freelancer_id = ?
        AND payments.status = 'pending'
    ");
    $stmt->execute([$freelancer_id]);
    $result = $stmt->fetch();
    $activeJobsCount = $result['active_jobs_count'];
} catch (PDOException $e) {
    // Log error or handle gracefully
    error_log("Database error: " . $e->getMessage());
    $activeJobsCount = 0;
}

// Get count of work submissions - add error handling
try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as submissions_count
        FROM work_submissions
        WHERE freelancer_id = ?
    ");
    $stmt->execute([$freelancer_id]);
    $result = $stmt->fetch();
    $submissionsCount = isset($result['submissions_count']) ? $result['submissions_count'] : 0;
} catch (PDOException $e) {
    // Log error or handle gracefully
    error_log("Database error: " . $e->getMessage());
    $submissionsCount = 0;
}

// Fetch Accepted Jobs with error handling
try {
    $stmt = $pdo->prepare("
        SELECT jobs.*, proposals.id AS proposal_id
        FROM jobs
        JOIN proposals ON jobs.id = proposals.job_id
        JOIN payments ON jobs.id = payments.job_id AND payments.freelancer_id = proposals.freelancer_id
        WHERE proposals.freelancer_id = ?
        AND payments.status = 'pending'
    ");
    $stmt->execute([$freelancer_id]);
    $acceptedJobs = $stmt->fetchAll();
} catch (PDOException $e) {
    // Log error or handle gracefully
    error_log("Database error: " . $e->getMessage());
    $acceptedJobs = [];
}

// Fetch Work Submission History with error handling
try {
    $stmt = $pdo->prepare("
        SELECT jobs.title, work_submissions.*
        FROM work_submissions
        JOIN proposals ON work_submissions.proposal_id = proposals.id
        JOIN jobs ON proposals.job_id = jobs.id
        WHERE work_submissions.freelancer_id = ?
    ");
    $stmt->execute([$freelancer_id]);
    $workHistory = $stmt->fetchAll();
} catch (PDOException $e) {
    // Log error or handle gracefully
    error_log("Database error: " . $e->getMessage());
    $workHistory = [];
}
?>

<div class="container mt-5">
    <!-- Dashboard header with quick stats -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">Freelancer Dashboard</h1>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="../jobs/browse-jobs.php" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Find Jobs
            </a>
        </div>
    </div>

    <!-- Profile card row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h3><?= htmlspecialchars($user['username'] ?? 'User') ?></h3>
                        <span class="badge bg-success mb-2"><?= $user['role'] ?? 'freelancer' ?></span>
                        <p class="text-muted small mb-0">ID: <?= $user['id'] ?? $freelancer_id ?></p>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="../dashboard/profile.php" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <!-- Quick stats cards -->
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3 bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-briefcase text-success"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0">Active Jobs</h6>
                                    <h3 class="mb-0"><?= $activeJobsCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3 bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-file-alt text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0">Submissions</h6>
                                    <h3 class="mb-0"><?= $submissionsCount ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>

    <!-- Accepted Jobs Section -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-header bg-white py-3">
            <h3 class="mb-0">Accepted Jobs</h3>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($acceptedJobs)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($acceptedJobs as $job) : ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($job['title']) ?></td>
                                    <td class="text-truncate" style="max-width: 300px;"><?= nl2br(htmlspecialchars($job['description'])) ?></td>
                                    <td>
                                        <a href="../proposals/submit-work.php?proposal_id=<?= $job['proposal_id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-upload me-1"></i> Submit Work
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <h5>No accepted jobs yet</h5>
                    <p class="text-muted mb-3">Start bidding on projects to get your first job!</p>
                    <a href="../jobs/browse-jobs.php" class="btn btn-outline-primary">Browse Available Jobs</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Work Submission History Section -->
    <div class="card shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-header bg-white py-3">
            <h3 class="mb-0">My Work Submissions</h3>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($workHistory)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Description</th>
                                <th>File</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($workHistory as $work) : ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($work['title'] ?? 'Untitled') ?></td>
                                    <td class="text-truncate" style="max-width: 200px;"><?= nl2br(htmlspecialchars($work['work_description'] ?? '')) ?></td>
                                    <td>
                                        <?php if (!empty($work['file_path'])) : ?>
                                            <a href="<?= htmlspecialchars($work['file_path']) ?>" download class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i> Download
                                            </a>
                                        <?php else : ?>
                                            <span class="badge bg-light text-dark">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= isset($work['submitted_at']) ? date('M d, Y', strtotime($work['submitted_at'])) : 'N/A' ?></td>
                                    <td>
                                        <span class="badge bg-warning">Pending</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5>No submissions yet</h5>
                    <p class="text-muted">Your completed work submissions will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Font Awesome if not already included in header -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>

<?php include '../includes/footer.php'; ?>