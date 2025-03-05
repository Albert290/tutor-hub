<?php
session_start();
require_once 'includes/config.php'; 
require_once 'includes/functions.php';

if (!is_logged_in()) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $reg_number = $_POST['reg_number'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE reg_number = ?");
        $stmt->execute([$reg_number]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            
            // Debug: Confirm session
            echo "<pre>Login Success:\n";
            print_r($_SESSION);
            echo "</pre>";
            
            // Redirect
            header("Location: {$user['role']}/dashboard.php");
            exit();
        } else {
            die("Invalid credentials!");
        }
    } catch (PDOException $e) {
        die("Login Error: " . $e->getMessage());
    }
}
// Include header
include('includes/header.php');
?> 

<main class="auth-container">
    <form class="login-form" method="POST">
        <h2>Welcome Back!</h2>
        
        <div class="form-group">
            <input type="text" name="reg_number" placeholder="Registration Number" required>
        </div>
        
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        
        <button type="submit" class="btn">Login</button>
        
        <p class="signup-link">
            Don't have an account? <a href="register.php">Sign up here</a>
        </p>
    </form>
</main>

<?php include('includes/footer.php'); ?>