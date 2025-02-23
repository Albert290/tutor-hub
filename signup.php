<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form processing logic
    $role = $_POST['role'];
    $fullname = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        // Insert into users
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullname, $email, $password, $role]);
        $user_id = $pdo->lastInsertId();

        // Handle student registration
        if ($role === 'student' || $role === 'both') {
            $reg_number = filter_var($_POST['student_reg'], FILTER_SANITIZE_STRING);
            $stmt = $pdo->prepare("INSERT INTO students (student_id, registration_number) VALUES (?, ?)");
            $stmt->execute([$user_id, $reg_number]);
        }

        // Handle tutor registration
        if ($role === 'tutor' || $role === 'both') {
            $transcript_path = handle_file_upload('transcript');
            $stmt = $pdo->prepare("INSERT INTO tutors (tutor_id, courses, transcript_path) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, implode(',', $_POST['courses']), $transcript_path]);
        }

        header("Location: login.php");
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}

require 'includes/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="registration-container">
        <form method="POST" enctype="multipart/form-data">
          <!-- Role Selection -->
<div class="role-selection" id="margin">
    <label class="role-card student">
        <input type="radio" name="role" value="student" required 
               <?= (isset($_POST['role']) && $_POST['role'] == 'student') ? 'checked' : '' ?>>
        <div class="role-content">
            <h3>Student</h3>
            <p>Access learning resources and find tutors</p>
        </div>
    </label>

    <label class="role-card tutor">
        <input type="radio" name="role" value="tutor" required 
               <?= (isset($_POST['role']) && $_POST['role'] == 'tutor') ? 'checked' : '' ?>>
        <div class="role-content">
            <h3>Tutor</h3>
            <p>Teach subjects and earn recognition</p>
        </div>
    </label>
</div>


            <!-- Common Fields -->
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>

            <!-- Student Fields -->
            <div class="student-fields">
                <input type="text" name="student_reg" placeholder="Registration Number">
            </div>

            <!-- Tutor Fields -->
            <div class="tutor-fields">
                <input type="text" name="tutor_reg" placeholder="Tutor Registration Number">
                <input type="file" name="transcript" accept=".pdf" required>
                <select name="courses[]" multiple class="course-select">
                    <?php
                    $courses = $pdo->query("SELECT * FROM units");
                    while ($course = $courses->fetch()):
                    ?>
                        <option value="<?= $course['unit_id'] ?>"><?= $course['unit_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn">Register</button>
        </form>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>