// Toggle Password Visibility
function togglePassword() {
    const passwordField = event.target.closest('.toggle-password').previousElementSibling;
    const eyeIcon = event.target.closest('.toggle-password').querySelector('i');
    
    if (passwordField.type === "password") {
        passwordField.type = "text";
        eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordField.type = "password";
        eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Form Validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Add validation logic
});

document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Add validation logic
});

// Real-time Validation
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', () => {
        if (input.checkValidity()) {
            input.style.borderColor = '#4CAF50';
        } else {
            input.style.borderColor = '#ff4444';
        }
    });
});

// Password Strength Checker (for signup)
const passwordInput = document.getElementById('password');
if(passwordInput) {
    passwordInput.addEventListener('input', function() {
        const strengthBadge = document.createElement('div');
        strengthBadge.className = 'password-strength';
        // Add password strength logic
    });
}

// Role Selection Handling
document.addEventListener('DOMContentLoaded', () => {
    // Get all role radio buttons
    const roleRadios = document.querySelectorAll('input[name="role"]');
    
    // Get role-specific sections
    const studentSection = document.querySelector('.student-fields');
    const tutorSection = document.querySelector('.tutor-fields');

    // Function to toggle sections
    function toggleRoleSections() {
        if (document.getElementById('studentRole').checked) {
            studentSection.classList.add('active');
            tutorSection.classList.remove('active');
        } else {
            tutorSection.classList.add('active');
            studentSection.classList.remove('active');
        }
    }

    // Add event listeners to all radio buttons
    roleRadios.forEach(radio => {
        radio.addEventListener('change', toggleRoleSections);
    });

    // Initialize with student section visible
    toggleRoleSections();
});

// File Upload Display
document.getElementById('transcripts').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'No file chosen';
    document.querySelector('.file-name').textContent = fileName;
});

// Tag Input System
const tagInput = document.getElementById('courses');
const tagContainer = document.querySelector('.tag-container');

tagInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const value = this.value.trim();
        if (value) {
            createTag(value);
            this.value = '';
        }
    }
});

function createTag(text) {
    const tag = document.createElement('div');
    tag.className = 'tag';
    tag.innerHTML = `
        ${text}
        <span onclick="this.parentElement.remove()">&times;</span>
    `;
    tagContainer.appendChild(tag);
}

// Initialize student fields as active
document.querySelector('.student-fields').classList.add('active');