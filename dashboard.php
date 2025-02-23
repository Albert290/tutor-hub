<?php
require 'includes/database.php';
session_start();

// Redirect unauthorized users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
if ($_SESSION['role'] === 'tutor') {
    $stmt = $pdo->prepare("SELECT u.*, t.* FROM users u JOIN tutors t ON u.user_id = t.tutor_id WHERE u.user_id = ?");
} else {
    $stmt = $pdo->prepare("SELECT u.*, s.* FROM users u JOIN students s ON u.user_id = s.student_id WHERE u.user_id = ?");
}

$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- Your CSS links -->
</head>
<body>
    <?php if ($_SESSION['role'] === 'tutor'): ?>
        <!-- Tutor Dashboard -->
        <h2>Your Expertise</h2>
        <ul>
            <?php 
            $units_stmt = $pdo->prepare("SELECT u.unit_code, u.unit_name 
                                       FROM tutor_units tu
                                       JOIN units u ON tu.unit_id = u.unit_id
                                       WHERE tu.tutor_id = ?");
            $units_stmt->execute([$_SESSION['user_id']]);
            while ($unit = $units_stmt->fetch()): ?>
                <li><?= htmlspecialchars($unit['unit_code']) ?> - <?= htmlspecialchars($unit['unit_name']) ?></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <!-- Student Dashboard -->
        <h2>Find a Tutor</h2>
        <form action="search.php" method="GET">
            <input type="text" name="unit" placeholder="Search by unit code">
            <button type="submit">Search</button>
        </form>
    <?php endif; ?>
</body>
</html>