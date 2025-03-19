<?php
include '../includes/db.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'] ?? null;
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $location = $_POST['location'] ?? null;
    $bio = $_POST['bio'] ?? null;
    $skills = $_POST['skills'] ?? null;
    $hourly_rate = $_POST['hourly_rate'] ?? null;
    
    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_picture']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = '../uploads/profile_pictures/' . $new_filename;
            
            if (!is_dir('../uploads/profile_pictures/')) {
                mkdir('../uploads/profile_pictures/', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                $profile_picture = $new_filename;
            }
        }
    }

    try {
        // Check if username or email already exists
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        
        if ($check->rowCount() > 0) {
            $error = "Username or email already exists!";
        } else {
            // Insert the new user
            $stmt = $pdo->prepare("INSERT INTO users (username, email, first_name, last_name, phone, password, role, bio, profile_picture, skills, hourly_rate, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $first_name, $last_name, $phone, $password, $role, $bio, $profile_picture, $skills, $hourly_rate, $location])) {
                header('Location: login.php');
                exit;
            } else {
                $error = "Registration Failed.";
            }
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="text-center font-weight-bold my-2">Create Your Account</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4 text-center">
                        <p class="lead">Join our community and start your journey today!</p>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" id="registerForm" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" required>
                                    <label for="first_name">First Name</label>
                                    <div class="invalid-feedback">Please enter your first name</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" required>
                                    <label for="last_name">Last Name</label>
                                    <div class="invalid-feedback">Please enter your last name</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                                    <label for="username">Username</label>
                                    <div class="invalid-feedback">Please choose a username</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                    <label for="email">Email address</label>
                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                    <label for="password">Password</label>
                                    <div class="invalid-feedback">Please enter a password</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                                    <label for="confirm_password">Confirm Password</label>
                                    <div class="invalid-feedback">Passwords do not match</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3 mb-md-0">
                                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="Phone Number">
                                    <label for="phone">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="location" id="location" class="form-control" placeholder="Location">
                                    <label for="location">Location</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <select name="role" id="roleSelect" class="form-select" onchange="toggleFields()">
                                <option value="client">Hire Talent (Client)</option>
                                <option value="freelancer">Find Work (Freelancer)</option>
                               </select>
                            <label for="roleSelect">I want to:</label>
                        </div>
                        
                        
                        <div id="freelancerFields" style="display: none;">
                            <div class="form-floating mb-3">
                                <input type="text" name="skills" id="skills" class="form-control" placeholder="Skills">
                                <label for="skills">Skills (comma separated)</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="number" name="hourly_rate" id="hourly_rate" class="form-control" step="0.01" placeholder="Hourly Rate">
                                <label for="hourly_rate">Hourly Rate (₹)</label>
                            </div>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <textarea name="bio" id="bio" class="form-control" style="height: 100px" placeholder="Bio"></textarea>
                            <label for="bio">Bio</label>
                        </div>
                        
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                            <div class="mt-2" id="imagePreview"></div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">I agree to the <a href="../terms.php">Terms of Service</a> and <a href="../privacy.php">Privacy Policy</a></label>
                            <div class="invalid-feedback">You must agree before submitting</div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php" class="fw-bold">Sign in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    const role = document.getElementById('roleSelect').value;
    const freelancerFields = document.getElementById('freelancerFields');

    
    // Show/hide freelancer specific fields
    freelancerFields.style.display = role === 'freelancer' ? 'block' : 'none';
    
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    
    // Image preview
    const profilePicture = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    
    profilePicture.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-height: 200px;">`;
            }
            reader.readAsDataURL(file);
        } else {
            imagePreview.innerHTML = '';
        }
    });
    
    // Form validation
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            event.preventDefault();
            document.getElementById('confirm_password').setCustomValidity('Passwords do not match');
        } else {
            document.getElementById('confirm_password').setCustomValidity('');
        }
        
        form.classList.add('was-validated');
    });
    
    // Initialize field visibility based on default role
    toggleFields();
});
</script>
<?php include '../includes/footer.php'; ?>