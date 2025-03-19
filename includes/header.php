<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreelanceIndia - Connect with Top Talent</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <?php 
    // Define user role for easier access
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    $loggedIn = isset($_SESSION['user_id']);

    // Set default notification counts
    // Later you can replace these with actual function calls
    $messageCount = 0;
    $notificationCount = 0;

    // For demonstration, we'll use static counts
    // In a real implementation, you would fetch these from your database
    if ($loggedIn) {
        $messageCount = 2; // Placeholder value
        $notificationCount = 1; // Placeholder value
    }
    ?>


    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <i class="fas fa-code-branch me-2"></i>
                <span class="fw-bold">FreelanceIndia</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (!$loggedIn): ?>                   
                    <li class="nav-item">
                        <a class="nav-link" href="./index.php/#how-it-works">About</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($userRole === 'freelancer' || !$loggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../jobs/browse-jobs.php">Find Jobs</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($userRole === 'client' || !$loggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../jobs/post-job.php">Hire Talent</a>
                    </li>
                    <?php endif; ?>
                    
                </ul>
                
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if ($loggedIn): ?>
                        <!-- Search button -->
                        <li class="nav-item me-2">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="fas fa-search"></i>
                            </a>
                        </li>
                        
                        
                        
                        <!-- Messages -->
                        <li class="nav-item me-2">
                            <a class="nav-link position-relative" href="../messaging/chat.php">
                                <i class="fas fa-comment"></i>
                            </a>
                        </li>
                        
                        <!-- User dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="profile-pic-small me-2">
                                    <?php if (isset($_SESSION['profile_picture']) && !empty($_SESSION['profile_picture'])): ?>
                                        <img src="<?php echo $_SESSION['profile_picture']; ?>" alt="Profile" class="rounded-circle" width="28" height="28">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="d-none d-md-inline">
                                    <?php echo isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'My Account'; ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($userRole === 'client'): ?>
                                    <!-- Client-specific menu items -->
                                    <li><h6 class="dropdown-header">Client Menu</h6></li>
                                    <li><a class="dropdown-item" href="../dashboard/client-dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="../jobs/post-job.php"><i class="fas fa-plus-circle me-2"></i>Post Job</a></li>
                                    <li><a class="dropdown-item" href="../proposals/view-proposals.php"><i class="fas fa-file-contract me-2"></i>Proposals</a></li>
                                <?php elseif ($userRole === 'freelancer'): ?>
                                    <!-- Freelancer-specific menu items -->
                                    <li><h6 class="dropdown-header">Freelancer Menu</h6></li>
                                    <li><a class="dropdown-item" href="../dashboard/freelancer-dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                    <li><a class="dropdown-item" href="../jobs/browse-jobs.php"><i class="fas fa-search me-2"></i>Find Jobs</a></li>
                                    <li><a class="dropdown-item" href="../payments/withdraw.php"><i class="fas fa-money-bill-wave me-2"></i>Withdraw</a></li>
                                <?php endif; ?>
                                
                                <!-- Common menu items for all logged-in users -->
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../dashboard/profile.php"><i class="fas fa-user-cog me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="../dashboard/settings.php"><i class="fas fa-cog me-2"></i>Account Settings</a></li>
                                <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Not logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/login.php">Login</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary" href="../auth/register.php">Sign Up</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <?php if ($loggedIn): ?>
    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../search/results.php" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="query" placeholder="Search jobs, freelancers, skills..." autofocus>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="searchJobs" value="jobs" <?php echo ($userRole === 'freelancer') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="searchJobs">Jobs</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="searchFreelancers" value="freelancers" <?php echo ($userRole === 'client') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="searchFreelancers">Freelancers</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="searchAll" value="all">
                                <label class="form-check-label" for="searchAll">All</label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>