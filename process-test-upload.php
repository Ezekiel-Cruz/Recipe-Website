<?php
// Set display errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Upload Test Results</h1>";

// Check if a file was uploaded
if (!isset($_FILES['testfile']) || $_FILES['testfile']['error'] == UPLOAD_ERR_NO_FILE) {
    echo "<p style='color:red'>No file was uploaded.</p>";
    echo "<a href='check-upload.php'>Go back</a>";
    exit;
}

// Display upload information
echo "<h2>Upload Information:</h2>";
echo "<pre>";
print_r($_FILES['testfile']);
echo "</pre>";

// Check for upload errors
$errorMessages = [
    UPLOAD_ERR_OK => "No error, upload successful.",
    UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
    UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.",
    UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
    UPLOAD_ERR_NO_FILE => "No file was uploaded.",
    UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
    UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
    UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload."
];

if ($_FILES['testfile']['error'] !== UPLOAD_ERR_OK) {
    echo "<p style='color:red'>Error: " . $errorMessages[$_FILES['testfile']['error']] . "</p>";
    echo "<a href='check-upload.php'>Go back</a>";
    exit;
}

// Define the upload directory
$uploadDir = __DIR__ . '/uploads/recipes/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo "<p style='color:red'>Failed to create upload directory.</p>";
        echo "<a href='check-upload.php'>Go back</a>";
        exit;
    }
}

// Try to upload the file
$filename = 'test_' . time() . '_' . basename($_FILES['testfile']['name']);
$targetPath = $uploadDir . $filename;

$result = move_uploaded_file($_FILES['testfile']['tmp_name'], $targetPath);
if ($result) {
    echo "<p style='color:green'>File uploaded successfully!</p>";
    echo "<p>File saved to: " . $targetPath . "</p>";
} else {
    echo "<p style='color:red'>Failed to save the uploaded file.</p>";
    echo "<p>System PHP error: " . error_get_last()['message'] . "</p>";
}

echo "<a href='check-upload.php'>Go back</a>";
?>
