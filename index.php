
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
<?php 
include('includes/header.php');
include('includes/config.php');
include('includes/functions.php');

// Process search 
$searchResults = []; 
$searchMessage = ''; // Variable to store search feedback

if(isset($_GET['unitSearch'])) {     
    $searchTerm = '%' . trim($_GET['unitSearch']) . '%';
    
    try {
        // Prepare the SQL statement
        $stmt = $pdo->prepare("SELECT * FROM tutor_subjects WHERE subject_name LIKE :searchTerm");
        
        // Bind the parameter
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch all results
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Set message based on search results
        if (empty($searchResults)) {
            $searchMessage = "No units found matching your search: " . htmlspecialchars($_GET['unitSearch']);
        }
    } catch(PDOException $e) {
        $searchMessage = "Search error: " . $e->getMessage();
    }
} 
?>

<section class="hero" id="home">     
    <div class="container">         
        <div class="hero-content">             
            <h1>Tharaka University Tutors Hub.</h1>             
            <p>Find expert tutors in your course units or share your knowledge with peers</p>             
            
            <form method="GET" class="search-container">                 
                <input type="text" name="unitSearch" id="unitSearch" placeholder="Search for a unit..." required>                 
                <button type="submit" class="btn" id="searchBtn"><i class="fas fa-search"></i></button>             
            </form>                          
            
            <?php if(!empty($searchMessage)): ?>
                <div class="search-message alert alert-info">
                    <?php echo $searchMessage; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($searchResults)): ?>                 
                <div class="search-results">
                    <h3>Search Results:</h3>                    
                    <?php foreach($searchResults as $subject): ?>                         
                        <div class="subject result">                             
                            <?php if(is_logged_in()): ?>                                 
                                <a href="unit.php?id=<?php echo htmlspecialchars($unit['id']); ?>">                                     
                                <?php echo htmlspecialchars($subject['subject_name']); ?>                               
                                </a>                             
                                <?php else: ?>                                 
                                <span class="subject-locked" onclick="openLoginModal()">                                     
                                    <?php echo htmlspecialchars($subject['subject_name']); ?>                                     
                                    <i class="fas fa-lock"></i>                                 
                                </span>                                 
                            <?php endif; ?>                         
                        </div>                     
                    <?php endforeach; ?>                 
                </div>             
            <?php endif; ?>         
        </div>
        <div class="hero-illustration">
           <img src="assets/images/pexels-yankrukov-8199656.jpg" alt="Learning illustration" class="hero-image">
        </div>     
     </div> 
        <!-- In your hero section -->

    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works">
    <div class="container">
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
        <div class="auth-cta">
    <h3>Get Started Today</h3>
    <p>Join our academic community to unlock personalized learning support</p>
    <div class="cta-buttons">
        <button class="btn btn-primary" onclick="window.location.href='register.php'">Create Account</button> 
    </div>
</div>
</section>


 <!-- Mission Section -->
    <section class="mission">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-content">
                    <h2>Our Mission</h2>
                    <p>At Tutor Hub, we believe in the power of peer-to-peer learning. Our platform bridges the gap between academic challenge and student success by connecting those who need help with those who can provide it.</p>
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


  <!-- Success Stories & Testimonials -->
<section class="testimonials">
    <div class="container">
        <h2>What Our Students Say</h2>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <p>"Thanks to Tutor Hub, I finally passed my Calculus exam!"</p>
                <h4>— Jane, 2nd Year Computer Science</h4>
            </div>
            <div class="testimonial-card">
                <p>"Becoming a tutor helped me earn extra cash while helping others."</p>
                <h4>— Mark, 3rd Year Business Studies</h4>
            </div>
        </div>
    </div>
</section>

<section class="faq">
    <div class="containerr">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-item">
            <h3>Is Tutor Hub free?</h3>
            <p>Yes! Students can browse and connect with tutors for free. Some tutors may charge fees for advanced sessions.</p>
        </div>
        <div class="faq-item">
            <h3>Can I become a tutor?</h3>
            <p>Yes! If you have expertise in a subject, you can apply to become a tutor and help others.</p>
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
                    <i class="fas fa-comments"></i>
                    <h3>Direct Communication</h3>
                    <p>Securely message tutors, schedule sessions, and track your learning progress through your personal dashboard</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-handshake"></i>
                    <h3>Personalized Matching</h3>
                    <p>Login to access our smart matching system that connects you with ideal tutors/students based on your courses and needs</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Secure Academic Network</h3>
                    <p>We maintain a verified university community to ensure quality interactions and protect your academic integrity</p>
                </div>
            </div>
        </div>
        
        <div class="auth-cta">
    <h3>Get Started Today</h3>
    <p>Join our academic community to unlock personalized learning support</p>
    <div class="cta-buttons">
        <button class="btn btn-primary" onclick="window.location.href='register.php'">Create Account</button>
        <button class="btn btn-secondary">Learn More</button>
    </div>
</div>
    </section>
    <script>
function openLoginModal() {
    // Assuming you have a function to open login modal
    alert('Please login to access unit details');
    // You might want to replace this with your actual modal opening method
    // For example: document.getElementById('loginModal').style.display = 'block';
}
        function showLoginPrompt() {
    const modal = document.getElementById('loginModal');
    modal.style.display = 'block';
}

// Modify your existing modal close functionality as needed
     </script>

    <script src="assets/js/script.js"></script>
</body>
</html>