<?php include('includes/header.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/config.php';
    
    if (isset($_POST['role'])) { // Registration processing
        // Validate and sanitize inputs
        $role = $_POST['role'];
        $reg_number = filter_var($_POST['reg_number'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Insert into users table
        $stmt = $pdo->prepare("INSERT INTO users (role, reg_number, email, phone, password, year, semester) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // Handle tutor-specific fields
        if ($role === 'tutor') {
            // Handle file upload
            $transcript = uploadTranscript($_FILES['transcript']);
            
            // Insert tutor subjects
            $subjects = $_POST['subjects'];
        }
        
        // Redirect to appropriate dashboard
        header("Location: $role/dashboard.php");
        exit();
    }
    else { // Login processing
        // Validate credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE reg_number = ?");
        $stmt->execute([$_POST['reg_number']]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($_POST['password'], $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            header("Location: {$user['role']}/dashboard.php");
            exit();
        }
    }
}

function uploadTranscript($file) {
    // Add file upload validation and handling
}
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