
<?php
include '../includes/db.php';

$adminSecretKey = 'SUPERSECRET123'; // Change this to your actual secret key

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Check for secret key if registering as admin
    if ($role === 'admin' && $_POST['admin_key'] !== $adminSecretKey) {
        die('Invalid admin secret key!');
    }

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $role])) {
        header('Location: login.php');
        exit;
    } else {
        echo "Registration Failed.";
    }
}
?>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h2>Register</h2>
    <form method="POST">
        <div class="mb-3">
            <input type="text" name="username" placeholder="Username" class="form-control" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" placeholder="Password" class="form-control" required>
        </div>
        <div class="mb-3">
            <select name="role" id="roleSelect" class="form-select" onchange="toggleAdminKey()">
                <option value="client">Client (Hire Talent)</option>
                <option value="freelancer">Freelancer (Find Jobs)</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<script>
function toggleAdminKey() {
    const role = document.getElementById('roleSelect').value;
    document.getElementById('adminKeyField').style.display = role === 'admin' ? 'block' : 'none';
}
</script>
<?php include '../includes/footer.php'; ?>
