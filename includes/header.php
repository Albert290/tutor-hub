<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub Connect | Student Tutor Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/Innovation/assets/css/styles.css">
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
                    <button class="btn" id="signUpBtn">Sign Up</button>
                    <button class="btn outline" id="loginBtn">Login</button>
                </div>
            </div>
            
            <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </nav> 

    <script>
        // Add click handlers for the buttons
        document.getElementById('signUpBtn').addEventListener('click', () => {
            window.location.href = 'register.php';
        });
        
        document.getElementById('loginBtn').addEventListener('click', () => {
            window.location.href = 'login.php';
        });
    </script>