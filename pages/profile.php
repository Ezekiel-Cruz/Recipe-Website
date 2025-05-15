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

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$user = $userObj->getUserById($user_id);

// Check if user was found
if (!$user) {
    echo '<div class="alert alert-danger">User not found. There may be an issue with your account.</div>';
    include('../includes/footer.php');
    exit();
}

// Get user's recipes using the Recipe class
$stmt = $db->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="profile-container">
    <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
    
    <div class="profile-info">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
    </div>
    
    <h2><i class="fas fa-utensils"></i> My Recipes</h2>
    <?php if (!empty($recipes)): ?>
    <p>You have shared <?php echo count($recipes); ?> recipe(s)</p>
    <ul class="recipe-list">
        <?php foreach ($recipes as $recipe): ?>
            <li>
                <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=<?php echo $recipe['id']; ?>">
                    <i class="fas fa-book-open"></i> <?php echo htmlspecialchars($recipe['title']); ?>
                </a>
                <div class="recipe-actions">
                    <a href="<?php echo $rootPath; ?>pages/edit-recipe.php?id=<?php echo $recipe['id']; ?>" class="btn-edit" title="Edit Recipe">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <div class="empty-recipes">
        <p>You haven't added any recipes yet.</p>
        <p><a href="<?php echo $rootPath; ?>pages/add-recipe.php" class="btn-primary"><i class="fas fa-plus"></i> Add Your First Recipe</a></p>
    </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>