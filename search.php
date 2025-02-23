<?php
require 'includes/database.php';
session_start();

// Verify student role
if ($_SESSION['role'] !== 'student') {
    header("Location: dashboard.php");
    exit();
}

$unit = filter_input(INPUT_GET, 'unit', FILTER_SANITIZE_STRING);

// Search query
$stmt = $pdo->prepare("SELECT u.fullname, t.school_reg_number, t.payment_method, 
                        GROUP_CONCAT(units.unit_code SEPARATOR ', ') AS expertise
                      FROM tutor_units tu
                      JOIN tutors t ON tu.tutor_id = t.tutor_id
                      JOIN users u ON t.tutor_id = u.user_id
                      JOIN units ON tu.unit_id = units.unit_id
                      WHERE units.unit_code LIKE ?
                      GROUP BY t.tutor_id");
$stmt->execute(["%$unit%"]);
$tutors = $stmt->fetchAll();
?>
<!-- Display results -->