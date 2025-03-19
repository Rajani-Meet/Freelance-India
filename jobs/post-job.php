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
        <div class="container mt-4">
            <div class="alert alert-success">
                <h4 class="alert-heading">Job Posted Successfully!</h4>
                <p>Your job "<strong><?= htmlspecialchars($title) ?></strong>" has been posted. Freelancers can now submit proposals.</p>
                <hr>
                <a href="../dashboard/client-dashboard.php" class="btn btn-outline-primary">Go to Dashboard</a>
                <a href="post-job.php" class="btn btn-outline-success">Post Another Job</a>
            </div>
        </div>
        <?php
        include '../includes/footer.php';
        exit;
    } else {
        echo "<div class='alert alert-danger'>Failed to post the job. Please try again.</div>";
    }
}
?>

<div class="container mt-5">
    <h1>Post a New Job</h1>
    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Job Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Budget (Rs.)</label>
            <input type="number" name="budget" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Post Job</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
