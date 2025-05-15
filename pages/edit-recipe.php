<?php
session_start();
include '../includes/header.php';
include '../config/database.php';
include '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . $rootPath . "pages/login.php");
    exit();
}

$recipe_id = $_GET['id'] ?? null;
$recipe = null;

if ($recipe_id) {
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $recipe_id, 'user_id' => $_SESSION['user_id']]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $ingredients = $_POST['ingredients'] ?? '';
    $instructions = $_POST['instructions'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $difficulty = $_POST['difficulty'] ?? null;
    $prep_time = $_POST['prep_time'] ?? null;
    $cook_time = $_POST['cook_time'] ?? null;
    $servings = $_POST['servings'] ?? null;
    $notes = $_POST['notes'] ?? null;

    $stmt = $pdo->prepare("UPDATE recipes SET 
                title = :title, 
                ingredients = :ingredients, 
                instructions = :instructions, 
                category_id = :category_id, 
                difficulty = :difficulty, 
                prep_time = :prep_time, 
                cook_time = :cook_time, 
                servings = :servings, 
                notes = :notes, 
                updated_at = NOW() 
            WHERE id = :id");
    
    $stmt->execute([
        'title' => $title,
        'ingredients' => $ingredients,
        'instructions' => $instructions,
        'category_id' => $category_id,
        'difficulty' => $difficulty,
        'prep_time' => $prep_time,
        'cook_time' => $cook_time,
        'servings' => $servings,
        'notes' => $notes,
        'id' => $recipe_id
    ]);

    // Handle image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploads_dir = $rootPath . 'uploads/recipes/';
        $tmp_name = $_FILES['image']['tmp_name'];
        $name = basename($_FILES['image']['name']);
        $new_name = 'recipe_' . $recipe_id . '_' . time() . '.' . pathinfo($name, PATHINFO_EXTENSION);
        
        if (move_uploaded_file($tmp_name, $uploads_dir . $new_name)) {
            $stmt = $pdo->prepare("UPDATE recipes SET image = :image WHERE id = :id");
            $stmt->execute([
                'image' => $new_name,
                'id' => $recipe_id
            ]);
        }
    }

    header("Location: " . $rootPath . "pages/recipe-detail.php?id=" . $recipe_id);
    exit();
}

// Get categories for dropdown
$categories = getCategories();
?>

<div class="container">
    <div class="form-container fade-in">
        <h1 class="text-center">Edit Your Recipe</h1>
        
        <?php if ($recipe): ?>
            <form action="" method="POST" enctype="multipart/form-data" class="recipe-form">
                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                    
                    <div class="form-group">
                        <label for="title">Recipe Title</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id">
                                <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= ($recipe['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="difficulty">Difficulty</label>
                            <select id="difficulty" name="difficulty">
                                <option value="">Select difficulty</option>
                                <option value="Easy" <?= ($recipe['difficulty'] == 'Easy') ? 'selected' : '' ?>>Easy</option>
                                <option value="Medium" <?= ($recipe['difficulty'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                                <option value="Hard" <?= ($recipe['difficulty'] == 'Hard') ? 'selected' : '' ?>>Hard</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prep_time">Preparation Time (minutes)</label>
                            <input type="number" id="prep_time" name="prep_time" min="1" value="<?= htmlspecialchars($recipe['prep_time'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cook_time">Cooking Time (minutes)</label>
                            <input type="number" id="cook_time" name="cook_time" min="0" value="<?= htmlspecialchars($recipe['cook_time'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="servings">Servings</label>
                            <input type="number" id="servings" name="servings" min="1" value="<?= htmlspecialchars($recipe['servings'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-list"></i> Ingredients</h3>
                    <div class="form-group">
                        <label for="ingredients">List all ingredients (one per line)</label>
                        <textarea id="ingredients" name="ingredients" rows="8" required><?= htmlspecialchars($recipe['ingredients']) ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-utensils"></i> Instructions</h3>
                    <div class="form-group">
                        <label for="instructions">Cooking Instructions</label>
                        <textarea id="instructions" name="instructions" rows="10" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-sticky-note"></i> Additional Notes</h3>
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="4"><?= htmlspecialchars($recipe['notes'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-image"></i> Recipe Image</h3>
                    <div class="form-group">
                        <label for="image">Upload a New Image (Optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    
                    <?php if (!empty($recipe['image'])): ?>
                    <div class="current-image">
                        <p>Current Image:</p>
                        <img src="<?php echo $rootPath; ?>uploads/recipes/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Current recipe image" style="max-width: 200px;">
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Recipe</button>
                    <a href="<?php echo $rootPath; ?>pages/recipe-detail.php?id=<?php echo $recipe_id; ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">
                <p>Recipe not found or you don't have permission to edit it.</p>
                <a href="<?php echo $rootPath; ?>pages/home.php" class="btn btn-primary">Return to Home</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>