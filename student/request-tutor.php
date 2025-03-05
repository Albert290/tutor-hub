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

// Check if tutor_id is provided
if (!isset($_GET['tutor_id']) || empty($_GET['tutor_id'])) {
    $_SESSION['error'] = "Invalid tutor selection.";
    header('Location: dashboard.php');
    exit();
}

$tutor_id = $_GET['tutor_id'];

try {
    // Verify tutor exists and is authorized
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.email, u.name, 
               GROUP_CONCAT(DISTINCT ts.subject_name SEPARATOR ', ') as subjects
        FROM users u
        JOIN tutor_subjects ts ON u.user_id = ts.tutor_id
        WHERE u.user_id = ? AND u.role = 'tutor' AND u.is_authorized = TRUE
        GROUP BY u.user_id
    ");
    $stmt->execute([$tutor_id]);
    $tutor = $stmt->fetch();

    if (!$tutor) {
        $_SESSION['error'] = "Selected tutor is not available.";
        header('Location: dashboard.php');
        exit();
    }

    // Fetch subjects for dropdown
    $stmt = $pdo->prepare("
        SELECT DISTINCT s.subject_id, s.subject_name
        FROM subjects s
        WHERE s.subject_name IN (
            SELECT subject_name 
            FROM tutor_subjects 
            WHERE tutor_id = ?
        )
    ");
    $stmt->execute([$tutor_id]);
    $tutor_subjects = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again.";
    header('Location: dashboard.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (!isset($_POST['subject_id']) || empty($_POST['subject_id'])) {
        $_SESSION['error'] = "Please select a subject.";
        header('Location: request-tutor.php?tutor_id=' . $tutor_id);
        exit();
    }

    try {
        // Check if request already exists
        $stmt = $pdo->prepare("
            SELECT 1 FROM student_requests 
            WHERE student_id = ? AND tutor_id = ? AND subject_id = ? 
            AND status IN ('pending', 'accepted')
        ");
        $stmt->execute([$_SESSION['user_id'], $tutor_id, $_POST['subject_id']]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "You already have a pending or accepted request for this subject with this tutor.";
            header('Location: dashboard.php');
            exit();
        }

        // Insert new request
        $stmt = $pdo->prepare("
            INSERT INTO student_requests 
            (student_id, tutor_id, subject_id, status) 
            VALUES (?, ?, ?, 'pending')
        ");
        $stmt->execute([
            $_SESSION['user_id'], 
            $tutor_id, 
            $_POST['subject_id']
        ]);

        $_SESSION['success'] = "Tutor request submitted successfully!";
        header('Location: dashboard.php');
        exit();

    } catch (PDOException $e) {
        error_log("Request submission error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to submit request. Please try again.";
        header('Location: request-tutor.php?tutor_id=' . $tutor_id);
        exit();
    }
}

// Include header
include '../includes/header.php';
?>

<!-- Rest of the code remains the same as in the previous artifact -->

<div class="container request-tutor-page">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2>Request Tutor</h2>
                </div>
                <div class="card-body">
                    <?php 
                    // Display any error messages
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">' . 
                             htmlspecialchars($_SESSION['error']) . 
                             '</div>';
                        unset($_SESSION['error']);
                    }
                    ?>

                    <div class="tutor-info mb-4">
                        <h3>Tutor Details</h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($tutor['email']); ?></p>
                        <p><strong>Subjects:</strong> <?php echo htmlspecialchars($tutor['subjects']); ?></p>
                    </div>

                    <form method="POST" action="request-tutor.php?tutor_id=<?php echo $tutor_id; ?>">
                        <div class="form-group">
                            <label for="subject_id">Select Subject</label>
                            <select name="subject_id" id="subject_id" class="form-control" required>
                                <option value="">Choose a subject</option>
                                <?php foreach ($tutor_subjects as $subject): ?>
                                    <option value="<?php echo $subject['subject_id']; ?>">
                                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="additional_notes">Additional Notes (Optional)</label>
                            <textarea 
                                name="additional_notes" 
                                id="additional_notes" 
                                class="form-control" 
                                rows="3" 
                                placeholder="Any specific requirements or preferences"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            Submit Tutor Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>