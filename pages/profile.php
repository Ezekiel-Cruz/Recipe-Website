<?php
session_start();
include('../includes/header.php');
include('../config/database.php');
include('../classes/User.php');
include('../classes/Recipe.php');
include('../includes/functions.php'); // Keeping this for backward compatibility

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Initialize User and Recipe objects
$userObj = new User($db);
$recipeObj = new Recipe($db);

// Process profile update
$profilePicMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    if ($userObj->updateProfile($_SESSION['user_id'], $username, $email, $bio)) {
        $profilePicMessage = '<div class="alert alert-success">Profile updated successfully!</div>';
    } else {
        $profilePicMessage = '<div class="alert alert-danger">Failed to update profile. Please try again.</div>';
    }
}

// Handle recipe deletion
if (isset($_GET['delete_recipe']) && is_numeric($_GET['delete_recipe'])) {
    $recipeId = (int)$_GET['delete_recipe'];
    // Verify recipe belongs to current user before deleting
    $checkRecipe = $db->prepare("SELECT id FROM recipes WHERE id = ? AND user_id = ?");
    $checkRecipe->execute([$recipeId, $_SESSION['user_id']]);
    if ($checkRecipe->rowCount() > 0) {
        if ($recipeObj->deleteRecipe($recipeId)) {
            $profilePicMessage = '<div class="alert alert-success">Recipe deleted successfully!</div>';
        } else {
            $profilePicMessage = '<div class="alert alert-danger">Failed to delete recipe. Please try again.</div>';
        }
    } else {
        $profilePicMessage = '<div class="alert alert-danger">You do not have permission to delete this recipe.</div>';
    }
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$user = $userObj->getUserById($user_id);

// Check if user was found
if (!$user) {
    echo '<div class="alert alert-danger">User not found. There may be an issue with your account.</div>';
    include('../includes/footer.php');
    exit();
}

// Pagination settings
$recipesPerPage = 6;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) $currentPage = 1;

// Get total number of user's recipes
$countStmt = $db->prepare("SELECT COUNT(*) as total FROM recipes WHERE user_id = ?");
$countStmt->execute([$user_id]);
$totalRecipes = $countStmt->fetchColumn();
$totalPages = ceil($totalRecipes / $recipesPerPage);

// Adjust the query to include pagination
$offset = ($currentPage - 1) * $recipesPerPage;
$stmt = $db->prepare("SELECT r.*, c.name as category_name FROM recipes r 
                     LEFT JOIN categories c ON r.category_id = c.id 
                     WHERE r.user_id = ? 
                     ORDER BY r.created_at DESC
                     LIMIT $recipesPerPage OFFSET $offset");
$stmt->execute([$user_id]);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <?php echo $profilePicMessage; ?>
    
    <div class="profile-container">
        
            <div class="profile-details">
                <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><i class="fas fa-calendar-alt"></i> Member since <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                <?php if (!empty($user['bio'])): ?>
                    <div class="profile-bio">
                        <h3>About Me</h3>
                        <p><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                    </div>
                <?php endif; ?>
                <button class="edit-profile-btn" id="edit-profile-button">
                     Edit Profile
                </button>
            </div>
        
        
        <!-- Edit Profile Modal -->
        <div id="edit-profile-modal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit Your Profile</h2>
                <form method="POST" class="edit-profile-form">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio (Optional)</label>
                        <textarea name="bio" id="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        
        <h2><i class="fas fa-utensils"></i> My Recipes</h2>
        <?php if (!empty($recipes)): ?>
            <p>You have shared <?php echo $totalRecipes; ?> recipe(s)</p>
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
                                <?php if (!empty($recipe['difficulty'])): ?>
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
                            <div class="recipe-actions">
                                <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn-outline"><i class="fas fa-eye"></i> View</a>
                                <a href="<?php echo $rootPath; ?>pages/edit-recipe.php?id=<?php echo $recipe['id']; ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo $rootPath; ?>pages/profile.php?delete_recipe=<?php echo $recipe['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this recipe? This action cannot be undone.')"><i class="fas fa-trash-alt"></i> Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-recipes">
                <p>You haven't added any recipes yet.</p>
                <p><a href="<?php echo $rootPath; ?>pages/add-recipe.php" class="btn-primary"><i class="fas fa-plus"></i> Add Your First Recipe</a></p>
            </div>
        <?php endif; ?>
        
        <!-- Pagination for My Recipes -->
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
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Profile Modal
        const modal = document.getElementById('edit-profile-modal');
        const editProfileBtn = document.getElementById('edit-profile-button');
        const closeBtn = document.querySelector('.close');
        
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', function() {
                modal.style.display = 'block';
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>

<?php include('../includes/footer.php'); ?>