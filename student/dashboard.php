<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.php');
    exit();
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $uploadDir = '../uploads/profile_pics/';
    
    // Ensure upload directory exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
    $uploadPath = $uploadDir . $fileName;
    
    // Validate file type and size
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    if (in_array($_FILES['profile_pic']['type'], $allowedTypes) && 
        $_FILES['profile_pic']['size'] <= $maxFileSize) {
        
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadPath)) {
            // Update profile picture in database
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
            $stmt->execute([$uploadPath, $_SESSION['user_id']]);
            
            $_SESSION['upload_success'] = "Profile picture uploaded successfully!";
        } else {
            $_SESSION['upload_error'] = "Failed to upload profile picture.";
        }
    } else {
        $_SESSION['upload_error'] = "Invalid file type or size. Max 5MB, allowed types: JPEG, PNG, GIF.";
    }
    
    // Redirect to prevent form resubmission
    header('Location: dashboard.php');
    exit();
}

// Fetch student data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch();

// Handle unit search
$tutors = [];
$searchError = '';
if (isset($_GET['subject']) && !empty($_GET['subject'])) {
    $subject = trim($_GET['subject']);
    
    try {
        // Search for tutors teaching the specific subject
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id, 
                u.email, 
                ts.subject_name,
                AVG(r.rating) as avg_rating,
                COUNT(r.rating) as rating_count
            FROM 
                users u
            JOIN 
                tutor_subjects ts ON u.user_id = ts.tutor_id
            LEFT JOIN 
                ratings r ON u.user_id = r.tutor_id
            WHERE 
                u.role = 'tutor' 
                AND ts.subject_name LIKE ?
            GROUP BY 
                u.user_id, u.email, ts.subject_name
        ");
        $stmt->execute(["%$subject%"]);
        $tutors = $stmt->fetchAll();
        
        if (empty($tutors)) {
            $searchError = "No tutors found for subject: " . htmlspecialchars($subject);
        }
    } catch (PDOException $e) {
        $searchError = "Error searching for tutors: " . $e->getMessage();
    }
}

// Fetch student's current requests
try {
    $stmt = $pdo->prepare("
        SELECT 
            sr.request_id, 
            sr.subject_id,
            s.subject_name,
            u.name as tutor_name, 
            sr.status 
        FROM 
            student_requests sr
        JOIN 
            subjects s ON sr.subject_id = s.subject_id
        JOIN 
            users u ON sr.tutor_id = u.user_id
        WHERE 
            sr.student_id = ?
        ORDER BY 
            sr.requested_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    $requests = [];
    error_log("Error fetching requests: " . $e->getMessage());
}

// Include header
include '../includes/header.php';
?>

<!-- Rest of the code remains the same as in the previous artifact -->


<!-- Toggle Button -->
<button class="dashboard-toggle" onclick="toggleDashboard()">View Profile</button>

<!-- Dashboard (Initially Hidden) -->
<div class="dashboard-container hidden" id="dashboard">
<div class="profile-header">
    <h1>Welcome, <?php echo $student['email']; ?></h1>
    <div class="profile-pic">
        <img src="<?php echo $student['profile_pic'] ?? '../assets/images/default-profile.png'; ?>" alt="Profile">
        <a href="view-profile.php" class="btn btn-primary">View Profile</a>
    </div>
</div>

        <form action="dashboard.php" method="POST" enctype="multipart/form-data" class="profile-pic-upload">
            <input 
                type="file" 
                name="profile_pic" 
                id="profile_pic_upload" 
                accept="image/jpeg,image/png,image/gif"
                style="display:none;"
            >
            <button 
                type="button" 
                onclick="document.getElementById('profile_pic_upload').click();" 
                class="btn btn-secondary"
            >
                Change Picture
            </button>
        </form>
    </div>
</div>

        <?php 
        // Display upload messages
        if (isset($_SESSION['upload_success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['upload_success']) . '</div>';
            unset($_SESSION['upload_success']);
        }
        if (isset($_SESSION['upload_error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['upload_error']) . '</div>';
            unset($_SESSION['upload_error']);
        }
        ?>
    </div>

    <div class="dashboard-content">
        <!-- Search Tutors Section -->
        <section class="search-section">
            <h2>Find Tutors</h2>
            <form class="search-form" method="GET" action="dashboard.php">
                <input 
                    type="text" 
                    placeholder="Search by subject..." 
                    name="subject" 
                    value="<?php echo htmlspecialchars($_GET['subject'] ?? ''); ?>"
                >
                <button type="submit" class="btn">Search</button>
            </form>
            
            <div class="tutor-results">
                <?php if (!empty($tutors)): ?>
                    <?php foreach ($tutors as $tutor): ?>
                        <div class="tutor-card">
                            <div class="tutor-info">
                                <h3><?php echo htmlspecialchars($tutor['email']); ?></h3>
                                <p>Subject: <?php echo htmlspecialchars($tutor['subject_name']); ?></p>
                                <div class="rating">
                                    Rating: 
                                    <?php 
                                    $avgRating = round($tutor['avg_rating'] ?? 0, 1);
                                    echo $avgRating . '/5 ';
                                    echo "({$tutor['rating_count']} ratings)";
                                    ?>
                                </div>
                                <a href="request-tutor.php?tutor_id=<?php echo $tutor['user_id']; ?>" class="btn btn-primary">
                                    Request Tutor
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (!empty($_GET['subject'])): ?>
                    <div class="no-results">
                        <?php echo htmlspecialchars($searchError); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Current Requests Section -->
        <section class="requests-section">
            <h2>Your Requests</h2>
            <div class="requests-list">
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $request): ?>
                        <div class="request-card <?php echo strtolower($request['status']); ?>">
                            <span class="subject"><?php echo htmlspecialchars($request['subject_name']); ?></span>
                            <span class="tutor-name"><?php echo htmlspecialchars($request['tutor_name']); ?></span>
                            <span class="status"><?php echo htmlspecialchars($request['status']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No current requests.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script>

function toggleDashboard() {
    document.getElementById("dashboard").classList.toggle("hidden");

document.getElementById('profile_pic_upload')?.addEventListener('change', function() {
    this.closest('form').submit();
});
</script>

<?php include '../includes/footer.php'; ?>