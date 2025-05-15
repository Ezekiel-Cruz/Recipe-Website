<?php
// Display PHP file upload configuration
echo "<h1>PHP File Upload Configuration</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_file_uploads</td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "</table>";

// Check upload directory permissions
$uploadDir = __DIR__ . '/uploads/recipes/';
echo "<h2>Upload Directory Check</h2>";
echo "<p>Upload directory path: " . $uploadDir . "</p>";

if (!file_exists($uploadDir)) {
    echo "<p style='color:red'>Upload directory does not exist. Attempting to create it...</p>";
    if (mkdir($uploadDir, 0777, true)) {
        echo "<p style='color:green'>Created upload directory successfully.</p>";
    } else {
        echo "<p style='color:red'>Failed to create upload directory.</p>";
    }
} else {
    echo "<p style='color:green'>Upload directory exists.</p>";
}

if (is_writable($uploadDir)) {
    echo "<p style='color:green'>Upload directory is writable.</p>";
} else {
    echo "<p style='color:red'>Upload directory is not writable. Attempting to set permissions...</p>";
    if (chmod($uploadDir, 0777)) {
        echo "<p style='color:green'>Permissions set successfully.</p>";
    } else {
        echo "<p style='color:red'>Failed to set permissions.</p>";
    }
}

// Display form for testing file uploads
echo "<h2>Test File Upload</h2>";
echo "<form action='process-test-upload.php' method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='testfile'><br><br>";
echo "<input type='submit' value='Test Upload'>";
echo "</form>";
?>
