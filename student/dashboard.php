<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch();
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-container">
    <div class="profile-header">
        <h1>Welcome, <?php echo $student['email']; ?></h1>
        <div class="profile-pic">
            <img src="<?php echo $student['profile_pic'] ?? '../assets/images/default-profile.png'; ?>" alt="Profile">
        </div>
    </div>

    <div class="dashboard-content">
        <!-- Search Tutors Section -->
        <section class="search-section">
            <h2>Find Tutors</h2>
            <form class="search-form">
                <input type="text" placeholder="Search by subject..." name="subject">
                <button type="submit" class="btn">Search</button>
            </form>
            
            <div class="tutor-results">
                <!-- Tutors will be loaded here via AJAX -->
                <div class="tutor-card placeholder">
                    <div class="tutor-photo shimmer"></div>
                    <div class="tutor-info">
                        <div class="shimmer" style="width: 60%"></div>
                        <div class="shimmer" style="width: 80%"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Current Requests -->
        <section class="requests-section">
            <h2>Your Requests</h2>
            <div class="requests-list">
                <div class="request-card pending">
                    <span class="subject">Mathematics 101</span>
                    <span class="tutor-name">John Doe</span>
                    <span class="status">Pending</span>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include '../includes/footer.php'; ?>