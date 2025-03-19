<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'client') {
    header('Location: ../auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];

    $stmt = $pdo->prepare("INSERT INTO jobs (client_id, title, description, budget) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$_SESSION['user_id'], $title, $description, $budget])) {
        ?>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="bi bi-check-circle-fill text-success display-1"></i>
                            </div>
                            <h2 class="mb-3">Job Posted Successfully!</h2>
                            <p class="lead mb-4">Your job "<strong><?= htmlspecialchars($title) ?></strong>" has been published and is now visible to freelancers.</p>
                            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                                <a href="../dashboard/client-dashboard.php" class="btn btn-primary">
                                    <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                                </a>
                                <a href="post-job.php" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Post Another Job
                                </a>
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
        $error = true;
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error!</strong> Failed to post the job. Please try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="mb-0"><i class="bi bi-briefcase me-2"></i>Post a New Job</h3>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Job Title</label>
                            <input type="text" id="title" name="title" class="form-control form-control-lg" 
                            placeholder="E.g. WordPress Website Development" required>
                            <div class="form-text">Be specific and concise (70 characters max)</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Job Description</label>
                            <textarea id="description" name="description" class="form-control" rows="8" 
                            placeholder="Describe your project requirements, deliverables, and timeline..." required></textarea>
                            <div class="form-text">
                                Include all relevant details:
                                <ul class="mt-2">
                                    <li>Project scope and objectives</li>
                                    <li>Required skills and experience</li>
                                    <li>Deliverables and milestones</li>
                                    <li>Timeline expectations</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="budget" class="form-label fw-bold">Budget (Rs.)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" id="budget" name="budget" class="form-control" 
                                    placeholder="Enter your budget" min="100" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-bold">Category</label>
                                <select id="category" class="form-select">
                                    <option value="design">Design</option>
                                    <option value="development">Development</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="writing">Writing</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-outline-secondary me-2">Save Draft</button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Publish Job
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card bg-light border-0 mt-4">
                <div class="card-body p-4">
                    <h5><i class="bi bi-lightbulb me-2"></i>Tips for Getting Great Proposals</h5>
                    <ul class="mb-0">
                        <li>Be clear about your requirements and expectations</li>
                        <li>Provide examples of work you like</li>
                        <li>Set a realistic budget for the scope of work</li>
                        <li>Respond promptly to freelancer questions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

<?php include '../includes/footer.php'; ?>