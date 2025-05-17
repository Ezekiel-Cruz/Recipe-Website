<?php
class Recipe {
    // Using public properties to allow direct access
    public $id;
    public $title;
    public $ingredients;
    public $instructions;
    public $category_id;
    public $user_id;
    public $created_at;
    public $updated_at;
    public $db;
    public $image;
    public $notes;
    public $difficulty;
    public $prep_time;
    public $cook_time;
    public $servings;

    // Constructor with optional parameters for a flexible API
    public function __construct($db, $title = null, $ingredients = null, $instructions = null, $category_id = null, $user_id = null) {
        $this->db = $db;
        
        if ($title !== null) {
            $this->title = $title;
            $this->ingredients = $ingredients;
            $this->instructions = $instructions;
            $this->category_id = $category_id;
            $this->user_id = $user_id;
            $this->created_at = date("Y-m-d H:i:s");
            $this->updated_at = date("Y-m-d H:i:s");
            
            // Initialize additional fields with default values
            $this->difficulty = null;
            $this->prep_time = null;
            $this->cook_time = null;
            $this->servings = null;
            $this->notes = null;
            $this->image = null;
        }
    }

    public function save() {
        // Code to save the recipe to the database
        try {
            $stmt = $this->db->prepare("INSERT INTO recipes (title, ingredients, instructions, category_id, user_id, image, notes, difficulty, prep_time, cook_time, servings) 
                                        VALUES (:title, :ingredients, :instructions, :category_id, :user_id, :image, :notes, :difficulty, :prep_time, :cook_time, :servings)");
            
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':ingredients', $this->ingredients);
            $stmt->bindParam(':instructions', $this->instructions);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':notes', $this->notes);
            $stmt->bindParam(':difficulty', $this->difficulty);
            $stmt->bindParam(':prep_time', $this->prep_time);
            $stmt->bindParam(':cook_time', $this->cook_time);
            $stmt->bindParam(':servings', $this->servings);
            
            // Debug log for values being saved
            error_log("Recipe data to save: " . json_encode([
                'title' => $this->title,
                'ingredients' => substr($this->ingredients, 0, 100) . '...',
                'category_id' => $this->category_id,
                'user_id' => $this->user_id,
                'difficulty' => $this->difficulty,
                'prep_time' => $this->prep_time,
                'cook_time' => $this->cook_time,
                'servings' => $this->servings
            ]));
            
            $result = $stmt->execute();
            if ($result) {
                $this->id = $this->db->lastInsertId();
                error_log("Recipe saved with ID: " . $this->id);
            } else {
                error_log("Database error: " . print_r($stmt->errorInfo(), true));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("PDO Exception: " . $e->getMessage());
            return false;
        }
    }

    public function update($id) {
        // Code to update the recipe in the database
        try {
            $stmt = $this->db->prepare("UPDATE recipes SET 
                                        title = :title, 
                                        ingredients = :ingredients, 
                                        instructions = :instructions, 
                                        category_id = :category_id,
                                        image = :image,
                                        notes = :notes,
                                        difficulty = :difficulty,
                                        prep_time = :prep_time,
                                        cook_time = :cook_time,
                                        servings = :servings,
                                        updated_at = NOW()
                                        WHERE id = :id AND user_id = :user_id");
            
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':ingredients', $this->ingredients);
            $stmt->bindParam(':instructions', $this->instructions);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':notes', $this->notes);
            $stmt->bindParam(':difficulty', $this->difficulty);
            $stmt->bindParam(':prep_time', $this->prep_time);
            $stmt->bindParam(':cook_time', $this->cook_time);
            $stmt->bindParam(':servings', $this->servings);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $this->user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        // First get the recipe to delete its image if needed
        try {
            // Get recipe details to find the image filename
            $stmt = $this->db->prepare("SELECT image FROM recipes WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete the associated image if it exists
            if ($recipe && !empty($recipe['image'])) {
                $imagePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'recipes' . DIRECTORY_SEPARATOR . $recipe['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                    error_log("Deleted image file: " . $imagePath);
                }
            }
            
            // Delete the recipe from the database
            $stmt = $this->db->prepare("DELETE FROM recipes WHERE id = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting recipe: " . $e->getMessage());
            return false;
        }
    }

    public function getRecipeById($id) {
        try {
            $stmt = $this->db->prepare("SELECT r.*, u.username as author, c.name as category_name 
                                       FROM recipes r 
                                       LEFT JOIN users u ON r.user_id = u.id 
                                       LEFT JOIN categories c ON r.category_id = c.id 
                                       WHERE r.id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Recipe::getRecipeById Error: " . $e->getMessage());
            return null;
        }
    }
    
    // Method to handle image uploads
    public function uploadImage($file) {
        // Check if there was an upload error
        if($file['error'] !== UPLOAD_ERR_OK) {
            error_log("Upload error code: " . $file['error']);
            return false;
        }
        
        // Define the upload directory path properly for XAMPP
        $uploadDir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'recipes' . DIRECTORY_SEPARATOR;
        
        // Log path for debugging
        error_log("Upload directory path: " . $uploadDir);
        
        // Check if upload directory exists, if not create it
        if (!file_exists($uploadDir)) {
            error_log("Creating upload directory: " . $uploadDir);
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("Failed to create directory: " . $uploadDir . " - " . error_get_last()['message']);
                return false;
            }
        }
        
        // Force set permissions to ensure directory is writable
        @chmod($uploadDir, 0777);
        
        // Check if directory is writable
        if (!is_writable($uploadDir)) {
            error_log("Directory is still not writable after chmod: " . $uploadDir);
            return false;
        } else {
            error_log("Directory is writable: " . $uploadDir);
        }
        
        // Generate a unique filename with random element to avoid collisions
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        // Only allow specific image extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            error_log("File extension not allowed: " . $fileExtension);
            return false;
        }
        
        $uniqueId = uniqid('recipe_', true);
        $filename = $uniqueId . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $filename;
        
        error_log("Target file path: " . $targetFilePath);
        error_log("Temp file path: " . $file['tmp_name']);
        
        // Check if image file is an actual image
        $check = @getimagesize($file['tmp_name']);
        if ($check === false) {
            error_log("File is not a valid image");
            return false;
        }
        
        // Check file size (< 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            error_log("File too large: " . $file['size'] . " bytes");
            return false;
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            $this->image = $filename;
            error_log("File successfully uploaded to: " . $targetFilePath);
            return true;
        } else {
            error_log("Failed to move uploaded file. Last error: " . error_get_last()['message']);
            return false;
        }
    }
    
    // Method to get image path
    public function getImagePath($relativePath = true) {
        if (empty($this->image)) {
            return $relativePath ? '/recipe-website/assets/img/default-recipe.jpg' : $_SERVER['DOCUMENT_ROOT'] . '/recipe-website/assets/img/default-recipe.jpg';
        }
        
        return $relativePath ? '/recipe-website/uploads/recipes/' . $this->image : $_SERVER['DOCUMENT_ROOT'] . '/recipe-website/uploads/recipes/' . $this->image;
    }

    public function getAllRecipes() {
        try {
            $query = "SELECT r.*, u.username as author, c.name as category_name 
                     FROM recipes r 
                     LEFT JOIN users u ON r.user_id = u.id 
                     LEFT JOIN categories c ON r.category_id = c.id 
                     ORDER BY r.created_at DESC";
                     
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getIngredients() {
        return $this->ingredients;
    }

    public function setIngredients($ingredients) {
        $this->ingredients = $ingredients;
    }

    public function getInstructions() {
        return $this->instructions;
    }

    public function setInstructions($instructions) {
        $this->instructions = $instructions;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function getNotes() {
        return $this->notes;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }
    
    public function getAllRecipesByUserId($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM recipes WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function deleteRecipe($recipeId) {
        try {
            // First, get image file info if it exists
            $stmt = $this->db->prepare("SELECT image FROM recipes WHERE id = :id");
            $stmt->bindParam(':id', $recipeId);
            $stmt->execute();
            $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete the recipe record
            $stmt = $this->db->prepare("DELETE FROM recipes WHERE id = :id");
            $stmt->bindParam(':id', $recipeId);
            $result = $stmt->execute();
            
            // If deletion successful and there was an image, try to delete the image file too
            if ($result && !empty($recipe['image'])) {
                $imagePath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'recipes' . DIRECTORY_SEPARATOR . $recipe['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath); // Delete the file (suppress errors)
                }
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error deleting recipe: " . $e->getMessage());
            return false;
        }
    }
    
    public function getNewestRecipes($limit = 6) {
        try {
            $stmt = $this->db->prepare("SELECT r.*, u.username as author, c.name as category_name 
                                       FROM recipes r 
                                       LEFT JOIN users u ON r.user_id = u.id 
                                       LEFT JOIN categories c ON r.category_id = c.id 
                                       ORDER BY r.created_at DESC 
                                       LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting newest recipes: " . $e->getMessage());
            return [];
        }
    }
    
    public function getRecipesByCategory($categoryId, $limit = 10) {
        try {
            $stmt = $this->db->prepare("SELECT r.*, u.username as author FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.category_id = :category_id ORDER BY r.created_at DESC LIMIT :limit");
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public function searchRecipes($keyword) {
        try {
            $keyword = '%' . $keyword . '%';
            $stmt = $this->db->prepare("SELECT r.*, u.username as author FROM recipes r JOIN users u ON r.user_id = u.id WHERE r.title LIKE :keyword OR r.ingredients LIKE :keyword OR r.instructions LIKE :keyword ORDER BY r.created_at DESC");
            $stmt->bindParam(':keyword', $keyword);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
