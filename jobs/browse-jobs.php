<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$jobs = $pdo->query("SELECT * FROM jobs WHERE status='open'")->fetchAll();
?>

<div class="container mt-5">
    <h1>Available Jobs</h1>
    <?php foreach ($jobs as $job) : ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5><?= htmlspecialchars($job['title']) ?></h5>
                <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                <p><strong>Budget:</strong> Rs.<?= $job['budget'] ?></p>
                
                
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'freelancer') : ?>
                    <a href="job-details.php?id=<?= $job['id'] ?>" class="btn btn-info btn-sm">View Details</a>

                <?php else: ?>
                    <p class="text-muted mt-2">Login as a freelancer to submit proposals.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>
