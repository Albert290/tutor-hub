<?php
require 'includes/database.php';
session_start();

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable if using HTTPS

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

   // After successful login
$stmt = $pdo->prepare("SELECT role FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user['role'] === 'tutor') {
    header("Location: tutor-dashboard.php");
} else {
    header("Location: student-dashboard.php");
}
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!-- Your existing login form -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | StudyHub Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="#" class="logo">StudyHub<span>Connect</span></a>
            
            <div class="nav-links" id="navLinks">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#search">Find Tutors</a>
                <div class="auth-buttons">
                    <a href="signup.html" class="btn">Sign Up</a>
                    <a href="login.html" class="btn outline">Login</a>
                </div>
            </div>
            
            <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </nav>
    
    <div class="auth-container">
        <div class="auth-illustration"></div>
        
        <div class="auth-form">
            <div class="form-header">
                <h1>Welcome Back!</h1>
                <p>Login to continue your learning journey</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-lock"></i>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>

                <button type="submit" class="btn">Login</button>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                    <a href="forgot-password.html">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>

    <script src="auth.js"></script>
</body>
</html>