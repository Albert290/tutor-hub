<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Initialize variables
$tutors = [];
$requests = [];

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

// Handle tutor request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tutor_id']) && isset($_POST['subject_name'])) {
    try {
        // Check if request already exists
        $checkStmt = $pdo->prepare("
            SELECT request_id FROM student_requests 
            WHERE student_id = ? AND tutor_id = ? AND subject_name = ?
        ");
        $checkStmt->execute([
            $_SESSION['user_id'], 
            $_POST['tutor_id'], 
            $_POST['subject_name']
        ]);
        
        if ($checkStmt->rowCount() == 0) {
            // Create new request
            $insertStmt = $pdo->prepare("
                INSERT INTO student_requests (student_id, tutor_id, subject_name, status) 
                VALUES (?, ?, ?, 'pending')
            ");
            $success = $insertStmt->execute([
                $_SESSION['user_id'], 
                $_POST['tutor_id'], 
                $_POST['subject_name']
            ]);
            
            if ($success) {
                $_SESSION['request_success'] = "Request sent successfully!";
            } else {
                $_SESSION['request_error'] = "Failed to send request.";
            }
        } else {
            $_SESSION['request_error'] = "You have already sent a request to this tutor for this subject.";
        }
    } catch (PDOException $e) {
        $_SESSION['request_error'] = "Error: " . $e->getMessage();
    }
    
    // If it's an AJAX request, return JSON response
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => isset($_SESSION['request_success']),
            'message' => $_SESSION['request_success'] ?? $_SESSION['request_error'] ?? ''
        ]);
        exit;
    }
    
    // Otherwise redirect
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
        // Directly search in tutor_subjects table
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
            sr.subject_name,
            u.email as tutor_email,
            sr.status,
            sr.requested_at,
            sr.updated_at 
        FROM 
            student_requests sr
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
 
?>
<!-- Navigation -->
<nav class="navbar">
        <div class="container">
            <a href="#" class="logo">StudyHub<span>Connect</span></a>
            
            <div class="nav-links" id="navLinks">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#search">Find Tutors</a>
                <div class="auth-buttons">
                    <button class="btn outline" id="logoutBtn">Logout</button>
                </div>
            </div>
            
            <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </nav> 

   

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

<?php 
// Display messages
if (isset($_SESSION['upload_success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['upload_success']) . '</div>';
    unset($_SESSION['upload_success']);
}
if (isset($_SESSION['upload_error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['upload_error']) . '</div>';
    unset($_SESSION['upload_error']);
}
if (isset($_SESSION['request_success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['request_success']) . '</div>';
    unset($_SESSION['request_success']);
}
if (isset($_SESSION['request_error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['request_error']) . '</div>';
    unset($_SESSION['request_error']);
}
?>

<!-- How It Works Section -->
<section class="how-it-works">
    <div class="containerr">
        <h2>How Tutor Hub Works</h2>
        <div class="steps-grid">
            <div class="step">
                <i class="fas fa-user-plus"></i>
                <h3>1. Sign Up</h3>
                <p>Create your free account and set up your profile.</p>
            </div>
            <div class="step">
                <i class="fas fa-search"></i>
                <h3>2. Find a Tutor</h3>
                <p>Search by subject or tutor expertise and send requests.</p>
            </div>
            <div class="step">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>3. Start Learning</h3>
                <p>Schedule a session and begin learning with your tutor.</p>
            </div>
        </div>
    </div>
</section>

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
                            <button 
                                class="btn btn-primary request-tutor-btn" 
                                data-tutor-id="<?php echo $tutor['user_id']; ?>" 
                                data-subject-name="<?php echo htmlspecialchars($tutor['subject_name']); ?>"
                                data-tutor-email="<?php echo htmlspecialchars($tutor['email']); ?>"
                            >
                                Request Tutor
                            </button>
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
        <div class="requests-list" id="requests-container">
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $request): ?>
                    <div class="request-card <?php echo strtolower($request['status']); ?>">
                        <span class="subject"><?php echo htmlspecialchars($request['subject_name']); ?></span>
                        <span class="tutor-name"><?php echo htmlspecialchars($request['tutor_email']); ?></span>
                        <span class="status"><?php echo htmlspecialchars($request['status']); ?></span>
                        <span class="date">
                            Requested: <?php echo date('M j, Y', strtotime($request['requested_at'])); ?>
                        </span>
                        <?php if ($request['updated_at']): ?>
                            <span class="date">
                                Updated: <?php echo date('M j, Y', strtotime($request['updated_at'])); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p id="no-requests-message">No current requests.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<section class="quick-notes">
    <h2>Quick Notes</h2>
    <textarea id="noteInput" placeholder="Type your notes here..."></textarea>
    <div class="button-group">
        <button onclick="saveNote()">Save</button>
        <button onclick="clearNote()">Clear</button>
    </div>
</section>

 

<script>
document.getElementById("noteInput").value = localStorage.getItem("quickNote") || "";

function saveNote() {
    localStorage.setItem("quickNote", document.getElementById("noteInput").value);
    alert("Note saved!");
}

function clearNote() {
    document.getElementById("noteInput").value = "";
    localStorage.removeItem("quickNote");
    alert("Note cleared!");
}
</script>


<script>
function toggleDashboard() {
    document.getElementById("dashboard").classList.toggle("hidden");
}

document.getElementById('profile_pic_upload')?.addEventListener('change', function() {
    this.closest('form').submit();
});

// Add event listeners to all request buttons
document.addEventListener('DOMContentLoaded', function() {
    // Get all request buttons
    const requestButtons = document.querySelectorAll('.request-tutor-btn');
    
    // Add click event listener to each button
    requestButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const tutorId = this.getAttribute('data-tutor-id');
            const subjectName = this.getAttribute('data-subject-name');
            const tutorEmail = this.getAttribute('data-tutor-email');
            
            sendTutorRequest(tutorId, subjectName, tutorEmail);
        });
    });
});



// Function to send tutor request via AJAX
function sendTutorRequest(tutorId, subjectName, tutorEmail) {
    // Create form data
    const formData = new FormData();
    formData.append('tutor_id', tutorId);
    formData.append('subject_name', subjectName);
    
    // Create and configure request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'dashboard.php', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    // Handle response
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    // Show success message
                    showMessage(response.message, 'success');
                    
                    // Add new request to the list
                    addRequestToList(subjectName, tutorEmail);
                } else {
                    // Show error message
                    showMessage(response.message, 'danger');
                }
            } catch (e) {
                showMessage('An error occurred while processing your request.', 'danger');
            }
        } else {
            showMessage('An error occurred while processing your request.', 'danger');
        }
    };
    
    // Handle errors
    xhr.onerror = function() {
        showMessage('An error occurred while sending your request.', 'danger');
    };
    
    // Send request
    xhr.send(formData);
}

// Function to add new request to the list
function addRequestToList(subjectName, tutorEmail) {
    const requestsContainer = document.getElementById('requests-container');
    const noRequestsMessage = document.getElementById('no-requests-message');
    
    // Remove "no requests" message if it exists
    if (noRequestsMessage) {
        noRequestsMessage.remove();
    }
    
    // Create new request card
    const requestCard = document.createElement('div');
    requestCard.className = 'request-card pending';
    
    // Add content to the card
    requestCard.innerHTML = `
        <span class="subject">${subjectName}</span>
        <span class="tutor-name">${tutorEmail}</span>
        <span class="status">pending</span>
        <span class="date">Requested: ${new Date().toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}</span>
    `;
    
    // Add card to the container
    requestsContainer.insertBefore(requestCard, requestsContainer.firstChild);
}

// Function to show messages
function showMessage(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    // Insert after header
    const header = document.querySelector('header');
    header.insertAdjacentElement('afterend', alertDiv);
    
    // Remove message after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Use event delegation
    document.querySelector('.tutor-results').addEventListener('click', function(e) {
        // Check if the clicked element is a request button or its child
        const button = e.target.closest('.request-tutor-btn');
        if (button) {
            const tutorId = button.getAttribute('data-tutor-id');
            const subjectName = button.getAttribute('data-subject-name');
            const tutorEmail = button.getAttribute('data-tutor-email');
            
            sendTutorRequest(tutorId, subjectName, tutorEmail);
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    const requestButtons = document.querySelectorAll('.request-tutor-btn');
    console.log('Found buttons:', requestButtons.length);
    
    // Rest of your code...
}); 

document.getElementById('logoutBtn').addEventListener('click', () => {
    // Confirm logout with the user
    const confirmLogout = confirm("Are you sure you want to log out?");
    
    if (confirmLogout) {
        // Send a request to logout.php
        fetch('logout.php')
            .then(response => {
                if (response.ok) {
                    // Display a success message
                    alert("You have been logged out successfully.");
                    // Redirect to the login page
                    window.location.href = 'login.php';
                } else {
                    // Handle errors
                    alert("Logout failed. Please try again.");
                }
            })
            .catch(error => {
                console.error('Error during logout:', error);
                alert("An error occurred during logout.");
            });
    }
});

document.getElementById('logoutBtn').addEventListener('click', () => {
    const confirmLogout = confirm("Are you sure you want to log out?");
    
    if (confirmLogout) {
        // Show loading spinner
        document.getElementById('loadingSpinner').style.display = 'block';
        
        fetch('logout.php')
            .then(response => response.json())
            .then(data => {
                // Hide loading spinner
                document.getElementById('loadingSpinner').style.display = 'none';
                
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.href = 'login.php';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                // Hide loading spinner
                document.getElementById('loadingSpinner').style.display = 'none';
                console.error('Error during logout:', error);
                alert("An error occurred during logout.");
            });
    }
});

</script>

<?php include '../includes/footer.php'; ?>