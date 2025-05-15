<?php
include('../includes/header.php');
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title fade-in">Discover & Share Amazing Recipes</h1>
            <p class="hero-subtitle fade-in">Find inspiration for your next meal or share your culinary creations with our community</p>
            <div class="hero-buttons fade-in">
                <a href="<?php echo $rootPath; ?>pages/categories.php" class="btn">Browse Recipes</a>
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
        <h2>Featured Recipes</h2>
        <div class="recipe-list">
            <!-- Sample Recipe Items -->
            <div class="recipe-item hover-grow">
                <img src="<?php echo $rootPath; ?>uploads/recipes/sample-recipe.jpg" alt="Sample Recipe">
                <div class="recipe-content">
                    <h3><a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=1">Creamy Garlic Parmesan Pasta</a></h3>
                    <div class="recipe-meta">
                        <span class="recipe-meta-item"><i class="far fa-clock"></i> 30 mins</span>
                        <span class="recipe-meta-item"><i class="far fa-user"></i> 4 servings</span>
                    </div>
                    <p>A delicious and creamy pasta dish that's perfect for weeknight dinners.</p>
                    <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=1" class="btn">View Recipe</a>
                </div>
            </div>
            
            <div class="recipe-item hover-grow">
                <img src="<?php echo $rootPath; ?>uploads/recipes/sample-recipe.jpg" alt="Sample Recipe">
                <div class="recipe-content">
                    <h3><a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=2">Classic Chocolate Chip Cookies</a></h3>
                    <div class="recipe-meta">
                        <span class="recipe-meta-item"><i class="far fa-clock"></i> 45 mins</span>
                        <span class="recipe-meta-item"><i class="far fa-user"></i> 24 cookies</span>
                    </div>
                    <p>Soft and chewy chocolate chip cookies with the perfect balance of flavors.</p>
                    <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=2" class="btn">View Recipe</a>
                </div>
            </div>
            
            <div class="recipe-item hover-grow">
                <img src="<?php echo $rootPath; ?>uploads/recipes/sample-recipe.jpg" alt="Sample Recipe">
                <div class="recipe-content">
                    <h3><a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=3">Thai Coconut Curry Soup</a></h3>
                    <div class="recipe-meta">
                        <span class="recipe-meta-item"><i class="far fa-clock"></i> 40 mins</span>
                        <span class="recipe-meta-item"><i class="far fa-user"></i> 6 servings</span>
                    </div>
                    <p>A flavorful and aromatic soup that brings the taste of Thailand to your kitchen.</p>
                    <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=3" class="btn">View Recipe</a>
                </div>
            </div>
        </div>
    </section>

    <section class="featured-section">
        <h2>Popular Categories</h2>
        <ul class="category-list">
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=breakfast"><i class="fas fa-coffee"></i> Breakfast</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=lunch"><i class="fas fa-hamburger"></i> Lunch</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=dinner"><i class="fas fa-utensils"></i> Dinner</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=snacks"><i class="fas fa-apple-alt"></i> Snacks</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=dessert"><i class="fas fa-cookie"></i> Dessert</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=appetizer"><i class="fas fa-cheese"></i> Appetizer</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=side-dish"><i class="fas fa-bread-slice"></i> Side Dish</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=beverage"><i class="fas fa-glass-martini-alt"></i> Beverage / Drinks</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=soup"><i class="fas fa-mug-hot"></i> Soup</a></li>
            <li><a href="<?php echo $rootPath; ?>pages/categories.php?category=salad"><i class="fas fa-leaf"></i> Salad</a></li>
        </ul>
    </section>
</div>

<?php
include('../includes/footer.php');
?>