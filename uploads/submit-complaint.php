
<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO complaints (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);
    echo "<div class='alert alert-success'>Complaint submitted successfully! We will get back to you soon.</div>";
}
?>

<div class="container mt-5">
    <h1>Submit Complaint</h1>
    <form method="POST" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label class="form-label">Your Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Your Complaint</label>
            <textarea name="message" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Submit Complaint</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
