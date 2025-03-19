
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'freelancer') {
    header('Location: ../auth/login.php');
    exit;
}
$freelancer_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch Accepted Jobs (Jobs with approved proposals & pending payments)
$stmt = $pdo->prepare("
    SELECT jobs.*, proposals.id AS proposal_id
    FROM jobs
    JOIN proposals ON jobs.id = proposals.job_id
    JOIN payments ON jobs.id = payments.job_id
    WHERE proposals.freelancer_id = ?
    AND payments.status = 'pending'
");
$stmt->execute([$freelancer_id]);
$acceptedJobs = $stmt->fetchAll();

// Fetch Work Submission History
$stmt = $pdo->prepare("
    SELECT jobs.title, work_submissions.*
    FROM work_submissions
    JOIN proposals ON work_submissions.proposal_id = proposals.id
    JOIN jobs ON proposals.job_id = jobs.id
    WHERE work_submissions.freelancer_id = ?
");
$stmt->execute([$freelancer_id]);
$workHistory = $stmt->fetchAll();
?>

<div class="container mt-5">
    <h1>Freelancer Dashboard</h1>
    <div class="card p-4">
        <h3>Welcome, <?= htmlspecialchars($user['username']) ?></h3>
        <p><strong>Role:</strong> <?= $user['role'] ?></p>
        <p><strong>User ID:</strong> <?= $user['id'] ?></p>
    </div>
  

    <!-- Accepted Jobs Section -->
    <h3 class="mt-4">Accepted Jobs</h3>
    <table class="table table-bordered">
        <thead>
            <tr><th>Job Title</th><th>Description</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php if (count($acceptedJobs) > 0): ?>
                <?php foreach ($acceptedJobs as $job) : ?>
                    <tr>
                        <td><?= htmlspecialchars($job['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($job['description'])) ?></td>
                        <td>
                            <a href="../proposals/submit-work.php?proposal_id=<?= $job['proposal_id'] ?>" class="btn btn-primary btn-sm">Submit Work</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No accepted jobs yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Work Submission History Section -->
    <h3 class="mt-4">My Work Submissions</h3>
    <table class="table table-bordered">
        <thead>
            <tr><th>Job Title</th><th>Description</th><th>File</th><th>Submitted At</th></tr>
        </thead>
        <tbody>
            <?php if (count($workHistory) > 0): ?>
                <?php foreach ($workHistory as $work) : ?>
                    <tr>
                        <td><?= htmlspecialchars($work['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($work['work_description'])) ?></td>
                        <td>
                            <?php if ($work['file_path']) : ?>
                                <a href="<?= htmlspecialchars($work['file_path']) ?>" download>Download File</a>
                            <?php else : ?>
                                No file uploaded
                            <?php endif; ?>
                        </td>
                        <td><?= $work['submitted_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">No work submissions yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
