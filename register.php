<?php include('includes/header.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/config.php';
    require_once 'includes/functions.php';

    // Common validation
    $role = $_POST['role'];
    $reg_number = clean_input($_POST['reg_number']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = clean_input($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (role, reg_number, email, phone, password, year, semester)
                             VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $year = $_POST['year'];
        $semester = ($role === 'student') ? $_POST['semester'] : null;

        $stmt->execute([$role, $reg_number, $email, $phone, $password, $year, $semester]);
        $user_id = $pdo->lastInsertId();

        if ($role === 'tutor') {
            // Handle transcript upload
            $transcript_path = handle_file_upload('transcript', 'transcripts/');
            
            // Insert tutor application
            $stmt = $pdo->prepare("INSERT INTO tutor_applications (tutor_id, transcript_path)
                                  VALUES (?, ?)");
            $stmt->execute([$user_id, $transcript_path]);

            // Process subjects
            $subjects = array_map('trim', explode(',', $_POST['subjects']));
            foreach ($subjects as $subject) {
                if (!empty($subject)) {
                    $stmt = $pdo->prepare("INSERT INTO tutor_subjects (tutor_id, subject_name)
                                         VALUES (?, ?)");
                    $stmt->execute([$user_id, $subject]);
                }
            }
        }

        // Redirect to login
        header("Location: login.php?registered=1");
        exit();

    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}

function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function handle_file_upload($field, $target_dir) {
    // Add proper file upload handling
    // Return file path
}
?>

<main class="auth-container">
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
    document.querySelectorAll('.role-card, form').forEach(el => {
        el.classList.remove('selected', 'hidden');
        el.classList.add('hidden');
    });
    
    // Show selected role card and appropriate form
    event.currentTarget.classList.add('selected');
    document.getElementById(role + 'Form').classList.remove('hidden');
}
</script>

<?php include('includes/footer.php'); ?>