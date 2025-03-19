
    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">FreelanceIndia</h5>
                    <p class="small">Connecting talented freelancers with amazing clients since 2020. Build your career or find the perfect talent for your project.</p>
                    <div class="mt-4">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">For Clients</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="./jobs/post-job.php" class="text-white text-decoration-none">Post a Job</a></li>
                        <li class="mb-2"><a href="../freelancers/browse-freelancers.php" class="text-white text-decoration-none">Find Freelancers</a></li>
                        <li class="mb-2"><a href="../payments/payment-methods.php" class="text-white text-decoration-none">Payment Methods</a></li>
                        <li class="mb-2"><a href="../help/clients-faq.php" class="text-white text-decoration-none">Client FAQs</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">For Freelancers</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Find Work</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Get Paid</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Success Tips</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Freelancer FAQs</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase mb-4">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Contact Us</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">File Complaint</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Terms of Service</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center p-3 mt-3" style="background-color: rgba(0, 0, 0, 0.2);">
            <p class="mb-0">&copy; 2025 FreelanceIndia - All Rights Reserved</p>
            <p class="small mb-0">Made with <i class="fas fa-heart text-danger"></i> in India</p>
        </div>
    </footer>

    <!-- Back to top button -->
    <button type="button" class="btn btn-primary btn-floating" id="btn-back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
    // Back to top button
    const backToTopButton = document.getElementById("btn-back-to-top");

    window.onscroll = function() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            backToTopButton.style.display = "block";
        } else {
            backToTopButton.style.display = "none";
        }
    };

    backToTopButton.addEventListener("click", function() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    });
    </script>
</body>
</html>