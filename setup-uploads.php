<?php
/**
 * Upload Directory Setup Script
 * This script ensures the uploads directory exists and has proper permissions
 */

// Define the upload directory path
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'recipes' . DIRECTORY_SEPARATOR;

// Check if the directory exists, if not create it
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die("Failed to create upload directory: " . $uploadDir);
    }
}

// Ensure the directory has proper permissions
@chmod($uploadDir, 0777);

// Check if the directory is writable
if (!is_writable($uploadDir)) {
    die("Upload directory is not writable: " . $uploadDir);
}

echo "Upload directory is properly configured: " . $uploadDir;
