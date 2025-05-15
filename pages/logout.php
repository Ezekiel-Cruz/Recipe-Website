<?php
// filepath: d:\xampp\htdocs\recipe-website\pages\logout.php
session_start();

// Destroy the session
session_destroy();

// Unset all session variables
$_SESSION = array();

// If cookies are used, delete them
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/');
}

// Redirect to home page
header('Location: ../index.php');
exit();
?>
