<?php
session_start();
include '../includes/header.php';
include '../config/database.php';
include '../includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=add-recipe.php');
    exit();
}

// Get categories for dropdown
$categories = getCategories();
?>

<div class="container">
    <div class="form-container fade-in">
        <h1 class="text-center">Share Your Recipe</h1>
        <p class="text-center mb-2">Fill in the details below to share your delicious recipe with our community</p>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                There was an error submitting your recipe. Please try again.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                Your recipe has been submitted successfully! <a href="<?php echo $rootPath; ?>pages/recipes-categories.php">View all recipes</a>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo $rootPath; ?>api/recipes.php" method="POST" enctype="multipart/form-data" class="recipe-form">
            <div class="form-section">
                <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                
                <div class="form-group">
                    <label for="title">Recipe Title</label>
                    <input type="text" id="title" name="title" placeholder="Enter a descriptive title" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select a category</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">Breakfast</option>
                                <option value="2">Lunch</option>
                                <option value="3">Dinner</option>
                                <option value="4">Dessert</option>
                                <option value="5">Snacks</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="difficulty">Difficulty</label>
                        <select id="difficulty" name="difficulty" required>
                            <option value="">Select difficulty</option>
                            <option value="Easy">Easy</option>
                            <option value="Medium">Medium</option>
                            <option value="Hard">Hard</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="prep_time">Preparation Time (minutes)</label>
                        <input type="number" id="prep_time" name="prep_time" min="1" placeholder="Prep time in minutes" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cook_time">Cooking Time (minutes)</label>
                        <input type="number" id="cook_time" name="cook_time" min="0" placeholder="Cook time in minutes" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="servings">Servings</label>
                        <input type="number" id="servings" name="servings" min="1" placeholder="Number of servings" required>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-list"></i> Ingredients</h3>
                <p class="help-text">List each ingredient on a new line. Include quantity and measurement (e.g., 2 cups flour).</p>
                
                <div class="form-group">
                    <textarea id="ingredients" name="ingredients" rows="8" placeholder="Enter ingredients, one per line" required></textarea>
                </div>
                
                <button type="button" class="btn-outline btn-sm add-ingredient-btn">
                    <i class="fas fa-plus"></i> Add Another Ingredient
                </button>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-tasks"></i> Instructions</h3>
                <p class="help-text">Provide step-by-step instructions. Each paragraph will be treated as a separate step.</p>
                
                <div class="form-group">
                    <textarea id="instructions" name="instructions" rows="10" placeholder="Enter step-by-step instructions" required></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="fas fa-image"></i> Recipe Image</h3>
                
                <div class="form-group">
                    <label for="image" class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i> Choose Image
                        <input type="file" id="image" name="image" accept="image/*" class="file-upload-input">
                    </label>
                    <div id="file-name" class="file-name">No file chosen</div>
                    <p class="help-text">Upload a high-quality image of your finished dish. JPG, PNG or GIF. Max 5MB.</p>
                </div>
            </div>
            
            <div class="form-section">
                <h3><i class="far fa-sticky-note"></i> Additional Notes (Optional)</h3>
                
                <div class="form-group">
                    <textarea id="notes" name="notes" rows="4" placeholder="Share any tips, variations, or additional information about your recipe"></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-block">Submit Recipe <i class="fas fa-paper-plane"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
    // Simple script to show the selected file name
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('image');
        const fileNameDisplay = document.getElementById('file-name');
        
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
                fileNameDisplay.textContent = 'No file chosen';
            }
        });
        
        // Add ingredient button functionality
        const addIngredientBtn = document.querySelector('.add-ingredient-btn');
        const ingredientsTextarea = document.getElementById('ingredients');
        
        addIngredientBtn.addEventListener('click', function() {
            ingredientsTextarea.value += ingredientsTextarea.value ? '\n' : '';
            ingredientsTextarea.focus();
        });
    });
</script>

<?php
include '../includes/footer.php';
?>