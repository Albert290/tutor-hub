
// Hamburger Menu Toggle
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navLinks.classList.toggle('active');
});

// Close menu when clicking outside on mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        if (!e.target.closest('.navbar .container')) {
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
        }
    }
});

// Close menu when clicking a link
document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
        }
    });
});

// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    // Modal Handling
    const modal = document.getElementById('registrationModal');
    const signUpBtn = document.getElementById('signUpBtn');
    const closeBtn = document.querySelector('.close');
    
    signUpBtn.addEventListener('click', () => {
        modal.style.display = 'block';
    });

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Form Toggle
    const tutorToggle = document.getElementById('tutorToggle');
    const studentToggle = document.getElementById('studentToggle');
    const tutorFields = document.querySelectorAll('.tutor-field');

    tutorToggle.addEventListener('click', () => {
        tutorFields.forEach(field => field.style.display = 'block');
        studentToggle.classList.remove('active');
        tutorToggle.classList.add('active');
    });

    studentToggle.addEventListener('click', () => {
        tutorFields.forEach(field => field.style.display = 'none');
        tutorToggle.classList.remove('active');
        studentToggle.classList.add('active');
    });


// Search input animation
const searchInput = document.getElementById('unitSearch');

searchInput.addEventListener('focus', () => {
    searchInput.parentElement.style.transform = 'scale(1.02)';
});

searchInput.addEventListener('blur', () => {
    searchInput.parentElement.style.transform = 'scale(1)';
});

    // Search Functionality
    document.getElementById('searchBtn').addEventListener('click', () => {
        const searchTerm = document.getElementById('unitSearch').value;
        const tutors = [
            { name: 'John Doe', unit: 'Computer Science 101', rating: 4.8 },
            { name: 'Jane Smith', unit: 'Mathematics for Engineers', rating: 4.5 },
        ];

        const filteredTutors = tutors.filter(tutor => 
            tutor.unit.toLowerCase().includes(searchTerm.toLowerCase())
        );

        displayTutors(filteredTutors);
    });

    function displayTutors(tutors) {
        const resultsGrid = document.getElementById('tutorResults');
        resultsGrid.innerHTML = '';

        tutors.forEach(tutor => {
            const tutorCard = document.createElement('div');
            tutorCard.className = 'tutor-card';
            tutorCard.innerHTML = `
                <h3>${tutor.name}</h3>
                <p>${tutor.unit}</p>
                <div class="rating">
                    ${Array(Math.floor(tutor.rating)).fill('<i class="fas fa-star"></i>').join('')}
                </div>
            `;
            resultsGrid.appendChild(tutorCard);
        });
    }

    // 3D Rotation Effect
    const rotationElement = document.getElementById('3d-rotate');
    if (rotationElement) {
        document.addEventListener('mousemove', (e) => {
            const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
            rotationElement.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
        });
    }
});

// Intersection Observer configuration
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            const statCards = entry.target.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                setTimeout(() => animateCounters(card), index * 300);
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.1,
    rootMargin: '0px'
});

// Observe the stats grid container
const statsGrid = document.querySelector('.stats-grid');
if (statsGrid) {
    statsObserver.observe(statsGrid);
}

// Counter animation function
function animateCounters(card) {
    const counter = card.querySelector('h3');
    const target = parseInt(counter.dataset.count);
    const duration = 2000;
    let start = null;

    const step = (timestamp) => {
        if (!start) start = timestamp;
        const progress = timestamp - start;
        const percentage = Math.min(progress / duration, 1);
        const current = Math.floor(percentage * target);
        
        counter.textContent = current + (target === 40 ? '%' : '');
        
        if (percentage < 1) {
            window.requestAnimationFrame(step);
        }
    };
    
    window.requestAnimationFrame(step);
}

console.log('Stats grid element:', statsGrid); // Should show DOM element
statsObserver.observe(statsGrid);
console.log('Observer activated'); // Should appear in console

// FAQ Toggle Functionality
document.querySelectorAll('.faq-question').forEach(question => {
    question.addEventListener('click', () => {
        const faqItem = question.parentElement;
        const answer = faqItem.querySelector('.faq-answer');
        
        // Toggle active class
        faqItem.classList.toggle('active');
        
        // Toggle aria-expanded attribute
        const isExpanded = faqItem.classList.contains('active');
        question.setAttribute('aria-expanded', isExpanded);
        
        // Smooth scroll for opening
        if (isExpanded) {
            const answerHeight = answer.scrollHeight;
            answer.style.maxHeight = `${answerHeight}px`;
        } else {
            answer.style.maxHeight = '0';
        }
    });
});

// Optional: Close FAQ when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.faq-item')) {
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
            item.querySelector('.faq-answer').style.maxHeight = '0';
        });
    }
});

document.getElementById("signUpBtn").addEventListener("click", function() {
    window.location.href = "signup.html";
});

document.getElementById("loginBtn").addEventListener("click", function() {
    window.location.href = "login.html";
});
