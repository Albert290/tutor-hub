<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Process form submission or direct action link
$application_id = isset($_POST['application_id']) ? $_POST['application_id'] : (isset($_GET['id']) ? $_GET['id'] : 0);
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$admin_notes = isset($_POST['admin_notes']) ? clean_input($_POST['admin_notes']) : '';

if (!$application_id || !in_array($action, ['approve', 'reject'])) {
    $_SESSION['error_message'] = "Invalid request";
    header('Location: applications.php');
    exit();
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Update application status
    $stmt = $pdo->prepare("
        UPDATE tutor_applications 
        SET status = ?, reviewed_at = NOW() 
        WHERE application_id = ?
    ");
    $stmt->execute([$action === 'approve' ? 'approved' : 'rejected', $application_id]);
    
    // Get tutor ID from application
    $stmt = $pdo->prepare("SELECT tutor_id FROM tutor_applications WHERE application_id = ?");
    $stmt->execute([$application_id]);
    $tutor_id = $stmt->fetchColumn();
    
    if (!$tutor_id) {
        throw new Exception("Application not found");
    }
    
    // Update user authorization status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET is_authorized = ? 
        WHERE user_id = ?
    ");
    $stmt->execute([$action === 'approve', $tutor_id]);
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['success_message'] = "Tutor application has been " . ($action === 'approve' ? 'approved' : 'rejected');
} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    $_SESSION['error_message'] = "Error processing application: " . $e->getMessage();
}

header('Location: applications.php');
exit();