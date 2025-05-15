<?php
class Category {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function addCategory($name) {
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    public function getCategories() {
        $stmt = $this->db->query("SELECT * FROM categories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateCategory($id, $name) {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllCategories() {
        // This method is maintained for backward compatibility
        return $this->getCategories();
    }
    
    public function getTopCategories($limit = 5) {
        try {
            // Query to get categories with the most recipes
            $query = "SELECT c.id, c.name, COUNT(r.id) as recipe_count, 
                      CASE 
                        WHEN c.name = 'Breakfast' THEN 'fas fa-coffee'
                        WHEN c.name = 'Lunch' THEN 'fas fa-hamburger'
                        WHEN c.name = 'Dinner' THEN 'fas fa-utensils'
                        WHEN c.name = 'Snacks' THEN 'fas fa-apple-alt'
                        WHEN c.name = 'Dessert' THEN 'fas fa-cookie'
                        WHEN c.name = 'Appetizer' THEN 'fas fa-cheese'
                        WHEN c.name = 'Side Dish' THEN 'fas fa-bread-slice'
                        WHEN c.name = 'Beverage' OR c.name = 'Drinks' THEN 'fas fa-glass-martini-alt'
                        WHEN c.name = 'Soup' THEN 'fas fa-mug-hot'
                        WHEN c.name = 'Salad' THEN 'fas fa-leaf'
                        ELSE 'fas fa-utensils'
                      END as icon
                     FROM categories c
                     LEFT JOIN recipes r ON c.id = r.category_id
                     GROUP BY c.id
                     ORDER BY recipe_count DESC
                     LIMIT :limit";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting top categories: " . $e->getMessage());
            return [];
        }
    }
}