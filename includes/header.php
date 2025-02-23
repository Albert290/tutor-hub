<?php
session_start();
require 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub Connect | Student Tutor Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
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
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="btn">Dashboard</a>
                        <a href="logout.php" class="btn outline">Logout</a>
                    <?php else: ?>
                        <a href="signup.php" class="btn">Sign Up</a>
                        <a href="login.php" class="btn outline">Login</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </nav>