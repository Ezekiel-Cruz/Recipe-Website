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
        }
    }

    public function save() {
        // Code to save the recipe to the database
        try {
            $stmt = $this->db->prepare("INSERT INTO recipes (title, ingredients, instructions, category_id, user_id, image, notes) 
                                        VALUES (:title, :ingredients, :instructions, :category_id, :user_id, :image, :notes)");
            
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':ingredients', $this->ingredients);
            $stmt->bindParam(':instructions', $this->instructions);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':notes', $this->notes);
            
            return $stmt->execute();
        } catch (PDOException $e) {
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
                                        updated_at = NOW()
                                        WHERE id = :id AND user_id = :user_id");
            
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':ingredients', $this->ingredients);
            $stmt->bindParam(':instructions', $this->instructions);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':image', $this->image);
            $stmt->bindParam(':notes', $this->notes);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $this->user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        // Code to delete the recipe from the database
        try {
            $stmt = $this->db->prepare("DELETE FROM recipes WHERE id = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
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
            return null;
        }
    }
    
    // Method to handle image uploads
    public function uploadImage($file) {
        // Check if upload directory exists, if not create it
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/recipe-website/uploads/recipes/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate a unique filename
        $filename = 'recipe_' . time() . '_' . basename($file['name']);
        $targetFilePath = $uploadDir . $filename;
        
        // Check if image file is a actual image
        $check = getimagesize($file['tmp_name']);
        if($check === false) {
            return false;
        }
        
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            $this->image = $filename;
            return true;
        } else {
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
    
    public function getFeaturedRecipes($limit = 6) {
        try {
            $stmt = $this->db->prepare("SELECT r.*, u.username as author FROM recipes r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
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