<?php
// functions.php - Legacy utility functions
// Note: Many of these functions are now duplicated in class-based alternatives
// Prefer using classes (Recipe, User, Category) for new code

function connectDatabase() {
    $host = 'localhost';
    $db = 'recipe_website';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserById($id) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    // Add error logging if user not found
    if (!$user) {
        error_log("User with ID $id not found in database");
    }
    
    return $user;
}

function loginUser($email, $password) {
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function registerUser($username, $email, $password) {
    $pdo = connectDatabase();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return false; // Email already exists
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$username, $email, $hashedPassword]);
}

function addRecipe($title, $ingredients, $instructions, $userId) {
    // Note: Consider using the Recipe class for new code
    // This function doesn't support newer fields like difficulty, prep_time, etc.
    $pdo = connectDatabase();
    $stmt = $pdo->prepare("INSERT INTO recipes (title, ingredients, instructions, user_id) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$title, $ingredients, $instructions, $userId]);
}

function getCategories() {
    // Note: Consider using the Category class for new code
    $pdo = connectDatabase();
    $stmt = $pdo->query("SELECT * FROM categories");
    return $stmt->fetchAll();
}

function getUserRecipes($userId) {
    $pdo = connectDatabase();
    try {
        $stmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    } catch (\PDOException $e) {
        error_log("Error fetching recipes for user $userId: " . $e->getMessage());
        return [];
    }
}
?>