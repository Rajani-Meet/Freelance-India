<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

$jobs = $pdo->query("SELECT * FROM jobs WHERE status='open'")->fetchAll();
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold">Available Opportunities</h1>
            <p class="lead text-muted">Discover projects that match your skills and expertise</p>
        </div>
        <div class="col-auto d-flex align-items-center">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'client') : ?>
                <a href="post-job.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Post New Job</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" id="jobSearch" class="form-control border-start-0" placeholder="Search jobs...">
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <select class="form-select" id="jobFilter">
                <option value="all">All Categories</option>
                <option value="design">Design</option>
                <option value="development">Development</option>
                <option value="marketing">Marketing</option>
                <option value="writing">Writing</option>
            </select>
        </div>
    </div>

    <div class="row" id="jobsList">
        <?php if (count($jobs) > 0) : ?>
            <?php foreach ($jobs as $job) : ?>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0 fw-bold"><?= htmlspecialchars($job['title']) ?></h5>
                                <span class="badge bg-success rounded-pill">Rs.<?= number_format($job['budget']) ?></span>
                            </div>
                            
                            <p class="card-text text-truncate-3 mb-4"><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'freelancer') : ?>
                                    <a href="job-details.php?id=<?= $job['id'] ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-2"></i>View Details
                                    </a>
                                <?php elseif (!isset($_SESSION['user_id'])) : ?>
                                    <a href="../auth/login.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Login to Apply
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted"><i class="bi bi-info-circle me-2"></i>For freelancers only</span>
                                <?php endif; ?>
                                
                                <small class="text-muted">Posted: <?= date('M d', strtotime($job['created_at'] ?? 'now')) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h3 class="mt-3">No jobs available at the moment</h3>
                    <p class="text-muted">Check back later for new opportunities</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.text-truncate-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 4.5em;
}
.hover-shadow {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>

<script>
document.getElementById('jobSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const jobCards = document.querySelectorAll('#jobsList .card');
    
    jobCards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.closest('.col-lg-6').style.display = 'block';
        } else {
            card.closest('.col-lg-6').style.display = 'none';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>