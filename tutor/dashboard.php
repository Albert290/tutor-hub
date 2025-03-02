<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header('Location: ../login.php');
    exit();
}

// Fetch tutor data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$tutor = $stmt->fetch();
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-container">
    <div class="profile-header">
        <h1>Welcome, <?php echo $tutor['email']; ?></h1>
        <div class="status-badge <?php echo $tutor['is_authorized'] ? 'approved' : 'pending'; ?>">
            <?php echo $tutor['is_authorized'] ? 'Verified Tutor' : 'Pending Approval'; ?>
        </div>
    </div>

    <div class="dashboard-content">
        <!-- Tutor Subjects -->
        <section class="subjects-section">
            <h2>Your Teaching Subjects</h2>
            <div class="subjects-grid">
                <?php
                $stmt = $pdo->prepare("SELECT subject_name FROM tutor_subjects WHERE tutor_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                while ($subject = $stmt->fetch()) {
                    echo '<div class="subject-card">' . $subject['subject_name'] . '</div>';
                }
                ?>
            </div>
        </section>

        <!-- Pending Requests -->
        <section class="requests-section">
            <h2>Student Requests</h2>
            <div class="requests-list">
                <div class="request-card">
                    <span class="student-name">Alice Smith</span>
                    <span class="subject">Physics 201</span>
                    <div class="actions">
                        <button class="btn accept">Accept</button>
                        <button class="btn reject">Reject</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include '../includes/footer.php'; ?>