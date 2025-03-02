
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub Connect | Student Tutor Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include('includes/header.php'); ?>
   <!-- Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <div class="hero-content">
            <h1>Connect, Learn, and Succeed.</h1>
            <p>Find expert tutors in your course units or share your knowledge with peers</p>
            <div class="search-container">
                <input type="text" id="unitSearch" placeholder="Search for a unit...">
                <button class="btn" id="searchBtn"><i class="fas fa-search"></i></button>
            </div>
        </div> 
        <!-- In your hero section -->
<div class="hero-illustration">
    <img src="assets/images/pexels-yankrukov-8199656.jpg" alt="Learning illustration" class="hero-image">
</div>
    </div>
</section>

 <!-- Mission Section -->
    <section class="mission">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-content">
                    <h2>Our Mission</h2>
                    <p>At StudyHub Connect, we believe in the power of peer-to-peer learning. Our platform bridges the gap between academic challenge and student success by connecting those who need help with those who can provide it.</p>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3 data-count="21">0</h3>
                            <p>Active Tutors</p>
                        </div>
                        <div class="stat-card">
                            <h3 data-count="40">0</h3>
                            <p>Success Rate</p>
                        </div>
                        <div class="stat-card">
                            <h3 data-count="52">0</h3>
                            <p>Courses Covered</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- About Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Empowering Student Success Through Peer Learning</h1>
                <p>Connecting knowledge seekers with academic mentors</p>
            </div>
        </div>
    </section>

   

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2>Why Choose StudyHub Connect?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Expert Tutors</h3>
                    <p>Peer-verified tutors with proven subject mastery</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-star"></i>
                    <h3>Rating System</h3>
                    <p>Transparent ratings and reviews for quality assurance</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>Flexible Scheduling</h3>
                    <p>Book sessions at your convenience</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tutor Results Section -->
    <section class="tutor-results" id="search">
        <div class="container">
            <h2>Available Tutors</h2>
            <div class="results-grid" id="tutorResults">
                <!-- Tutor cards will be dynamically inserted here -->
            </div>
        </div>
    </section>

    <!-- Registration Modals -->
    <div class="modal" id="registrationModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Join StudyHub Connect</h2>
            <div class="form-toggle">
                <button class="toggle-btn active" id="studentToggle">Student</button>
                <button class="toggle-btn" id="tutorToggle">Tutor</button>
            </div>
            <form id="registrationForm">
                <div class="form-group">
                    <input type="text" id="fullName" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <input type="email" id="email" placeholder="University Email" required>
                </div>
                <div class="form-group tutor-field">
                    <select id="units" multiple>
                        <option value="unit1">Computer Science 101</option>
                        <option value="unit2">Mathematics for Engineers</option>
                        <option value="unit3">Business Statistics</option>
                    </select>
                </div>
                <button type="submit" class="btn">Create Account</button>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>