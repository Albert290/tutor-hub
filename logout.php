<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Display a logout message and redirect after a delay
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .logout-container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logout-container h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .logout-container p {
            color: #666;
            margin-bottom: 2rem;
        }
    </style>
    <script>
        // Redirect to the login page after 3 seconds
        setTimeout(function() {
            window.location.href = "login.php";
        }, 3000); // 3000 milliseconds = 3 seconds
    </script>
</head>
<body>
    <div class="logout-container">
        <h1>Logout Successful</h1>
        <p>You have been logged out. Redirecting to the login page...</p>
    </div>
</body>
</html>