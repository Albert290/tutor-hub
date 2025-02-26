<?php
require 'includes/header.php';
$page_title = 'Home';
?>
  <!-- Hero Section -->
  <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1>Tharaka University Tutor Hub</h1>
                <p>Find expert tutors in your course units or share your knowledge with peers</p>
                <form method="GET" action="search.php" class="search-container">
                    <input type="text" id="unitSearch" placeholder="Search for a unit..." 
                           value="<?= htmlspecialchars($_GET['unit'] ?? '') ?>">
                    <button type="submit" class="btn" id="searchBtn"><i class="fas fa-search"></i></button>
                </form>
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
            <!-- In your hero section -->
            <div class="hero-illustration">
                <img src="images/hero.jpg" alt="Learning illustration" class="hero-image">
            </div>
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
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <i class="fas fa-money-bill-wave"></i> <!-- Cheap icon -->
                    <h3>Free of charge</h3>
                    <p>Learn without having to pay</p>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Tutor Results Section -->
    <section class="tutor-results" id="search">
        <div class="container">
            <h2>Available Tutors</h2>
            <div class="results-grid">
                <?php
                if(isset($_GET['unit'])) {
                    $unit = '%' . $_GET['unit'] . '%';
                    $stmt = $pdo->prepare("SELECT u.fullname, t.*, GROUP_CONCAT(units.unit_code SEPARATOR ', ') AS expertise 
                                         FROM tutors t
                                         JOIN users u ON t.tutor_id = u.user_id
                                         JOIN tutor_units tu ON t.tutor_id = tu.tutor_id
                                         JOIN units ON tu.unit_id = units.unit_id
                                         WHERE units.unit_code LIKE ?
                                         GROUP BY t.tutor_id");
                    $stmt->execute([$unit]);
                    
                    while($tutor = $stmt->fetch()) {
                ?>
                <div class="tutor-card">
                    <h3><?= htmlspecialchars($tutor['fullname']) ?></h3>
                    <p class="expertise"><?= htmlspecialchars($tutor['expertise']) ?></p>
                    <div class="rating">
                        <?php
                        $avg_rating = $pdo->query("SELECT AVG(rating_value) FROM ratings WHERE tutor_id = {$tutor['tutor_id']}")->fetchColumn();
                        echo str_repeat('★', round($avg_rating));
                        ?>
                    </div>
                    <a href="tutor_profile.php?id=<?= $tutor['tutor_id'] ?>" class="btn">View Profile</a>
                </div>
                <?php
                    } // Close while loop
                } // Close if statement
                ?>
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

  <!-- FAQ Section -->
<section class="faq" id="faq">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-grid">
            <!-- FAQ Item 1 -->
            <div class="faq-item">
                <div class="faq-question">
                    <h3>How do I become a tutor?</h3>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>To become a tutor, register with your academic credentials and select the units you're proficient in. Our team will verify your qualifications within 48 hours.</p>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="faq-item">
                <div class="faq-question">
                    <h3>What subjects are available?</h3>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>We cover all major university subjects including Computer Science, Engineering, Mathematics, Business, and more. Check our course catalog for full details.</p>
                </div>
            </div>

            <!-- Add more FAQ items following the same structure -->
        </div>
    </div>
</section>

    <script src="assets/js/script.js"></script>
</body>
</html>