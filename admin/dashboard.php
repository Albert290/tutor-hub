<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get pending applications count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM tutor_applications WHERE status = 'pending'");
$stmt->execute();
$pending_applications = $stmt->fetchColumn();

// Get active tutors count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'tutor' AND is_authorized = TRUE");
$stmt->execute();
$active_tutors = $stmt->fetchColumn();

// Get total students count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'student'");
$stmt->execute();
$total_students = $stmt->fetchColumn();

// Get pending requests count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM student_requests WHERE status = 'pending'");
$stmt->execute();
$pending_requests = $stmt->fetchColumn();

include 'includes/admin-header.php';
?>

<div class="admin-dashboard-container">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['admin_name']; ?></p>
    </div>
    
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon pending-icon"></div>
            <div class="stat-data">
                <h2><?php echo $pending_applications; ?></h2>
                <p>Pending Tutor Applications</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon tutor-icon"></div>
            <div class="stat-data">
                <h2><?php echo $active_tutors; ?></h2>
                <p>Active Tutors</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon student-icon"></div>
            <div class="stat-data">
                <h2><?php echo $total_students; ?></h2>
                <p>Registered Students</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon request-icon"></div>
            <div class="stat-data">
                <h2><?php echo $pending_requests; ?></h2>
                <p>Pending Requests</p>
            </div>
        </div>
    </div>
    
    <div class="quick-links">
        <a href="applications.php" class="quick-link-card">
            <h3>Verify Tutors</h3>
            <p>Review and approve new tutor applications</p>
        </a>
        
        <a href="tutors.php" class="quick-link-card">
            <h3>Manage Tutors</h3>
            <p>View and manage active tutors</p>
        </a>
        
        <a href="requests.php" class="quick-link-card">
            <h3>Student Requests</h3>
            <p>Monitor tutoring requests and sessions</p>
        </a>
        
        <a href="reports.php" class="quick-link-card">
            <h3>System Reports</h3>
            <p>View platform statistics and reports</p>
        </a>
    </div>
    
    <div class="recent-activity">
        <h2>Recent Activity</h2>
        <div class="activity-list">
            <?php
            // Get recent applications
            $stmt = $pdo->prepare("
                SELECT a.application_id, u.email, a.submitted_at 
                FROM tutor_applications a
                JOIN users u ON a.tutor_id = u.user_id
                WHERE a.status = 'pending'
                ORDER BY a.submitted_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            $applications = $stmt->fetchAll();
            
            if (count($applications) > 0) {
                foreach ($applications as $app) {
                    echo '<div class="activity-item">';
                    echo '<span class="activity-time">' . date('M d, H:i', strtotime($app['submitted_at'])) . '</span>';
                    echo '<span class="activity-text">New tutor application from ' . htmlspecialchars($app['email']) . '</span>';
                    echo '<a href="review-application.php?id=' . $app['application_id'] . '" class="activity-action">Review</a>';
                    echo '</div>';
                }
            } else {
                echo '<p class="no-activity">No recent activity to display</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>