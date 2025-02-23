document.addEventListener("DOMContentLoaded", function () {
    // Scroll Event for Stats Counter
    const statsGrid = document.querySelector(".stats-grid");
    if (statsGrid) {
        const statsObserver = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                document.querySelectorAll(".stat-number").forEach(stat => {
                    const target = +stat.dataset.target;
                    let count = 0;
                    const speed = Math.ceil(target / 100);
                    const updateCount = () => {
                        if (count < target) {
                            count += speed;
                            stat.textContent = count;
                            setTimeout(updateCount, 20);
                        } else {
                            stat.textContent = target;
                        }
                    };
                    updateCount();
                });
                statsObserver.unobserve(statsGrid);
            }
        }, { threshold: 2.0 });
        statsObserver.observe(statsGrid);
    }

    // FAQ Section Toggle
    document.querySelectorAll(".faq-question").forEach(question => {
        question.addEventListener("click", function () {
            const answer = this.nextElementSibling;
            const isOpen = answer.style.display === "block";
            document.querySelectorAll(".faq-answer").forEach(a => a.style.display = "none");
            answer.style.display = isOpen ? "none" : "block";
        });
    });

    // Close FAQ when clicking outside
    document.addEventListener("click", function (event) {
        if (!event.target.closest(".faq-question")) {
            document.querySelectorAll(".faq-answer").forEach(answer => answer.style.display = "none");
        }
    });

    // Role Selection in Form
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const studentFields = document.querySelector('.student-fields');
    const tutorFields = document.querySelector('.tutor-fields');

    roleRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'student') {
                studentFields.style.display = 'block';
                tutorFields.style.display = 'none';
            } else if (radio.value === 'tutor') {
                studentFields.style.display = 'none';
                tutorFields.style.display = 'block';
            }
        });
    });
});

document.querySelector('form').addEventListener('submit', (e) => {
    e.preventDefault(); // Prevents the form from refreshing the page
});

// File upload display
document.getElementById('transcript').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'No file chosen';
    document.querySelector('.file-name').textContent = fileName;
});

// Initialize Select2 (if using)
$('.course-select').select2({
    placeholder: "Select subjects you want to teach",
    width: '100%',
    theme: 'bootstrap',
    closeOnSelect: false
});