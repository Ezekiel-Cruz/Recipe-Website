<?php
session_start();
include('../includes/header.php');
include('../includes/functions.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id); // Assuming this function is defined in functions.php
$recipes = getUserRecipes($user_id); // Assuming this function is defined in functions.php
?>

<div class="profile-container">
    <h1><?php echo htmlspecialchars($user['username']); ?>'s Profile</h1>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    
    <h2>My Recipes</h2>
    <ul>
        <?php foreach ($recipes as $recipe): ?>
            <li>
                <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=<?php echo $recipe['id']; ?>">
                    <?php echo htmlspecialchars($recipe['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include('../includes/footer.php'); ?>