<?php
/**
 * CYN Tourism - Footer Layout (Tailwind CSS + Alpine.js)
 * 
 * @package CYN_Tourism
 * @version 3.1.0
 */
?>
            </div><!-- /.page-content -->
        </main>
    </div><!-- /.app-layout -->

    <!-- Toast Container (Alpine.js managed) -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-[9999] space-y-2"></div>

    <!-- App Shell Alpine.js Component -->
    <script>
    function appShell() {
        return {
            loading: true,
            sidebarOpen: false,
            sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true',

            init() {
                // Hide loader after DOM is ready
                this.$nextTick(() => {
                    setTimeout(() => { this.loading = false; }, 200);
                });
            },

            toggleCollapse() {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebar-collapsed', this.sidebarCollapsed);
            }
        };
    }

    // Toast Notification System (Vanilla JS for global use)
    function showToast(message, type) {
        type = type || 'info';
        var container = document.getElementById('toast-container');
        if (!container) return;

        var colorClasses = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-amber-500',
            info: 'bg-blue-500'
        };

        var icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };

        var toast = document.createElement('div');
        toast.className = 'flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-white text-sm transition-all duration-300 transform translate-x-full opacity-0 ' + (colorClasses[type] || colorClasses.info);
        toast.innerHTML = '<i class="fas fa-' + (icons[type] || 'info-circle') + '"></i><span>' + message + '</span>';
        container.appendChild(toast);

        // Animate in
        requestAnimationFrame(function() {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });

        // Remove after delay
        setTimeout(function() {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }

    // Form validation (Vanilla JS)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form[data-validate]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var requiredFields = form.querySelectorAll('[required]');
                var isValid = true;

                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500', 'ring-red-500');
                    } else {
                        field.classList.remove('border-red-500', 'ring-red-500');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    showToast('Please fill in all required fields', 'error');
                }
            });
        });
    });

    // Confirm delete
    function confirmDelete(message) {
        return confirm(message || 'Are you sure you want to delete this item?');
    }

    // Print function
    function printPage() {
        window.print();
    }
    </script>

    <!-- Legacy UI utils for backward compatibility -->
    <script src="ui-utils.js"></script>
</body>
</html>
