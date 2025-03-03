<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check if application ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: applications.php');
    exit();
}

$application_id = intval($_GET['id']);

// Get application details
$stmt = $pdo->prepare("
    SELECT a.*, u.email, u.reg_number, u.phone, u.year_of_study 
    FROM tutor_applications a
    JOIN users u ON a.tutor_id = u.user_id
    WHERE a.application_id = ?
");
$stmt->execute([$application_id]);
$application = $stmt->fetch();

if (!$application) {
    header('Location: applications.php');
    exit();
}

// Get tutor subjects
$stmt = $pdo->prepare("SELECT subject_name FROM tutor_subjects WHERE tutor_id = ?");
$stmt->execute([$application['tutor_id']]);
$subjects = $stmt->fetchAll();

include 'includes/admin-header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Review Tutor Application</h1>
        <div class="navigation-links">
            <a href="applications.php" class="nav-link">← Back to Applications</a>
        </div>
    </div>
    
    <div class="application-review-container">
        <div class="application-status">
            <h2>Application Status</h2>
            <div class="status-info">
                <span class="status-badge large <?php echo $application['status']; ?>">
                    <?php echo ucfirst($application['status']); ?>
                </span>
                
                <?php if ($application['status'] !== 'pending'): ?>
                <div class="status-detail">
                    <span>Reviewed on: <?php echo date('M d, Y H:i', strtotime($application['reviewed_at'])); ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if ($application['status'] === 'pending'): ?>
            <div class="review-actions">
                <form method="post" action="process-application.php" class="approval-form">
                    <input type="hidden" name="application_id" value="<?php echo $application_id; ?>">
                    
                    <div class="form-group">
                        <label for="admin_notes">Notes (Optional)</label>
                        <textarea id="admin_notes" name="admin_notes" rows="3"></textarea>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="submit" name="action" value="approve" class="btn-approve">Approve Application</button>
                        <button type="submit" name="action" value="reject" class="btn-reject">Reject Application</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="application-details-container">
            <section class="details-section">
                <h2>Tutor Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="label">Email:</span>
                        <span class="value"><?php echo htmlspecialchars($application['email']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Reg Number:</span>
                        <span class="value"><?php echo htmlspecialchars($application['reg_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Phone:</span>
                        <span class="value"><?php echo htmlspecialchars($application['phone']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Year of Study:</span>
                        <span class="value"><?php echo htmlspecialchars($application['year_of_study']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Applied On:</span>
                        <span class="value"><?php echo date('M d, Y H:i', strtotime($application['submitted_at'])); ?></span>
                    </div>
                </div>
            </section>
            
            <section class="details-section">
                <h2>Teaching Subjects</h2>
                <div class="subjects-grid">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="subject-card">
                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            
            <section class="details-section">
                <h2>Academic Transcript</h2>
                <div class="transcript-preview">
                    <div class="pdf-container">
                        <iframe src="../<?php echo htmlspecialchars($application['transcript_path']); ?>" width="100%" height="500px"></iframe>
                    </div>
                    <div class="transcript-actions">
                        <a href="../<?php echo htmlspecialchars($application['transcript_path']); ?>" target="_blank" class="btn-secondary">Open in New Tab</a>
                        <a href="../<?php echo htmlspecialchars($application['transcript_path']); ?>" download class="btn-secondary">Download Transcript</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>