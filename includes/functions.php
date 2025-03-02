<?php
// functions.php

function handle_file_upload($file_input, $target_dir) {
    $allowed_types = ['application/pdf'];
    $max_size = 2 * 1024 * 1024; // 2MB

    if (!isset($_FILES[$file_input])) {
        return ['error' => 'No file uploaded'];
    }

    $file = $_FILES[$file_input];
    
    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload error: ' . $file['error']];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['error' => 'Only PDF files are allowed'];
    }
    
    if ($file['size'] > $max_size) {
        return ['error' => 'File too large (max 2MB)'];
    }

    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $target_path = $target_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['path' => $target_path];
    } else {
        return ['error' => 'Failed to save file'];
    }
}

function validate_session() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: login.php');
        exit();
    }
}

function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>