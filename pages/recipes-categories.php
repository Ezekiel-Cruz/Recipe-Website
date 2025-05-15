<?php
session_start();
include '../includes/header.php';
include '../config/database.php';
include '../classes/Category.php';
include '../classes/Recipe.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Get categories
$categoryObj = new Category($db);
$categories = $categoryObj->getAllCategories();

// Get selected category filter (if any)
$selectedCategoryId = isset($_GET['category']) ? intval($_GET['category']) : null;

// Create recipe object
$recipeObj = new Recipe($db);

// Get recipes (filtered by category if selected)
$query = "SELECT r.*, u.username as author, c.name as category_name 
          FROM recipes r 
          LEFT JOIN users u ON r.user_id = u.id 
          LEFT JOIN categories c ON r.category_id = c.id";

if ($selectedCategoryId) {
    $query .= " WHERE r.category_id = :category_id";
}

$query .= " ORDER BY r.created_at DESC";
$stmt = $db->prepare($query);

if ($selectedCategoryId) {
    $stmt->bindParam(':category_id', $selectedCategoryId);
}

$stmt->execute();
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to get appropriate icon for each category
function getCategoryIcon($categoryName) {
    $icons = [
        'Breakfast' => 'fa-coffee',
        'Lunch' => 'fa-hamburger',
        'Dinner' => 'fa-utensils',
        'Snacks' => 'fa-apple-alt',
        'Dessert' => 'fa-cookie',
        'Appetizer' => 'fa-cheese',
        'Side Dish' => 'fa-bread-slice',
        'Beverage / Drinks' => 'fa-glass-martini-alt',
        'Soup' => 'fa-mug-hot',
        'Salad' => 'fa-leaf'
    ];
    
    return isset($icons[$categoryName]) ? $icons[$categoryName] : 'fa-utensils';
}

// Helper function to get recipe count for each category
function getRecipeCount($db, $categoryId) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM recipes WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $categoryId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] ?? 0;
}
?>

<div class="container">
    <!-- Success message if applicable -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success mt-3">
            Your recipe has been submitted successfully!
        </div>
    <?php endif; ?>    <!-- Category Sections Container -->
    <div class="categories-container">
        <!-- Recipe Categories Section -->
        <div class="category-section full-width">
            <div class="section-header">
                <h2 class="section-title">Recipe Categories</h2>
                <p class="subtitle">Browse recipes by category</p>
            </div>
            <?php if ($categories): ?>
                <div class="categories-grid">
                    <!-- Add "All Categories" option -->
                    <div class="category-card <?php echo $selectedCategoryId ? '' : 'active'; ?>">
                        <a href="<?php echo $rootPath; ?>pages/recipes-categories.php">
                            <div class="category-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <h3>All Categories</h3>
                            <span class="recipe-count"><?php echo count($recipes); ?> recipes</span>
                        </a>
                    </div>
                    
                    <?php foreach ($categories as $cat): ?>
                        <div class="category-card <?php echo ($selectedCategoryId == $cat['id']) ? 'active' : ''; ?>">
                            <a href="<?php echo $rootPath; ?>pages/recipes-categories.php?category=<?php echo $cat['id']; ?>">
                                <div class="category-icon">
                                    <i class="fas <?php echo getCategoryIcon($cat['name']); ?>"></i>
                                </div>
                                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                                <span class="recipe-count"><?php echo getRecipeCount($db, $cat['id']); ?> recipes</span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>No categories found. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recipes Section -->
    <div class="section mt-4">
        <div class="recipes-header">
            <h2 class="page-title">
                <?php if ($selectedCategoryId): ?>
                    <?php 
                    $categoryName = '';
                    foreach ($categories as $cat) {
                        if ($cat['id'] == $selectedCategoryId) {
                            $categoryName = $cat['name'];
                            break;
                        }
                    }
                    echo htmlspecialchars($categoryName) . ' Recipes'; 
                    ?>
                <?php else: ?>
                    All Recipes
                <?php endif; ?>
            </h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $rootPath; ?>pages/add-recipe.php" class="btn">Add a New Recipe</a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($recipes)): ?>
            <div class="alert alert-info">
                No recipes found in this category. 
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $rootPath; ?>pages/add-recipe.php">Be the first to add one!</a>
                <?php else: ?>
                    <a href="<?php echo $rootPath; ?>pages/login.php">Login</a> to add a recipe.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="recipe-grid">
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card">
                        <div class="recipe-header">
                            <?php if (!empty($recipe['image'])): ?>
                                <img src="<?php echo $rootPath; ?>uploads/recipes/<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                            <?php else: ?>
                                <div class="no-image">No Image</div>
                            <?php endif; ?>
                            <?php if (!empty($recipe['category_name'])): ?>
                                <span class="category-badge"><?php echo htmlspecialchars($recipe['category_name']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="recipe-body">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p class="recipe-meta">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($recipe['author'] ?? 'Unknown'); ?></span>
                                <?php if (!empty($recipe['difficulty'])): ?>
                                    <span><i class="fas fa-signal"></i> <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($recipe['prep_time'])): ?>
                                    <span><i class="far fa-clock"></i> Prep: <?php echo htmlspecialchars($recipe['prep_time']); ?> min</span>
                                <?php endif; ?>
                                <?php if (!empty($recipe['cook_time'])): ?>
                                    <span><i class="fas fa-fire"></i> Cook: <?php echo htmlspecialchars($recipe['cook_time']); ?> min</span>
                                <?php endif; ?>
                            </p>
                            <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn-outline">View Recipe</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>    
    </div>
</div>

<?php include '../includes/footer.php'; ?>
