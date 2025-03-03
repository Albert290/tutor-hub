<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = '';

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = clean_input($_POST['username']);
        $password = $_POST['password'];
        
        // Fetch admin
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } catch (Exception $e) {
        $error = "Login error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Peer Tutoring Platform</title>
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-login-form">
            <div class="admin-logo">
                <h1>Administrator Access</h1>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </div>
</body>
</html>