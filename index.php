<?php
session_start();
// Redirect to the home page
header('Location: pages/home.php');
exit();
?>
    <header>
        <div class="header-container">
            <a href="index.php" class="logo">Culinary Delights</a>
            <nav>
                <ul>
                    <li><a href="pages/home.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="pages/categories.php"><i class="fas fa-list"></i> Categories</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="pages/add-recipe.php"><i class="fas fa-plus-circle"></i> Add Recipe</a></li>
                        <li><a href="pages/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                        <li><a href="pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="pages/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="pages/signup.php"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="hero-banner">
        <div class="hero-content">
            <h1 class="hero-title fade-in">Culinary Delights</h1>
            <p class="hero-subtitle fade-in">Discover & Share Amazing Recipes</p>
            <div class="hero-buttons fade-in">
                <a href="pages/home.php" class="btn">Browse Recipes</a>
                <a href="pages/signup.php" class="btn btn-outline">Join Our Community</a>
            </div>
        </div>
    </div>

    <div class="container">
        <section class="features-section">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Discover Recipes</h3>
                    <p>Browse through hundreds of delicious recipes from various cuisines around the world.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <h3>Share Your Creations</h3>
                    <p>Share your own culinary creations with our community of food enthusiasts.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <h3>Save Favorites</h3>
                    <p>Create your own collection of favorite recipes for quick access.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Engage & Connect</h3>
                    <p>Comment, rate, and connect with other food lovers in our community.</p>
                </div>
            </div>
        </section>
        
        <section class="cta-section">
            <div class="cta-content">
                <h2>Ready to Start Cooking?</h2>
                <p>Join our growing community today and begin your culinary journey.</p>
                <a href="pages/signup.php" class="btn">Sign Up Now</a>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h3>Culinary Delights</h3>
                    <p>Discover, share, and enjoy delicious recipes from around the world.</p>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="pages/home.php">Home</a></li>
                        <li><a href="pages/categories.php">Categories</a></li>
                        <li><a href="pages/add-recipe.php">Add Recipe</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="pages/profile.php">My Profile</a></li>
                        <?php else: ?>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Contact</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Culinary Delights. All rights reserved.</p>
                <ul class="social-media">
                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fab fa-pinterest"></i></a></li>
                    <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                </ul>
            </div>
        </div>
    </footer>
    
    <script src="assets/js/scripts.js"></script>
</body>
</html>