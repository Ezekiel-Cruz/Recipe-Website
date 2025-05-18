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
    <title>Timplado de Platito</title>
    <link rel="icon" href="<?php echo $rootPath; ?>assets/img/favlogo.png" type="image/png">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/fonts/fonts.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/alerts.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/recipes.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/profile.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/profile-improvements.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/button-fixes.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/profile-original.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/view-recipe-button.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/profile-recipe-cards.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/search-form.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/category-cards.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/featured-recipes.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/home-improvements.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/recipe-meta-items.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/pagination.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/recipe-animations.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/variables.css">
    <link rel="stylesheet" href="<?php echo $rootPath; ?>assets/css/action-buttons.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="<?php echo $rootPath; ?>pages/home.php" class="logo">
                <img src="<?php echo $rootPath; ?>assets/img/favlogo.png" alt="Site Logo" class="header-logo-img">
                Timplado de Platito</a>
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
