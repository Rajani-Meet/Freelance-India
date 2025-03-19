
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Freelance Platform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php">FreelanceIndia</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'client'): ?>
                        <li class="nav-item"><a class="nav-link" href="../dashboard/client-dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../jobs/post-job.php">Post Job</a></li>
                        <li class="nav-item"><a class="nav-link" href="../proposals/view-proposals.php">Proposals</a></li>
                        <li class="nav-item"><a class="nav-link" href="../messaging/chat.php">Messages</a></li>
                    <?php elseif ($_SESSION['role'] === 'freelancer'): ?>
                        <li class="nav-item"><a class="nav-link" href="../dashboard/freelancer-dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="../jobs/browse-jobs.php">Browse Jobs</a></li>
                        <li class="nav-item"><a class="nav-link" href="../messaging/chat.php">Messages</a></li>
                        <li class="nav-item"><a class="nav-link" href="../payments/withdraw.php">Withdraw</a></li>
                    <?php elseif ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="../admin/dashboard.php">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="../dashboard/profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-danger text-white ms-2" href="../auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white ms-2" href="../auth/register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
