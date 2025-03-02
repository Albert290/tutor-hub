<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Common validation
        $role = $_POST['role'];
        $reg_number = clean_input($_POST['reg_number']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $phone = clean_input($_POST['phone']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users 
                      (role, reg_number, email, phone, password, year_of_study, semester)
                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $year = $_POST['year'];
        $semester = ($role === 'student') ? $_POST['semester'] : null;

        $stmt->execute([$role, $reg_number, $email, $phone, $password, $year, $semester]);
        $user_id = $pdo->lastInsertId();

        if ($role === 'tutor') {
            $upload_result = handle_file_upload('transcript', 'transcripts/');
            if (isset($upload_result['error'])) {
                throw new Exception($upload_result['error']);
            }

            $stmt = $pdo->prepare("INSERT INTO tutor_applications (tutor_id, transcript_path)
                                VALUES (?, ?)");
            $stmt->execute([$user_id, $upload_result['path']]);

            $subjects = array_map('trim', explode(',', $_POST['subjects']));
            foreach ($subjects as $subject) {
                if (!empty($subject)) {
                    $stmt = $pdo->prepare("INSERT INTO tutor_subjects (tutor_id, subject_name)
                                        VALUES (?, ?)");
                    $stmt->execute([$user_id, $subject]);
                }
            }
        }

        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;
        $_SESSION['email'] = $email;

        $redirect = ($role === 'tutor') ? 'tutor/dashboard.php' : 'student/dashboard.php';
        
        // Make sure the redirect URL is valid
        if (!file_exists($redirect)) {
            throw new Exception("Dashboard file not found: $redirect");
        }
        
        // Debug session data
        $_SESSION['debug_info'] = [
            'redirect_attempted' => true,
            'redirect_url' => $redirect,
            'time' => date('Y-m-d H:i:s')
        ];
        
        // Ensure there are no whitespace or other output before this point
        header("Location: $redirect");
        exit(); // This ensures that no further code is executed after the redirect
    } catch (Exception $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}

// Check for output buffering
if (!ob_get_level()) {
    ob_start();
}

// Include header
include('includes/header.php');
?>

<main class="auth-container">
    <?php if (!empty($error)): ?>
        <div class="error-message" style="color: red; padding: 10px; margin-bottom: 20px; background-color: #ffeeee; border: 1px solid #ffcccc; border-radius: 5px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php
    // Debugging information
    if (isset($_SESSION['debug_info']) && $_SESSION['debug_info']['redirect_attempted']): 
    ?>
        <div style="background-color: #ffffcc; padding: 10px; margin-bottom: 20px; border: 1px solid #e0e0e0; border-radius: 5px;">
            <p><strong>Debug Info:</strong> Redirect was attempted to: <?php echo $_SESSION['debug_info']['redirect_url']; ?> at <?php echo $_SESSION['debug_info']['time']; ?></p>
        </div>
    <?php 
        // Clear debug info after displaying
        unset($_SESSION['debug_info']);
    endif; 
    ?>
    
    <div class="role-selection">
        <h2>Join as...</h2>
        <div class="role-cards">
            <div class="role-card student" onclick="selectRole('student')">
                <h3>Student</h3>
                <p>Find qualified tutors for your subjects</p>
            </div>
            <div class="role-card tutor" onclick="selectRole('tutor')">
                <h3>Tutor</h3>
                <p>Share your knowledge and earn</p>
            </div>
        </div>
    </div>

    <!-- Student Registration Form -->
    <form id="studentForm" class="hidden" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="role" value="student">
        
        <h3>Student Requirements</h3>
        
        <div class="form-group">
            <input type="text" name="reg_number" placeholder="Registration Number" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="form-group">
            <input type="tel" name="phone" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <select name="year" required>
                <option value="">Year of Study</option>
                <option value="1">First Year</option>
                <option value="2">Second Year</option>
                <option value="3">Third Year</option>
                <option value="4">Fourth Year</option>
            </select>
        </div>
        <div class="form-group">
            <select name="semester" required>
                <option value="">Current Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
            </select>
        </div>

        <button type="submit" class="btn">Register as Student</button>
    </form>

    <!-- Tutor Registration Form -->
    <form id="tutorForm" class="hidden" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="role" value="tutor">
        
        <h3>Tutor Requirements</h3>
        
        <div class="form-group">
            <input type="text" name="reg_number" placeholder="Registration Number" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="form-group">
            <input type="tel" name="phone" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <select name="year" required>
                <option value="">Current Year of Study</option>
                <option value="2">Second Year</option>
                <option value="3">Third Year</option>
                <option value="4">Fourth Year</option>
            </select>
        </div>
        <div class="form-group">
            <input type="file" name="transcript" accept=".pdf" required>
            <small>Upload your academic transcript (PDF only)</small>
        </div>
        <div class="form-group">
            <textarea name="subjects" 
                      placeholder="List the units you can teach (separated by commas)
Example: Advanced Mathematics, Physics 101, Organic Chemistry" 
                      required></textarea>
        </div>

        <button type="submit" class="btn">Apply as Tutor</button>
    </form>
</main>

<script>
function selectRole(role) {
    // Hide all forms and reset selections
    document.querySelectorAll('.role-card').forEach(el => {
        el.classList.remove('selected');
    });
    
    document.querySelectorAll('form').forEach(el => {
        el.classList.add('hidden');
    });
    
    // Show selected role card and appropriate form
    document.querySelector('.role-card.' + role).classList.add('selected');
    document.getElementById(role + 'Form').classList.remove('hidden');
}
</script>

<?php include('includes/footer.php'); ?>