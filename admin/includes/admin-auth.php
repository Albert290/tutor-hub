<?php
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Store the requested URL to redirect back after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login
    header('Location: login.php');
    exit();
}