<?php
session_start();
include('../config/database.php');
include('../includes/functions.php');

// Check for successful registration message
$registered = isset($_GET['registered']) && $_GET['registered'] == 'success';
$redirect = $_GET['redirect'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (loginUser($email, $password)) {
        
        // Redirect to the requested page if set, otherwise to home
        if (!empty($redirect)) {
            header('Location: ' . $redirect);
        } else {
            header('Location: home.php');
        }
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}

include('../includes/header.php');
?>

<div class="container">
    <div class="login-container fade-in">
        <h2 class="text-center">Welcome Back</h2>
        <p class="text-center mb-2">Login to your account to access your recipes and profile</p>
        
        <?php if ($registered): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                Registration successful! You can now log in with your credentials.
            </div>
        <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo $rootPath; ?>pages/login.php<?php echo !empty($redirect) ? '?redirect=' . urlencode($redirect) : ''; ?>" method="POST">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Your email address" required>
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Your password" required>
                </div>
                <button type="submit" class="btn-block">Login <i class="fas fa-sign-in-alt"></i></button>
            </form>
            
            <div class="form-footer">
                <p>Don't have an account? <a href="<?php echo $rootPath; ?>pages/signup.php">Sign up here</a></p>
            </div>
        </div>
    </div>

<?php include('../includes/footer.php'); ?>