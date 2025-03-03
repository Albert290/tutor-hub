<?php
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Check for flash messages
$successMsg = '';
$errorMsg = '';

if (isset($_SESSION['success'])) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $errorMsg = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tutoring Platform</title>
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Flash Messages -->
    <?php if (!empty($successMsg)): ?>
        <div class="flash-message success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $successMsg; ?></span>
            <button class="close-btn"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errorMsg)): ?>
        <div class="flash-message error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $errorMsg; ?></span>
            <button class="close-btn"><i class="fas fa-times"></i></button>
        </div>
    <?php endif; ?>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h1>Admin Panel</h1>
        </div>
        
        <div class="admin-profile">
            <div class="profile-info">
                <span class="admin-name"><?php echo $_SESSION['admin_name']; ?></span>
                <span class="admin-role">Administrator</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="applications.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Tutor Applications</span>
                    </a>
                </li>
                <li>
                    <a href="tutors.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'tutors.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Manage Tutors</span>
                    </a>
                </li>
                <li>
                    <a href="requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'requests.php' ? 'active' : ''; ?>">
                        <i class="fas fa-hand-paper"></i>
                        <span>Tutoring Requests</span>
                    </a>
                </li>
                <li>
                    <a href="students.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-graduate"></i>
                        <span>Manage Students</span>
                    </a>
                </li>
                <li>
                    <a href="subjects.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'subjects.php' ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i>
                        <span>Manage Subjects</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="admin-content">
        <!-- Top Navigation -->
        <header class="admin-header">
            <div class="header-left">
                <button id="sidebar-toggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="header-right">
                <div class="admin-dropdown">
                    <button class="dropdown-btn">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo $_SESSION['admin_name']; ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="change-password.php">
                            <i class="fas fa-key"></i> Change Password
                        </a>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header> 
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    
    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(e.target) && 
            !e.target.closest('.sidebar-toggle')) {
            sidebar.classList.remove('active');
        }
    });
});
</script>