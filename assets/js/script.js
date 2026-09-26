// assets/js/script.js

document.addEventListener("DOMContentLoaded", function() {
    // Sidebar toggle functionality
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');

    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Real-time clock update
    const clockElement = document.getElementById('realTimeClock');
    const dateElement = document.getElementById('realTimeDate');
    const clockIcon = document.getElementById('clockIcon');
    if (clockElement) {
        function updateClock() {
            const now = new Date();
            
            if (dateElement) {
                const dateOptions = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
                dateElement.textContent = now.toLocaleDateString('en-US', dateOptions);
            }
            
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            clockElement.textContent = now.toLocaleTimeString('en-US', timeOptions);
            
            if (clockIcon) {
                clockIcon.style.transition = 'all 0.2s ease-in-out';
                clockIcon.style.transform = 'scale(1.15)';
                setTimeout(() => {
                    clockIcon.style.transform = 'scale(1)';
                }, 200);
            }
        }
        updateClock(); // Initial call
        setInterval(updateClock, 1000); // Update every second
    }
});
