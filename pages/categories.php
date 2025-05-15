<?php
include '../includes/header.php';
include '../config/database.php';
include '../classes/Category.php';

// Get categories
$category = new Category($pdo);
$categories = $category->getAllCategories();

// Get filter if exists
$filter = isset($_GET['category']) ? $_GET['category'] : '';
?>

<div class="container">
    <div class="categories-header">
        <h1 class="page-title">Recipe Categories</h1>
        <p class="subtitle">Explore our delicious recipes by category</p>
    </div>

    <div class="categories-container">
        <?php if ($categories): ?>
            <div class="categories-grid">
                <?php foreach ($categories as $cat): ?>
                    <div class="category-card">
                        <a href="<?php echo $rootPath; ?>pages/categories.php?category=<?php echo strtolower(htmlspecialchars($cat['name'])); ?>">
                            <div class="category-icon">
                                <i class="fas <?php echo getCategoryIcon($cat['name']); ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                            <span class="recipe-count"><?php echo getRecipeCount($pdo, $cat['id']); ?> recipes</span>
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

<?php
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
function getRecipeCount($pdo, $categoryId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recipes WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $categoryId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] ?? 0;
}

include '../includes/footer.php';
?>