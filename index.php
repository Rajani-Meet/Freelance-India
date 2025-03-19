<?php include 'includes/header.php'; ?>

<!-- Hero Section with Background -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 py-4">
                <h1 class="display-4 fw-bold">Welcome to FreelanceIndia</h1>
                <p class="lead fs-4 my-4">Connect with India's top freelance talent or find your next exciting project - all in one trusted marketplace.</p>
                <div class="d-flex gap-3">
                    <a href="auth/register.php" class="btn btn-light btn-lg">Get Started</a>
                    <a href="#how-it-works" class="btn btn-outline-light btn-lg">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="hero-img.png" alt="Freelancing illustration" class="img-fluid" />
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="container text-center py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="p-3">
                <h3 class="fw-bold text-primary">10,000+</h3>
                <p class="text-muted">Skilled Freelancers</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3">
                <h3 class="fw-bold text-primary">5,000+</h3>
                <p class="text-muted">Completed Projects</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3">
                <h3 class="fw-bold text-primary">500+</h3>
                <p class="text-muted">Active Companies</p>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div id="how-it-works" class="container my-5">
    <h2 class="text-center mb-4 fw-bold">How It Works</h2>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="mb-3 text-primary">
                        <i class="fas fa-briefcase fa-2x"></i>
                    </div>
                    <h3>For Clients</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Post jobs and requirements</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Review proposals from qualified freelancers</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Hire the perfect match for your project</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Get quality work delivered on time</li>
                    </ul>
                    <a href="auth/register.php?type=client" class="btn btn-outline-primary mt-3">Post a Job</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="mb-3 text-primary">
                        <i class="fas fa-laptop-code fa-2x"></i>
                    </div>
                    <h3>For Freelancers</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Create a professional profile</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Browse relevant job opportunities</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Submit winning proposals</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Get paid securely for your work</li>
                    </ul>
                    <a href="auth/register.php?type=freelancer" class="btn btn-outline-primary mt-3">Find Work</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popular Categories -->
<div class="container my-5">
    <h2 class="text-center mb-4 fw-bold">Popular Categories</h2>
    <div class="row g-4">
        <div class="col-md-4 col-sm-6">
            <div class="card bg-light border-0 text-center p-3">
                <i class="fas fa-code fa-2x text-primary mb-3"></i>
                <h4>Web Development</h4>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card bg-light border-0 text-center p-3">
                <i class="fas fa-paint-brush fa-2x text-primary mb-3"></i>
                <h4>Graphic Design</h4>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card bg-light border-0 text-center p-3">
                <i class="fas fa-pen fa-2x text-primary mb-3"></i>
                <h4>Content Writing</h4>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card bg-light border-0 text-center p-3">
                <i class="fas fa-bullhorn fa-2x text-primary mb-3"></i>
                <h4>Digital Marketing</h4>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card bg-light border-0 text-center p-3">
                <i class="fas fa-camera fa-2x text-primary mb-3"></i>
                <h4>Photography</h4>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card bg-light border-0 text-center p-3">
                <i class="fas fa-language fa-2x text-primary mb-3"></i>
                <h4>Translation</h4>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4 fw-bold">What People Say</h2>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3 text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="fst-italic">"FreelanceIndia helped me find consistent work in my field. The platform is easy to use and clients are serious about their projects."</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">RK</div>
                            <div class="ms-3">
                                <h5 class="mb-0">Rajesh Kumar</h5>
                                <small class="text-muted">Web Developer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3 text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="fst-italic">"As a business owner, I've found amazing talent here that I couldn't afford to hire full-time. The quality of work has been outstanding."</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">PM</div>
                            <div class="ms-3">
                                <h5 class="mb-0">Priya Mehta</h5>
                                <small class="text-muted">Startup Founder</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="mb-3 text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="fst-italic">"The secure payment system gives me peace of mind. I've built long-term relationships with several clients through this platform."</p>
                        <div class="d-flex align-items-center mt-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">AD</div>
                            <div class="ms-3">
                                <h5 class="mb-0">Ananya Das</h5>
                                <small class="text-muted">Content Writer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="container text-center py-5">
    <h2 class="fw-bold">Ready to get started?</h2>
    <p class="lead mb-4">Join thousands of freelancers and businesses already using FreelanceIndia.</p>
    <div class="d-flex justify-content-center gap-3">
        <a href="auth/register.php?type=client" class="btn btn-primary btn-lg">Hire Talent</a>
        <a href="auth/register.php?type=freelancer" class="btn btn-outline-primary btn-lg">Find Work</a>
    </div>
</div>

<!-- Add Font Awesome if not already included in header -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>

<?php include 'includes/footer.php'; ?>