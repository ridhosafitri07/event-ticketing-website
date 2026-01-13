
    <!-- Footer (if needed) -->
    <?php if (isset($showFooter) && $showFooter): ?>
    <footer class="footer">
        <div class="footer-container">
            <p>&copy; <?= date('Y') ?> EventKu. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>
    <?php endif; ?>
    
    <!-- JavaScript -->
    <script src="<?= base_url('js/script.js') ?>"></script>
    
    <!-- Flash Messages Handler -->
    <script>
        // Auto hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.querySelector('.hamburger-menu');
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        }
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
