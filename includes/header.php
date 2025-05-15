<?php
// Start the session if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define base path for links
$rootPath = "";
// Count how many instances of "/pages/" are in the path
$pagesCount = substr_count($_SERVER['PHP_SELF'], '/pages/');
if ($pagesCount > 0) {
    $rootPath = str_repeat("../", $pagesCount);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Website</title>
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/fonts/fonts.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/alerts.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/recipes.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/profile.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/featured-recipes.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="<?php echo $rootPath; ?>pages/home.php" class="logo">Culinary Delights</a>
            <nav>
                <ul>
                    <li><a href="<?php echo $rootPath; ?>pages/home.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="<?php echo $rootPath; ?>pages/recipes-categories.php"><i class="fas fa-book-open"></i> Recipes & Categories</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="<?php echo $rootPath; ?>pages/add-recipe.php"><i class="fas fa-plus-circle"></i> Add Recipe</a></li>
                        <li><a href="<?php echo $rootPath; ?>pages/profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                        <li><a href="<?php echo $rootPath; ?>pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $rootPath; ?>pages/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="<?php echo $rootPath; ?>pages/signup.php"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>