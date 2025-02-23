<?php
require 'includes/header.php';

// Verify tutor status
$stmt = $pdo->prepare("SELECT is_approved FROM tutors WHERE tutor_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$tutor = $stmt->fetch();

if (!$tutor['is_approved']) {
    echo "<div class='approval-pending'>Your tutor application is under review</div>";
}
?>

<!-- Tutor Dashboard Content -->