<?php
// update_categories.php - One-time script to update categories in the database

require_once 'config/database.php';

try {
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();

    echo "Connected to database successfully.<br>";
    
    // Start transaction
    $db->beginTransaction();
    
    // First, update any recipes using categories that will be removed
    $query = "UPDATE recipes SET category_id = NULL WHERE category_id NOT IN (
        SELECT id FROM categories WHERE name IN (
            'Breakfast', 'Lunch', 'Dinner', 'Snacks', 'Dessert', 
            'Appetizer', 'Side Dish', 'Beverage / Drinks', 'Soup', 'Salad'
        )
    )";
    $db->exec($query);
    echo "Updated recipes with non-matching categories.<br>";
    
    // Delete all existing categories
    $query = "DELETE FROM categories";
    $db->exec($query);
    echo "Deleted existing categories.<br>";
    
    // Reset auto-increment
    $query = "ALTER TABLE categories AUTO_INCREMENT = 1";
    $db->exec($query);
    echo "Reset auto-increment.<br>";
    
    // Insert the new categories
    $categories = [
        'Breakfast',
        'Lunch',
        'Dinner',
        'Snacks',
        'Dessert',
        'Appetizer',
        'Side Dish',
        'Beverage / Drinks',
        'Soup',
        'Salad'
    ];
    
    $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
    
    foreach ($categories as $category) {
        $stmt->execute([$category]);
        echo "Added category: $category<br>";
    }
    
    // Commit transaction
    $db->commit();
    
    echo "<br>Categories updated successfully!";
    
} catch (PDOException $e) {
    // Roll back transaction on error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?>
