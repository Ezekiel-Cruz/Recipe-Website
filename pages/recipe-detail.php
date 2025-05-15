<?php
// recipe-detail.php
session_start();
include '../includes/header.php';
include '../config/database.php';
include '../classes/Recipe.php';

// Process comment submission
if (isset($_POST['submit_comment']) && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $recipeId = $_GET['id'] ?? 0;
    $commentText = trim($_POST['comment']);
    
    if (!empty($commentText) && $recipeId > 0) {
        try {
            // Insert comment into database
            $stmt = $pdo->prepare("INSERT INTO comments (recipe_id, user_id, comment_text, created_at) 
                                  VALUES (:recipe_id, :user_id, :comment_text, NOW())");
            $stmt->bindParam(':recipe_id', $recipeId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':comment_text', $commentText);
            
            if ($stmt->execute()) {
                // Comment added successfully
                // Redirect to avoid form resubmission
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $recipeId . "&comment_added=1");
                exit;
            } else {
                $errorMsg = "Failed to add comment. Please try again.";
            }
        } catch (PDOException $e) {
            $errorMsg = "Database error: " . $e->getMessage();
            error_log($e->getMessage());
        }
    } else {
        $errorMsg = "Comment cannot be empty";
    }
}

$recipeId = $_GET['id'] ?? null;

if ($recipeId) {
    $recipe = new Recipe($pdo);
    $recipeDetails = $recipe->getRecipeById($recipeId);

    if ($recipeDetails) {
        // Get actual values from the database
        $createdDate = date("F j, Y", strtotime($recipeDetails['created_at'] ?? "now"));
        $author = $recipeDetails['author'] ?? "John Doe";
        $authorId = $recipeDetails['user_id'] ?? 1;
        $prepTime = $recipeDetails['prep_time'] ?? 0;
        $cookTime = $recipeDetails['cook_time'] ?? 0;
        $servings = $recipeDetails['servings'] ?? "4";
        $category = $recipeDetails['category_name'] ?? "Main Course";
        $difficulty = $recipeDetails['difficulty'] ?? "Medium";
        ?>
        <div class="container">
            <div class="recipe-detail fade-in">
                <div class="recipe-header">
                    <h1 class="recipe-title"><?php echo htmlspecialchars($recipeDetails['title']); ?></h1>
                    
                    <div class="recipe-meta">
                        <span class="recipe-meta-item"><i class="far fa-calendar-alt"></i> <?php echo $createdDate; ?></span>
                        <span class="recipe-meta-item"><i class="far fa-user"></i> By <a href="<?php echo $rootPath; ?>pages/profile.php?id=<?php echo $authorId; ?>"><?php echo htmlspecialchars($author); ?></a></span>
                        <span class="recipe-meta-item"><i class="fas fa-hourglass-start"></i> Prep: <?php echo $prepTime; ?> mins</span>
                        <span class="recipe-meta-item"><i class="far fa-clock"></i> Cook: <?php echo $cookTime; ?> mins</span>
                        <span class="recipe-meta-item"><i class="fas fa-utensils"></i> <?php echo $servings; ?> servings</span>
                    </div>
                </div>
                
                <div class="recipe-image-container">
                    <?php 
                    // Define possible image paths
                    $recipeImage = $recipeDetails['image'] ?? '';
                    $imagePath = $rootPath . "uploads/recipes/" . ($recipeImage ?: 'sample-recipe.jpg');
                    $defaultImagePath = $rootPath . "assets/img/default-recipe.jpg";
                    
                    // Use a placeholder URL for demonstration purposes
                    $placeholderUrl = "https://via.placeholder.com/800x450?text=Delicious+Recipe";
                    
                    // Use the actual image if available, otherwise fallback to placeholder
                    $displayImagePath = file_exists($imagePath) ? $imagePath : $placeholderUrl;
                    ?>
                    <img src="<?php echo $displayImagePath; ?>" alt="<?php echo htmlspecialchars($recipeDetails['title']); ?>">
                    
                    <div class="recipe-badges">
                        <span class="recipe-badge recipe-badge-category"><?php echo htmlspecialchars($category); ?></span>
                        <span class="recipe-badge recipe-badge-difficulty"><?php echo htmlspecialchars($difficulty); ?></span>
                    </div>
                </div>
                
                <div class="recipe-actions">
                    <!-- Rating removed as requested -->
                </div>
                
                <div class="recipe-content">
                    <div class="recipe-ingredients">
                        <h2><i class="fas fa-list"></i> Ingredients</h2>
                        <ul class="ingredients-list">
                            <?php foreach (explode(',', $recipeDetails['ingredients']) as $ingredient): ?>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo htmlspecialchars(trim($ingredient)); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div class="recipe-instructions">
                        <h2><i class="fas fa-tasks"></i> Instructions</h2>
                        <div class="instructions-steps">
                            <?php 
                            $instructions = explode("\n", $recipeDetails['instructions']);
                            $stepNumber = 1;
                            foreach ($instructions as $step): 
                                if(trim($step) !== ''):
                            ?>
                                <div class="instruction-step">
                                    <div class="step-number"><?php echo $stepNumber++; ?></div>
                                    <div class="step-content"><?php echo htmlspecialchars(trim($step)); ?></div>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="recipe-notes">
                    <h2><i class="far fa-sticky-note"></i> Chef's Notes</h2>
                    <p><?php echo htmlspecialchars($recipeDetails['notes'] ?? "This recipe is perfect for weeknight dinners or special occasions. Feel free to adjust the seasoning to your taste preference."); ?></p>
                </div>
            </div>
            
            <div class="comments-section">
                <h2>Comments</h2>
                
                <?php if (isset($errorMsg)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $errorMsg; ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['comment_added']) && $_GET['comment_added'] == 1): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Your comment has been added successfully.
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="comment-form">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $recipeId; ?>" method="POST">
                        <div class="form-group">
                            <textarea name="comment" placeholder="Share your thoughts or experience with this recipe..." required></textarea>
                        </div>
                        <button type="submit" name="submit_comment" class="btn">Post Comment</button>
                    </form>
                </div>
                <?php else: ?>
                <div class="login-to-comment">
                    <p>Please <a href="<?php echo $rootPath; ?>pages/login.php">login</a> to leave a comment.</p>
                </div>
                <?php endif; ?>
                
                <div class="comments-list">
                    <?php
                    // Fetch comments for this recipe
                    try {
                        $commentsStmt = $pdo->prepare("
                            SELECT c.*, u.username as author_name 
                            FROM comments c
                            LEFT JOIN users u ON c.user_id = u.id
                            WHERE c.recipe_id = :recipe_id
                            ORDER BY c.created_at DESC
                        ");
                        $commentsStmt->bindParam(':recipe_id', $recipeId, PDO::PARAM_INT);
                        $commentsStmt->execute();
                        $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (count($comments) > 0) {
                            foreach ($comments as $comment) {
                                $commentDate = date("F j, Y", strtotime($comment['created_at']));
                                echo '<div class="comment">
                                    <div class="comment-header">
                                        <span class="comment-author">' . htmlspecialchars($comment['author_name']) . '</span>
                                        <span class="comment-date">' . $commentDate . '</span>
                                    </div>
                                    <div class="comment-body">
                                        <p>' . htmlspecialchars($comment['comment_text']) . '</p>
                                    </div>
                                </div>';
                            }
                        } else {
                            echo '<p>No comments yet. Be the first to comment!</p>';
                        }
                    } catch (PDOException $e) {
                        echo '<p>Unable to load comments at this time.</p>';
                        error_log($e->getMessage());
                    }
                    ?>
                </div>
            </div>
            
            <div class="related-recipes">
                <h2>You Might Also Like</h2>
                <div class="recipe-list">
                    <!-- Related recipes will be loaded dynamically from the database -->
                </div>
            </div>
        </div>
        <?php
    } else {
        echo "<div class='container'><div class='error-message'><i class='fas fa-exclamation-circle'></i> Recipe not found.</div></div>";
    }
} else {
    echo "<div class='container'><div class='error-message'><i class='fas fa-exclamation-circle'></i> No recipe ID provided.</div></div>";
}

include '../includes/footer.php';
?>