<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header('Location: ../auth/login.php');
    exit;
}

$freelancer_id = $_SESSION['user_id'];

// Calculate total earnings
$totalEarningsStmt = $pdo->prepare("
    SELECT SUM(amount) as total_earnings 
    FROM payments 
    WHERE freelancer_id = ? 
    AND status = 'released'
");
$totalEarningsStmt->execute([$freelancer_id]);
$totalEarnings = $totalEarningsStmt->fetch()['total_earnings'] ?? 0;

// Fetch released payments with job details
$stmt = $pdo->prepare("
    SELECT p.*, j.title as job_title, j.description as job_description, u.username as client_username
    FROM payments p
    JOIN jobs j ON p.job_id = j.id
    JOIN users u ON j.client_id = u.id
    WHERE p.freelancer_id = ? 
    AND p.status = 'released'
    ORDER BY p.created_at DESC
");
$stmt->execute([$freelancer_id]);
$payments = $stmt->fetchAll();

// Handle withdrawal request
$withdrawMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['withdraw_amount'])) {
    $withdrawAmount = floatval($_POST['withdraw_amount']);
    $accountDetails = $_POST['account_details'];
    
    if ($withdrawAmount > 0 && $withdrawAmount <= $totalEarnings) {
        // Process withdrawal (in a real app, this would interact with a payment gateway)
        $withdrawMessage = '<div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i> Withdrawal request for Rs.' . number_format($withdrawAmount, 2) . ' has been submitted successfully. 
            It will be processed within 2-3 business days.
        </div>';
    } else {
        $withdrawMessage = '<div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i> Invalid withdrawal amount. Please ensure you have sufficient funds.
        </div>';
    }
}
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard/freelancer-dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Withdraw Funds</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <?= $withdrawMessage ?>
    
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm rounded-lg mb-4">
                <div class="card-body text-center">
                    <div class="py-3">
                        <i class="fas fa-wallet fa-3x text-primary mb-3"></i>
                        <h4>Total Earnings</h4>
                        <h2 class="text-primary mb-0">Rs.<?= number_format($totalEarnings, 2) ?></h2>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Request Withdrawal</h5>
                </div>
                <div class="card-body">
                    <?php if ($totalEarnings > 0): ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="withdraw_amount" class="form-label">Amount to Withdraw (Rs.)</label>
                                <input type="number" class="form-control" id="withdraw_amount" name="withdraw_amount" 
                                    min="100" max="<?= $totalEarnings ?>" step="0.01" required
                                    placeholder="Enter amount">
                                <div class="form-text">Minimum: Rs.100 | Maximum: Rs.<?= number_format($totalEarnings, 2) ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="account_details" class="form-label">Account Details</label>
                                <select class="form-select mb-2" id="payment_method">
                                    <option value="bank">Bank Transfer</option>
                                    <option value="upi">UPI</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                                <textarea class="form-control" id="account_details" name="account_details" rows="3" 
                                    placeholder="Enter your account details" required></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-money-bill-wave me-2"></i>Request Withdrawal
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-coins fa-3x text-muted mb-3"></i>
                            <h5>No funds available</h5>
                            <p class="text-muted">Complete jobs to earn money that you can withdraw.</p>
                            <a href="../jobs/browse-jobs.php" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Browse Available Jobs
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-lg">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Available Funds</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($payments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Project</th>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Release Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-2">
                                                        <i class="fas fa-briefcase text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($payment['job_title']) ?></h6>
                                                        <small class="text-muted">Job #<?= $payment['job_id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($payment['client_username']) ?></td>
                                            <td class="fw-bold text-success">Rs.<?= number_format($payment['amount'], 2) ?></td>
                                            <td><?= date('M d, Y', strtotime($payment['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> Available
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice-dollar fa-4x text-muted mb-3"></i>
                            <h5>No funds available for withdrawal</h5>
                            <p class="text-muted mb-4">When clients release payments for your completed work, they'll appear here.</p>
                            <a href="../jobs/browse-jobs.php" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Find Work
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (count($payments) > 0): ?>
            <div class="card shadow-sm rounded-lg mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Withdrawal Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-clock me-2"></i>Processing Time</h6>
                        <p class="mb-0">Withdrawals are typically processed within 2-3 business days. Bank transfers may take an additional 1-2 business days to reflect in your account.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <h6><i class="fas fa-rupee-sign me-2"></i>Minimum Withdrawal</h6>
                                    <p class="mb-0">Rs.100</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-percent me-2"></i>Withdrawal Fee</h6>
                                    <p class="mb-0">0% (Free withdrawals)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>