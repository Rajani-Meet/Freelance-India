<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SESSION['role'] !== 'freelancer') {
    header('Location: ../auth/login.php');
    exit;
}

$proposal_id = $_GET['proposal_id'] ?? null;
if (!$proposal_id) {
    die("Invalid proposal.");
}

// Fetch proposal details
$proposalStmt = $pdo->prepare("
    SELECT proposals.*, jobs.title as job_title 
    FROM proposals 
    JOIN jobs ON proposals.job_id = jobs.id 
    WHERE proposals.id = ? AND proposals.freelancer_id = ?
");
$proposalStmt->execute([$proposal_id, $_SESSION['user_id']]);
$proposal = $proposalStmt->fetch();

if (!$proposal) {
    echo "<div class='alert alert-danger'>Proposal not found or you don't have permission to access it.</div>";
    include '../includes/footer.php';
    exit;
}

// Check if work has already been submitted
$workStmt = $pdo->prepare("SELECT * FROM work_submissions WHERE proposal_id = ?");
$workStmt->execute([$proposal_id]);
$existingWork = $workStmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['work_description'];
    $filePath = null;

    if (!empty($_FILES['work_file']['name'])) {
        $uploadDir = '../uploads/work_files/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $originalName = $_FILES['work_file']['name'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['work_file']['tmp_name'], $filePath)) {
            $filePath = $filePath;
        } else {
            echo "<div class='alert alert-danger'>Error uploading file.</div>";
            $filePath = null;
        }
    }

    if ($existingWork) {
        // Update existing submission
        $stmt = $pdo->prepare("
            UPDATE work_submissions 
            SET work_description = ?, file_path = COALESCE(?, file_path), updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$description, $filePath, $existingWork['id']]);
        $success = "Work submission updated successfully!";
    } else {
        // Create new submission
        $stmt = $pdo->prepare("
            INSERT INTO work_submissions (proposal_id, freelancer_id, work_description, file_path) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$proposal_id, $_SESSION['user_id'], $description, $filePath]);
        $success = "Work submitted successfully!";
    }
    
    // Refresh existing work data
    $workStmt->execute([$proposal_id]);
    $existingWork = $workStmt->fetch();
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard/freelancer-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Submit Work</li>
                </ol>
            </nav>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h3 class="mb-0">Job Details</h3>
                </div>
                <div class="card-body">
                    <h4><?= htmlspecialchars($proposal['job_title']) ?></h4>
                    <div class="mb-3">
                        <span class="badge bg-success me-2">Your Bid: ₹<?= $proposal['amount'] ?></span>
                    </div>
                    <p class="card-text"><?= nl2br(htmlspecialchars($proposal['proposal_text'])) ?></p>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h3 class="mb-0"><?= $existingWork ? 'Update Work Submission' : 'Submit Your Work' ?></h3>
                </div>
                <div class="card-body">
                    <?php if ($existingWork): ?>
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            You've already submitted work for this proposal. You can update your submission below.
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" id="workForm" novalidate>
                        <div class="mb-4">
                            <label for="work_description" class="form-label fw-bold">Work Description</label>
                            <textarea name="work_description" id="work_description" class="form-control" rows="6" required><?= $existingWork ? htmlspecialchars($existingWork['work_description']) : '' ?></textarea>
                            <div class="form-text">Provide details about the work you've completed.</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="work_file" class="form-label fw-bold">Upload File</label>
                            <input type="file" name="work_file" id="work_file" class="form-control">
                            <div class="form-text">Upload any relevant files (source code, documents, images, etc.)</div>
                            
                            <?php if ($existingWork && $existingWork['file_path']): ?>
                                <div class="mt-2">
                                    <span class="badge bg-secondary mb-2">Current File</span>
                                    <div class="d-flex align-items-center border rounded p-2">
                                        <i class="bi bi-file-earmark me-2"></i>
                                        <span class="me-auto"><?= basename($existingWork['file_path']) ?></span>
                                        <a href="<?= htmlspecialchars($existingWork['file_path']) ?>" class="btn btn-sm btn-outline-primary" download>
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <?= $existingWork ? 'Update Submission' : 'Submit Work' ?>
                            </button>
                            <a href="../dashboard/freelancer-dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('workForm');
    
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