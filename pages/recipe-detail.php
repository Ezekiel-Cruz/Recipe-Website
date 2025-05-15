<?php
// recipe-detail.php
session_start();
include '../includes/header.php';
include '../config/database.php';
include '../classes/Recipe.php';

$recipeId = $_GET['id'] ?? null;

if ($recipeId) {
    $recipe = new Recipe($pdo);
    $recipeDetails = $recipe->getRecipeById($recipeId);

    if ($recipeDetails) {
        // For demo purposes, create some sample data if fields are missing
        $createdDate = date("F j, Y", strtotime($recipeDetails['created_at'] ?? "now"));
        $author = $recipeDetails['author'] ?? "John Doe";
        $authorId = $recipeDetails['user_id'] ?? 1;
        $cookTime = $recipeDetails['cook_time'] ?? "30 mins";
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
                        <span class="recipe-meta-item"><i class="far fa-clock"></i> <?php echo $cookTime; ?></span>
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
                    <button class="btn-outline"><i class="far fa-bookmark"></i> Save</button>
                    <button class="btn-outline"><i class="fas fa-print"></i> Print</button>
                    <button class="btn-outline"><i class="fas fa-share-alt"></i> Share</button>
                    
                    <div class="rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                        <span>(4.0)</span>
                    </div>
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
                <h2>Comments (3)</h2>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="comment-form">
                    <form action="#" method="POST">
                        <div class="form-group">
                            <textarea name="comment" placeholder="Share your thoughts or experience with this recipe..." required></textarea>
                        </div>
                        <button type="submit" class="btn">Post Comment</button>
                    </form>
                </div>
                <?php else: ?>
                <div class="login-to-comment">
                    <p>Please <a href="<?php echo $rootPath; ?>pages/login.php">login</a> to leave a comment.</p>
                </div>
                <?php endif; ?>
                
                <div class="comments-list">
                    <!-- Sample comments for display purposes -->
                    <div class="comment">
                        <div class="comment-header">
                            <span class="comment-author">Jane Smith</span>
                            <span class="comment-date">May 10, 2025</span>
                        </div>
                        <div class="comment-body">
                            <p>I made this recipe last night and it was delicious! My family loved it.</p>
                        </div>
                    </div>
                    
                    <div class="comment">
                        <div class="comment-header">
                            <span class="comment-author">Mike Johnson</span>
                            <span class="comment-date">May 8, 2025</span>
                        </div>
                        <div class="comment-body">
                            <p>Great recipe! I added a bit more garlic and it turned out perfect.</p>
                        </div>
                    </div>
                    
                    <div class="comment">
                        <div class="comment-header">
                            <span class="comment-author">Sarah Williams</span>
                            <span class="comment-date">May 5, 2025</span>
                        </div>
                        <div class="comment-body">
                            <p>I've made this three times now and it never disappoints. My new go-to recipe!</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="related-recipes">
                <h2>You Might Also Like</h2>
                <div class="recipe-list">
                    <!-- Sample related recipes -->
                    <div class="recipe-item hover-grow">
                        <img src="<?php echo $rootPath; ?>uploads/recipes/sample-recipe.jpg" alt="Related Recipe">
                        <div class="recipe-content">
                            <h3><a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=2">Italian Pasta Carbonara</a></h3>
                            <p>A classic Italian pasta dish with a creamy egg sauce.</p>
                        </div>
                    </div>
                    
                    <div class="recipe-item hover-grow">
                        <img src="<?php echo $rootPath; ?>uploads/recipes/sample-recipe.jpg" alt="Related Recipe">
                        <div class="recipe-content">
                            <h3><a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=3">Grilled Lemon Herb Chicken</a></h3>
                            <p>Juicy grilled chicken with fresh herbs and lemon.</p>
                        </div>
                    </div>
                    
                    <div class="recipe-item hover-grow">
                        <img src="<?php echo $rootPath; ?>uploads/recipes/sample-recipe.jpg" alt="Related Recipe">
                        <div class="recipe-content">
                            <h3><a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=4">Vegetable Stir Fry</a></h3>
                            <p>A quick and healthy vegetable stir fry with a savory sauce.</p>
                        </div>
                    </div>
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