<?php
// Footer section of the recipe website
// Define base path for links if not already defined
if (!isset($rootPath)) {
    $rootPath = "";
    // Count how many instances of "/pages/" are in the path
    $pagesCount = substr_count($_SERVER['PHP_SELF'], '/pages/');
    if ($pagesCount > 0) {
        $rootPath = str_repeat("../", $pagesCount);
    }
}

// Make sure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <h3>Timplado de Platito</h3>
                <p>Discover, share, and enjoy delicious recipes from around the world.</p>
            </div>
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo $rootPath; ?>pages/home.php">Home</a></li>
                    <li><a href="<?php echo $rootPath; ?>pages/recipes-categories.php">Recipes & Categories</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $rootPath; ?>pages/add-recipe.php">Add Recipe</a></li>
                        <li><a href="<?php echo $rootPath; ?>pages/profile.php">My Profile</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $rootPath; ?>pages/login.php">Login</a></li>
                        <li><a href="<?php echo $rootPath; ?>pages/signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Timplado de Platito. All rights reserved.</p>
            <ul class="social-media">
                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                <li><a href="#"><i class="fab fa-pinterest"></i></a></li>
                <li><a href="#"><i class="fab fa-youtube"></i></a></li>
            </ul>
        </div>
    </div>
    
    <!-- Back to top button -->
    <div class="back-to-top">
        <button id="backToTopBtn" title="Back to Top"><i class="fas fa-arrow-up"></i></button>
    </div>
</footer>

<script src="<?php echo $rootPath; ?>assets/js/scripts.js"></script>
<script src="<?php echo $rootPath; ?>assets/js/pagination.js"></script>
<script src="<?php echo $rootPath; ?>assets/js/action-buttons.js"></script>
</body>
</html>