<?php
// Simple test script to verify image upload functionality
session_start();

// Set some values for testing
$_SESSION['user_id'] = 1; 

// Constants
$uploadDir = __DIR__ . '/uploads/recipes/';

echo "<h1>Image Upload Test</h1>";

// Check directory
echo "<h2>Directory Check</h2>";
echo "Upload directory: $uploadDir<br>";

if (!file_exists($uploadDir)) {
    echo "<span style='color:red'>Directory doesn't exist!</span><br>";
    
    // Try to create it
    if (mkdir($uploadDir, 0777, true)) {
        echo "<span style='color:green'>Created directory successfully</span><br>";
    } else {
        echo "<span style='color:red'>Failed to create directory: " . error_get_last()['message'] . "</span><br>";
    }
} else {
    echo "<span style='color:green'>Directory exists</span><br>";
}

// Check permissions
if (is_writable($uploadDir)) {
    echo "<span style='color:green'>Directory is writable</span><br>";
} else {
    echo "<span style='color:red'>Directory is NOT writable!</span><br>";
    
    // Try to set permissions
    chmod($uploadDir, 0777);
    echo "After chmod, writable: " . (is_writable($uploadDir) ? "Yes" : "No") . "<br>";
}

// Create a test file
$testFile = $uploadDir . 'test_' . time() . '.txt';
if (file_put_contents($testFile, 'Test content')) {
    echo "<span style='color:green'>Successfully created a test file</span><br>";
    unlink($testFile); // Clean up
} else {
    echo "<span style='color:red'>Failed to create test file: " . error_get_last()['message'] . "</span><br>";
}

// Image upload form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    echo "<h2>Upload Results</h2>";
    echo "<pre>";
    print_r($_FILES['test_image']);
    echo "</pre>";
    
    if ($_FILES['test_image']['error'] === 0) {
        $fileExtension = strtolower(pathinfo($_FILES['test_image']['name'], PATHINFO_EXTENSION));
        $newFilename = 'test_' . uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $newFilename;
        
        if (move_uploaded_file($_FILES['test_image']['tmp_name'], $targetPath)) {
            echo "<span style='color:green'>File uploaded successfully to: $targetPath</span><br>";
            echo "<p><img src='uploads/recipes/$newFilename' style='max-width: 300px;'></p>";
        } else {
            echo "<span style='color:red'>Move upload failed: " . error_get_last()['message'] . "</span><br>";
        }
    } else {
        echo "<span style='color:red'>Upload error: " . $_FILES['test_image']['error'] . "</span><br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        form { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        input[type="file"] { margin: 10px 0; }
        button { background: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>
    <h2>Test Upload Form</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="test_image" accept="image/*">
        <button type="submit">Upload Test Image</button>
    </form>
    
    <h2>PHP Info</h2>
    <p>PHP Version: <?php echo phpversion(); ?></p>
    <p>upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?></p>
    <p>post_max_size: <?php echo ini_get('post_max_size'); ?></p>
    <p>max_file_uploads: <?php echo ini_get('max_file_uploads'); ?></p>
    <p>file_uploads: <?php echo ini_get('file_uploads'); ?></p>
    <p>upload_tmp_dir: <?php echo ini_get('upload_tmp_dir'); ?></p>
</body>
</html>
