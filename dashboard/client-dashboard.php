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
    <h1>Client Dashboard</h1>
    <div class="card p-4">
        <h3>Welcome, <?= htmlspecialchars($user['username']) ?></h3>
        <p><strong>Role:</strong> <?= $user['role'] ?></p>
        <p><strong>User ID:</strong> <?= $user['id'] ?></p>
    </div>

    <h3 class="mt-4">Work History</h3>
    <table class="table table-bordered">
        <thead>
            <tr><th>Job Title</th><th>Freelancer</th><th>Description</th><th>File</th><th>Date Submitted</th></tr>
        </thead>
        <tbody>
            <?php foreach ($workSubmissions as $work) : ?>
                <tr>
                    <td><?= htmlspecialchars($work['title']) ?></td>
                    <td><?= htmlspecialchars($work['freelancer']) ?></td>
                    <td><?= nl2br(htmlspecialchars($work['work_description'])) ?></td>
                    <td>
                        <?php if ($work['file_path']) : ?>
                            <a href="<?= htmlspecialchars($work['file_path']) ?>" download>Download</a>
                        <?php else : ?>
                            No file uploaded
                        <?php endif; ?>
                    </td>
                    <td><?= $work['submitted_at'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($workSubmissions) === 0) : ?>
                <tr><td colspan="5">No work submitted yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
