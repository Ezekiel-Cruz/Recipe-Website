<?php
/**
 * Database Initialization Script
 * 
 * This script initializes the database for the Recipe Website
 * It creates the database and tables if they don't exist
 * 
 * Usage: php database/init_db.php
 */

// Define constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'recipe_website');
define('SCHEMA_FILE', __DIR__ . '/schema.sql');

// Output formatting
function output($message, $type = 'info') {
    $colors = [
        'info' => "\033[0;36m", // Cyan
        'success' => "\033[0;32m", // Green
        'error' => "\033[0;31m", // Red
        'warning' => "\033[0;33m", // Yellow
        'reset' => "\033[0m"
    ];
    
    echo $colors[$type] . $message . $colors['reset'] . PHP_EOL;
}

// Main execution
try {
    // Connect to MySQL without selecting a database
    output("Connecting to MySQL...");
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    output("Connected successfully", "success");
    
    // Read and execute the SQL file
    output("Initializing database from schema file...");
    $sql = file_get_contents(SCHEMA_FILE);
    
    if (!$sql) {
        throw new Exception("Could not read schema file: " . SCHEMA_FILE);
    }
    
    // Execute the SQL commands
    $pdo->exec($sql);
    output("Database initialized successfully", "success");
    
    // Set up uploads directory
    $uploadsDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'recipes';
    output("Setting up uploads directory: " . $uploadsDir);
    
    if (!file_exists($uploadsDir)) {
        if (mkdir($uploadsDir, 0777, true)) {
            output("Created uploads directory", "success");
        } else {
            output("Failed to create uploads directory: " . error_get_last()['message'], "error");
        }
    } else {
        output("Uploads directory already exists", "success");
    }
    
    // Ensure the directory has proper permissions
    if (chmod($uploadsDir, 0777)) {
        output("Set uploads directory permissions", "success");
    } else {
        output("Failed to set uploads directory permissions", "warning");
    }
    
    // Test write permissions
    $testFile = $uploadsDir . DIRECTORY_SEPARATOR . 'test.txt';
    if (file_put_contents($testFile, 'test')) {
        output("Uploads directory is writable", "success");
        unlink($testFile); // Clean up test file
    } else {
        output("Uploads directory is not writable: " . error_get_last()['message'], "error");
    }
    
} catch (PDOException $e) {
    output("Database error: " . $e->getMessage(), "error");
    exit(1);
} catch (Exception $e) {
    output("Error: " . $e->getMessage(), "error");
    exit(1);
}

output("Recipe Website database setup complete!", "success");
output("You can now use the website with the following credentials:");
output("Username: test_user");
output("Password: password123");
?>
