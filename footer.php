<?php
/**
 * CYN Tourism - Footer Template
 */
?>
            </div><!-- /.page-content -->
        </main>
    </div><!-- /.app-layout -->
    
    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>
    
    <!-- Scripts -->
    <script src="ui-utils.js"></script>
    <script>
    // Toast Notification System
    function showToast(message, type) {
        type = type || 'info';
        var container = document.getElementById('toast-container');
        if (!container) return;
        var toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        
        var icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        
        toast.innerHTML = '<i class="fas fa-' + (icons[type] || 'info-circle') + '"></i><span>' + message + '</span>';
        container.appendChild(toast);
        
        setTimeout(function() { toast.classList.add('show'); }, 10);
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() { toast.remove(); }, 300);
        }, 3000);
    }
    
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var requiredFields = form.querySelectorAll('[required]');
                var isValid = true;
                
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                    } else {
                        field.classList.remove('error');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    showToast('Please fill in all required fields', 'error');
                }
            });
        });
        
        // Hide page loader
        var loader = document.querySelector('.page-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(function() { loader.style.display = 'none'; }, 300);
        }
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
</body>
</html>
