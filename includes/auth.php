<?php
function handle_signup($pdo) {
    // Validate CSRF token
    if(!verify_csrf_token($_POST['csrf_token'])) {
        set_error('Invalid CSRF token');
        return;
    }

    // Process form data
    $fullname = sanitize_input($_POST['fullname']);
    $email = sanitize_email($_POST['email']);
    // ... other fields
    
    try {
        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO users (...) VALUES (...)");
        $stmt->execute([...]);
        
        // Handle tutor registration
        if($role === 'tutor') {
            handle_tutor_registration($pdo, $user_id);
        }
        
        redirect('login.php');
    } catch(PDOException $e) {
        set_error('Registration failed: ' . $e->getMessage());
    }
}

function handle_tutor_registration($pdo, $user_id) {
    // Handle file upload
    $transcripts = upload_file('transcripts', ['application/pdf']);
    
    // Insert into tutors table
    $stmt = $pdo->prepare("INSERT INTO tutors (...) VALUES (...)");
    $stmt->execute([...]);
}