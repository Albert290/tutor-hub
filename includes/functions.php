<?php
function handle_file_upload($file_input, $target_dir) {
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $allowed_types = ['application/pdf'];
    $max_size = 2 * 1024 * 1024;

    if (!isset($_FILES[$file_input])) {
        return ['error' => 'No file uploaded'];
    }

    $file = $_FILES[$file_input];
    
    // Validate file error code
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error: ' . $file['error']];
    }
    
    // Validate MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime, $allowed_types)) {
        return ['error' => 'Only PDF files allowed'];
    }
    
    if ($file['size'] > $max_size) {
        return ['error' => 'File exceeds 2MB limit'];
    }

    // Generate safe filename
    $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $file['name']);
    $target_path = rtrim($target_dir, '/') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['path' => $target_path];
    }
    return ['error' => 'File move failed'];
}

function enforce_authentication() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: ../login.php');
        exit();
    }
}

function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
}

function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}