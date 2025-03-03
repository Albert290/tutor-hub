<footer class="admin-footer">
            <div class="container">
                <div class="footer-content">
                    <p>&copy; <?php echo date('Y'); ?> Tutoring Platform - Admin Panel</p>
                </div>
            </div>
        </footer>
    </div><!-- End admin-content -->
    
    <script>
        // Sidebar toggle
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });
        
        // Close flash messages
        document.querySelectorAll('.flash-message .close-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });
        
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.flash-message').forEach(function(el) {
                el.style.display = 'none';
            });
        }, 5000);
        
        // Dropdown menu
        document.querySelector('.dropdown-btn').addEventListener('click', function() {
            this.nextElementSibling.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.matches('.dropdown-btn') && !e.target.closest('.dropdown-btn')) {
                document.querySelectorAll('.dropdown-content').forEach(function(content) {
                    if (content.classList.contains('show')) {
                        content.classList.remove('show');
                    }
                });
            }
        });
        
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(trigger) {
            trigger.addEventListener('mouseenter', function() {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = this.getAttribute('title');
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
                tooltip.style.left = (rect.left + (rect.width/2) - (tooltip.offsetWidth/2)) + 'px';
                tooltip.classList.add('show');
                
                this.addEventListener('mouseleave', function() {
                    tooltip.remove();
                });
            });
        });
    </script>
</body>
</html>