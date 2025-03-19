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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['work_description'];
    $filePath = null;

    if (!empty($_FILES['work_file']['name'])) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $filePath = $uploadDir . basename($_FILES['work_file']['name']);
        move_uploaded_file($_FILES['work_file']['tmp_name'], $filePath);
    }

    $stmt = $pdo->prepare("
        INSERT INTO work_submissions (proposal_id, freelancer_id, work_description, file_path) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$proposal_id, $_SESSION['user_id'], $description, $filePath]);

    echo "<div class='alert alert-success'>Work submitted successfully!</div>";
}
?>

<div class="container mt-5">
    <h1>Submit Work</h1>
    <form method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Work Description</label>
            <textarea name="work_description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload File (optional)</label>
            <input type="file" name="work_file" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Submit Work</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
