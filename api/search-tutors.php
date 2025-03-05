<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['subject'])) {
    echo json_encode([]);
    exit();
}

$subject = trim($_GET['subject']);
$stmt = $pdo->prepare("
    SELECT u.*, 
           AVG(r.rating) as rating,
           COUNT(r.rating_id) as total_reviews,
           GROUP_CONCAT(ts.subject_name SEPARATOR ',') as subjects
    FROM users u
    JOIN tutor_subjects ts ON u.user_id = ts.tutor_id
    LEFT JOIN ratings r ON u.user_id = r.tutor_id
    WHERE u.role = 'tutor'
      AND u.is_authorized = 1
      AND ts.subject_name LIKE ?
    GROUP BY u.user_id
");
$stmt->execute(["%$subject%"]);
$tutors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process subjects into arrays
foreach ($tutors as &$tutor) {
    $tutor['subjects'] = explode(',', $tutor['subjects']);
    $tutor['rating'] = (float) $tutor['rating'];
}

echo json_encode($tutors);