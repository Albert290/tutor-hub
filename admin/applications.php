<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get applications with filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';
$allowed_statuses = ['pending', 'approved', 'rejected', 'all'];
if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = 'pending';
}

$query = "
    SELECT a.*, u.email, u.reg_number, u.year_of_study 
    FROM tutor_applications a
    JOIN users u ON a.tutor_id = u.user_id
";

if ($status_filter !== 'all') {
    $query .= " WHERE a.status = ?";
    $params = [$status_filter];
} else {
    $params = [];
}

$query .= " ORDER BY a.submitted_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$applications = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Tutor Applications</h1>
        <div class="filter-controls">
            <a href="?status=pending" class="filter-btn <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="?status=approved" class="filter-btn <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">Approved</a>
            <a href="?status=rejected" class="filter-btn <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">Rejected</a>
            <a href="?status=all" class="filter-btn <?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
        </div>
    </div>
    
    <div class="applications-container">
        <?php if (count($applications) > 0): ?>
            <?php foreach ($applications as $app): ?>
                <div class="application-card">
                    <div class="application-header">
                        <h3><?php echo htmlspecialchars($app['email']); ?></h3>
                        <span class="status-badge <?php echo $app['status']; ?>">
                            <?php echo ucfirst($app['status']); ?>
                        </span>
                    </div>
                    
                    <div class="application-details">
                        <div class="detail-item">
                            <span class="label">Reg Number:</span>
                            <span class="value"><?php echo htmlspecialchars($app['reg_number']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Year of Study:</span>
                            <span class="value"><?php echo htmlspecialchars($app['year_of_study']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Applied On:</span>
                            <span class="value"><?php echo date('M d, Y H:i', strtotime($app['submitted_at'])); ?></span>
                        </div>
                        
                        <?php if ($app['status'] !== 'pending'): ?>
                        <div class="detail-item">
                            <span class="label">Reviewed On:</span>
                            <span class="value"><?php echo date('M d, Y H:i', strtotime($app['reviewed_at'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="application-subjects">
                        <h4>Subjects</h4>
                        <div class="subjects-list">
                            <?php
                            $stmt = $pdo->prepare("SELECT subject_name FROM tutor_subjects WHERE tutor_id = ?");
                            $stmt->execute([$app['tutor_id']]);
                            $subjects = $stmt->fetchAll();
                            
                            foreach ($subjects as $subject) {
                                echo '<span class="subject-tag">' . htmlspecialchars($subject['subject_name']) . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="application-actions">
                        <a href="review-application.php?id=<?php echo $app['application_id']; ?>" class="btn-secondary">
                            <?php echo $app['status'] === 'pending' ? 'Review' : 'View Details'; ?>
                        </a>
                        
                        <?php if ($app['status'] === 'pending'): ?>
                        <div class="action-buttons">
                            <a href="process-application.php?id=<?php echo $app['application_id']; ?>&action=approve" class="btn-approve">Approve</a>
                            <a href="process-application.php?id=<?php echo $app['application_id']; ?>&action=reject" class="btn-reject">Reject</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">
                <p>No <?php echo $status_filter; ?> applications found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>