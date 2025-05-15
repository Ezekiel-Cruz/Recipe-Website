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
        <div class="recipe-grid">
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
                    $imagePath = $rootPath . "uploads/recipes/" . $recipeImage;
                    
                    echo '<div class="recipe-card">
                        <div class="recipe-header">';
                    
                    if (!empty($recipeImage)) {
                        echo '<img src="' . $imagePath . '" alt="' . htmlspecialchars($recipeItem['title']) . '">';
                    } else {
                        echo '<div class="no-image">No Image</div>';
                    }
                    
                    if (!empty($recipeItem['category_name'])) {
                        echo '<span class="category-badge">' . htmlspecialchars($recipeItem['category_name']) . '</span>';
                    }
                    
                    echo '</div>
                        <div class="recipe-body">
                            <h3>' . htmlspecialchars($recipeItem['title']) . '</h3>
                            <p class="recipe-meta">
                                <span><i class="fas fa-user"></i> ' . htmlspecialchars($recipeItem['author'] ?? 'Unknown') . '</span>';
                    
                    if (!empty($recipeItem['difficulty'])) {
                        echo '<span><i class="fas fa-signal"></i> ' . htmlspecialchars($recipeItem['difficulty']) . '</span>';
                    }
                    
                    if (!empty($recipeItem['prep_time'])) {
                        echo '<span><i class="far fa-clock"></i> Prep: ' . htmlspecialchars($recipeItem['prep_time']) . ' min</span>';
                    }
                    
                    if (!empty($recipeItem['cook_time'])) {
                        echo '<span><i class="fas fa-fire"></i> Cook: ' . htmlspecialchars($recipeItem['cook_time']) . ' min</span>';
                    }
                    
                    echo '</p>
                            <a href="' . $rootPath . 'pages/recipe-detail.php?id=' . $recipeItem['id'] . '" class="btn-outline">View Recipe</a>
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
