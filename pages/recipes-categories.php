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

// Get search parameters
$searchTitle = isset($_GET['title']) ? trim($_GET['title']) : '';
$searchAuthor = isset($_GET['author']) ? trim($_GET['author']) : '';
$searchDifficulty = isset($_GET['difficulty']) ? trim($_GET['difficulty']) : '';

// Create recipe object
$recipeObj = new Recipe($db);

// Get recipes (filtered by category and search parameters if selected)
$query = "SELECT r.*, u.username as author, c.name as category_name 
          FROM recipes r 
          LEFT JOIN users u ON r.user_id = u.id 
          LEFT JOIN categories c ON r.category_id = c.id";

$whereConditions = [];
$params = [];

if ($selectedCategoryId) {
    $whereConditions[] = "r.category_id = :category_id";
    $params[':category_id'] = $selectedCategoryId;
}

if (!empty($searchTitle)) {
    $whereConditions[] = "r.title LIKE :title";
    $params[':title'] = "%{$searchTitle}%";
}

if (!empty($searchAuthor)) {
    $whereConditions[] = "u.username LIKE :author";
    $params[':author'] = "%{$searchAuthor}%";
}

if (!empty($searchDifficulty)) {
    $whereConditions[] = "r.difficulty = :difficulty";
    $params[':difficulty'] = $searchDifficulty;
}

if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(" AND ", $whereConditions);
}

$query .= " ORDER BY r.created_at DESC";

// Pagination settings
$recipesPerPage = 8;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;

// Get the total number of recipes
$countQuery = "SELECT COUNT(*) as count FROM recipes r LEFT JOIN users u ON r.user_id = u.id LEFT JOIN categories c ON r.category_id = c.id";
if (!empty($whereConditions)) {
    $countQuery .= " WHERE " . implode(" AND ", $whereConditions);
}
$countStmt = $db->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalRecipes = (int)$countStmt->fetchColumn();
$totalPages = ceil($totalRecipes / $recipesPerPage);

// Adjust the main query to include pagination
$offset = ($currentPage - 1) * $recipesPerPage;
$query .= " LIMIT $recipesPerPage OFFSET $offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
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

// Helper function to get total recipe count (for All Categories)
function getTotalRecipeCount($db) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM recipes");
    $stmt->execute();
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
    <?php endif; ?>
    
    <!-- Search Form -->
    <div class="search-container">
        <form action="<?php echo $rootPath; ?>pages/recipes-categories.php" method="GET" class="search-form">
            <?php if ($selectedCategoryId): ?>
                <input type="hidden" name="category" value="<?php echo $selectedCategoryId; ?>">
            <?php endif; ?>
            
            <div class="search-row">
                <div class="search-field">
                    <label for="title">Recipe Name</label>
                    <input type="text" id="title" name="title" placeholder="Search recipes..." value="<?php echo htmlspecialchars($searchTitle ?? ''); ?>">
                </div>
                
                <div class="search-field">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" placeholder="Search by author..." value="<?php echo htmlspecialchars($searchAuthor ?? ''); ?>">
                </div>
                
                <div class="search-field">
                    <label for="difficulty">Difficulty</label>
                    <select id="difficulty" name="difficulty">
                        <option value="">Any difficulty</option>
                        <option value="Easy" <?php echo ($searchDifficulty === 'Easy') ? 'selected' : ''; ?>>Easy</option>
                        <option value="Medium" <?php echo ($searchDifficulty === 'Medium') ? 'selected' : ''; ?>>Medium</option>
                        <option value="Hard" <?php echo ($searchDifficulty === 'Hard') ? 'selected' : ''; ?>>Hard</option>
                    </select>
                </div>
                
                <div class="search-buttons">
                    <button type="submit" class="btn search-btn">Search</button>
                    <a href="<?php echo $rootPath; ?>pages/recipes-categories.php<?php echo $selectedCategoryId ? '?category=' . $selectedCategoryId : ''; ?>" class="btn reset-btn">Reset</a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Category Sections Container -->
    <div class="categories-container">
        <!-- Recipe Categories Section -->
        <div class="category-section full-width">
            <div class="section-header">
                <h2 class="section-title">Recipe Categories</h2>
                <p class="subtitle">Browse recipes by category</p>
            </div>
            <?php if ($categories): ?>                <div class="categories-grid">                    <!-- Add "All Categories" option -->
                    <div class="category-card <?php echo $selectedCategoryId ? '' : 'active'; ?>">
                        <a href="<?php echo $rootPath; ?>pages/recipes-categories.php">
                            <div class="category-icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="category-text">
                                <h3>All Categories</h3>
                                <span class="recipe-count"><?php echo getTotalRecipeCount($db); ?> recipes</span>
                            </div>
                        </a>
                    </div>
                    
                    <?php foreach ($categories as $cat): ?>
                        <div class="category-card <?php echo ($selectedCategoryId == $cat['id']) ? 'active' : ''; ?>">
                            <a href="<?php echo $rootPath; ?>pages/recipes-categories.php?category=<?php echo $cat['id']; ?>">
                                <div class="category-icon">
                                    <i class="fas <?php echo getCategoryIcon($cat['name']); ?>"></i>
                                </div>
                                <div class="category-text">
                                    <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                                    <span class="recipe-count"><?php echo getRecipeCount($db, $cat['id']); ?> recipes</span>
                                </div>
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
                <?php endif; ?>            </h2>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $rootPath; ?>pages/add-recipe.php" class="btn">Add a New Recipe</a>
            <?php endif; ?>
        </div>
        
        <?php 
        // Display search information if any search parameters were used
        if (!empty($searchTitle) || !empty($searchAuthor) || !empty($searchDifficulty)): 
        ?>
        <div class="search-info">
            <p>
                Search results 
                <?php if (!empty($searchTitle)): ?>
                    for recipe name: <strong><?php echo htmlspecialchars($searchTitle); ?></strong>
                <?php endif; ?>
                
                <?php if (!empty($searchAuthor)): ?>
                    <?php echo !empty($searchTitle) ? ' and' : ''; ?> by author: <strong><?php echo htmlspecialchars($searchAuthor); ?></strong>
                <?php endif; ?>
                
                <?php if (!empty($searchDifficulty)): ?>
                    <?php echo (!empty($searchTitle) || !empty($searchAuthor)) ? ' and' : ''; ?> with difficulty: <strong><?php echo htmlspecialchars($searchDifficulty); ?></strong>
                <?php endif; ?>
            </p>
            <p>Found <?php echo count($recipes); ?> recipe(s)</p>
        </div>
        <?php endif; ?>
        
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
            <div class="recipe-grid">                <?php foreach ($recipes as $recipe): ?>
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
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($recipe['author'] ?? 'Unknown'); ?></span>                                <?php if (!empty($recipe['difficulty'])): ?>
                                    <span><i class="fas fa-signal"></i> <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($recipe['prep_time']) || !empty($recipe['cook_time'])): ?>
                                    <span><i class="far fa-clock"></i> 
                                    <?php if (!empty($recipe['prep_time']) && !empty($recipe['cook_time'])): ?>
                                        Prep: <?php echo htmlspecialchars($recipe['prep_time']); ?> min | Cook: <?php echo htmlspecialchars($recipe['cook_time']); ?> min
                                    <?php elseif (!empty($recipe['prep_time'])): ?>
                                        Prep: <?php echo htmlspecialchars($recipe['prep_time']); ?> min
                                    <?php elseif (!empty($recipe['cook_time'])): ?>
                                        Cook: <?php echo htmlspecialchars($recipe['cook_time']); ?> min
                                    <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                            <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn-outline">View Recipe</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
              <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-container">
                    <ul class="pagination">
                        <!-- Previous page link -->
                        <li class="previous <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                            <?php if ($currentPage > 1): ?>
                                <a href="<?php 
                                    $prevPageParams = $_GET;
                                    $prevPageParams['page'] = $currentPage - 1;
                                    echo '?' . http_build_query($prevPageParams);
                                ?>" aria-label="Previous" data-page="<?php echo $currentPage - 1; ?>">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            <?php else: ?>
                                <span aria-hidden="true">&laquo;</span>
                            <?php endif; ?>
                        </li>
                        
                        <!-- Page number links -->
                        <?php
                        // Show fewer page links on mobile
                        $maxLinks = 5;
                        $startPage = max(1, $currentPage - floor($maxLinks / 2));
                        $endPage = min($totalPages, $startPage + $maxLinks - 1);
                        
                        // Adjust start page if we're near the end
                        if ($endPage - $startPage < $maxLinks - 1) {
                            $startPage = max(1, $endPage - $maxLinks + 1);
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                <?php if ($i != $currentPage): ?>
                                    <a href="<?php 
                                        $pageParams = $_GET;
                                        $pageParams['page'] = $i;
                                        echo '?' . http_build_query($pageParams);
                                    ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                                <?php else: ?>
                                    <span><?php echo $i; ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Next page link -->
                        <li class="next <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?php 
                                    $nextPageParams = $_GET;
                                    $nextPageParams['page'] = $currentPage + 1;
                                    echo '?' . http_build_query($nextPageParams);
                                ?>" aria-label="Next" data-page="<?php echo $currentPage + 1; ?>">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            <?php else: ?>
                                <span aria-hidden="true">&raquo;</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>    
    </div>
</div>

<?php include '../includes/footer.php'; ?>
