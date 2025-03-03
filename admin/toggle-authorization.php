<?php
session_start();
require_once '../config/db.php';
require_once 'includes/admin-auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tutors.php');
    exit();
}

if (!isset($_POST['tutor_id']) || !isset($_POST['action'])) {
    $_SESSION['error'] = "Invalid request";
    header('Location: tutors.php');
    exit();
}

$tutorId = $_POST['tutor_id'];
$action = $_POST['action'];

// Validate action
if ($action !== 'authorize' && $action !== 'revoke') {
    $_SESSION['error'] = "Invalid action";
    header('Location: tutors.php');
    exit();
}

try {
    // Check if tutor exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND role = 'tutor'");
    $stmt->bind_param("i", $tutorId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Tutor not found");
    }
    $stmt->close();
    
    // Update authorization status
    $isAuthorized = ($action === 'authorize') ? 1 : 0;
    $stmt = $conn->prepare("UPDATE users SET is_authorized = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $isAuthorized, $tutorId);
    $stmt->execute();
    $stmt->close();
    
    // Success message
    if ($action === 'authorize') {
        $_SESSION['success'] = "Tutor has been authorized successfully";
    } else {
        $_SESSION['success'] = "Tutor authorization has been revoked";
    }
    
    header('Location: tutors.php');
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    header('Location: tutors.php');
    exit();
}