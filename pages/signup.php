<?php
session_start();
include('../config/database.php');
include('../includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validate password match
    if ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } else {
        // Call the function to register the user
        $registrationSuccess = registerUser($username, $email, $password);
        
        if ($registrationSuccess) {
            header('Location: login.php?registered=success');
            exit();
        } else {
            $error = "Registration failed. Email may already be in use.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/fonts/fonts.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Sign Up - Timplado de Platito</title>
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="container">
        <div class="signup-container fade-in">
            <h2 class="text-center">Create an Account</h2>
            <p class="text-center mb-2">Join our community and start sharing your favorite recipes</p>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="signup.php" method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" placeholder="Your email address" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm"><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm your password" required>
                </div>
                
                <div class="form-group">
                    <div class="terms-privacy">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                    </div>
                </div>

                <button type="submit" class="btn-block">Create Account <i class="fas fa-user-plus"></i></button>
            </form>
            
            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Log in here</a></p>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>