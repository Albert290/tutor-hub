<?php
session_start();
require_once '../includes/config.php'; // This initializes $pdo
require_once '../includes/functions.php';
include 'includes/admin-header.php';
?>
<section class="tutors-section">
    <h2>Available Tutors</h2>
    <div class="tutors-container">
        <div class="tutor-card">
            <img src="images/23.jpg" alt="Tutor Image">
            <h3>John Doe</h3>
            <p>Expert in Mathematics</p>
            <button>View Profile</button>
        </div>
        <div class="tutor-card">
            <img src="images/4.jpg" alt="Tutor Image">
            <h3>Jane Smith</h3>
            <p>Physics Specialist</p>
            <button>View Profile</button>
        </div>
        <div class="tutor-card">
            <img src="images/1.jpg" alt="Tutor Image">
            <h3>Mike Johnson</h3>
            <p>Chemistry Guru</p>
            <button>View Profile</button>
        </div>
    </div>
</section>

<script>
    document.querySelectorAll(".tutor-card button").forEach(button => {
    button.addEventListener("click", () => {
        alert("This feature is coming soon!");
    });
});

</script>


<?php require_once 'includes/admin-footer.php'; ?>