<?php
include('../includes/header.php');
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title fade-in">Discover & Share Amazing Recipes</h1>
            <p class="hero-subtitle fade-in">Find inspiration for your next meal or share your culinary creations with our community</p>
            <div class="hero-buttons fade-in">
                <a href="<?php echo $rootPath; ?>pages/recipes-categories.php" class="btn">Browse Recipes</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $rootPath; ?>pages/add-recipe.php" class="btn btn-outline">Share Your Recipe</a>
                <?php else: ?>
                    <a href="<?php echo $rootPath; ?>pages/login.php" class="btn btn-outline">Login to Share Recipes</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <section class="featured-section">
        <h2 class="text-center">New Recipes</h2>
        <div class="recipe-list">
            <?php
            include_once('../config/database.php');
            include_once('../classes/Recipe.php');
            
            $database = new Database();
            $db = $database->getConnection();
            $recipe = new Recipe($db);
            $newestRecipes = $recipe->getNewestRecipes(6);
            
            if (!empty($newestRecipes)) {
                foreach ($newestRecipes as $recipeItem) {
                    // Define possible image paths
                    $recipeImage = $recipeItem['image'] ?? '';
                    $imagePath = $rootPath . "uploads/recipes/" . ($recipeImage ?: 'sample-recipe.jpg');
                    $defaultImagePath = $rootPath . "assets/img/default-recipe.jpg";
                    
                    // Use a placeholder URL for demonstration purposes
                    $placeholderUrl = "https://via.placeholder.com/300x200?text=Recipe+Image";
                    
                    // Get the image display path
                    $displayImagePath = file_exists($_SERVER['DOCUMENT_ROOT'] . '/recipe-website/uploads/recipes/' . $recipeImage) ? $imagePath : $placeholderUrl;
                    
                    // Format the date
                    $recipeDate = date("F j, Y", strtotime($recipeItem['created_at']));
                    
                    echo '<div class="recipe-item hover-grow">
                        <a href="' . $rootPath . 'pages/recipe-detail.php?id=' . $recipeItem['id'] . '">
                            <img src="' . $displayImagePath . '" alt="' . htmlspecialchars($recipeItem['title']) . '">
                        </a>
                        <div class="recipe-content">
                            <h3><a href="' . $rootPath . 'pages/recipe-detail.php?id=' . $recipeItem['id'] . '">' . htmlspecialchars($recipeItem['title']) . '</a></h3>
                            <div class="recipe-meta">
                                <span><i class="far fa-calendar-alt"></i> ' . $recipeDate . '</span>
                                <span><i class="far fa-user"></i> ' . htmlspecialchars($recipeItem['author'] ?? 'Unknown') . '</span>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo '<div class="empty-state-container">
                        <div class="empty-state">
                            <i class="fas fa-utensils"></i>
                            <p>No new recipes yet. Check back later or <a href="' . $rootPath . 'pages/recipes-categories.php">browse all recipes</a>.</p>';
                if (isset($_SESSION['user_id'])) {
                    echo '<p><a href="' . $rootPath . 'pages/add-recipe.php" class="btn">Add a Recipe</a></p>';
                }
                echo '</div>
                    </div>';
            }
            ?>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-section">
        <h2 class="text-center">Popular Categories</h2>
        <?php
        // Get the top 5 categories with the most recipes
        include_once('../config/database.php');
        include_once('../classes/Category.php');
        
        $database = new Database();
        $db = $database->getConnection();
        $category = new Category($db);
        $topCategories = $category->getTopCategories(8);
        
        if (!empty($topCategories)) {
            echo '<ul class="category-list">';
            foreach ($topCategories as $cat) {
                echo '<li><a href="' . $rootPath . 'pages/recipes-categories.php?category=' . $cat['id'] . '" class="centered-category"><i class="' . $cat['icon'] . '"></i> ' . htmlspecialchars($cat['name']) . '</a></li>';
            }
            echo '</ul>';
        } else {
            // Fallback to static list if no data is available
            echo '<ul class="category-list">
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=1" class="centered-category"><i class="fas fa-coffee"></i> Breakfast</a></li>
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=2" class="centered-category"><i class="fas fa-hamburger"></i> Lunch</a></li>
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=3" class="centered-category"><i class="fas fa-utensils"></i> Dinner</a></li>
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=4" class="centered-category"><i class="fas fa-apple-alt"></i> Snacks</a></li>
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=5" class="centered-category"><i class="fas fa-cookie"></i> Dessert</a></li>
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=6" class="centered-category"><i class="fas fa-cheese"></i> Appetizer</a></li>
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=7" class="centered-category"><i class="fas fa-bread-slice"></i> Side Dish</a></li>
                <li><a href="' . $rootPath . 'pages/recipes-categories.php?category=8" class="centered-category"><i class="fas fa-glass-martini-alt"></i> Beverage</a></li>
            </ul>';
        }
        ?>
    </section>
</div>

<?php
include('../includes/footer.php');
?>