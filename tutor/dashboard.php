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
       


    </div>
    </section>
</div>

 

<section class="dashboard-card earnings-performance">
    <h2><i class="fas fa-chart-line"></i> Your Performance</h2>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">------</div>
            <div class="stat-label">Total Earnings</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">------</div>
            <div class="stat-label">Completion Rate</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">------</div>
            <div class="stat-label">Active Students</div>
        </div>
    </div>
</section>

 

<section class="school-announcements">
    <h2><i class="fas fa-bullhorn"></i> School Updates</h2>
    <div class="announcement-list">
        <div class="announcement-card">
            <div class="announcement-date">
                March 20 2025
            </div>
            <h3>Upcoming Exam Schedule</h3>
            <p>The final exam schedule has been released. Please check the school portal for details.</p>
        </div>
        <div class="announcement-card">
            <div class="announcement-date">
                April 15 2025
            </div>
            <h3>Tutor Training Workshop</h3>
            <p>A mandatory teacher training workshop will be held on April 15 in the cafeteria</p>
        </div>
        <div class="announcement-card">
            <div class="announcement-date">
                April 17 2025
            </div>
            <h3>School Holiday Reminder</h3>
            <p>Reminder: School will be closed for the long holidays.</p>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-subject');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const subjectId = this.getAttribute('data-subject-id');
                console.log('Edit subject with ID:', subjectId);
                alert("Edit subject with ID: " + subjectId);
            });
        });

        const addButton = document.querySelector('.add-subject');
        addButton.addEventListener('click', function() {
            console.log('Add subject clicked');
            alert("add subject clicked");
        });
    });
</script>
<?php include '../includes/footer.php'; ?>