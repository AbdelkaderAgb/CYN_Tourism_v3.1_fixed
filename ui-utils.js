/**
 * CYN Tourism - UI Utilities
 * Modern JavaScript for enhanced interactions
 */

// ============================================
// Theme Management
// ============================================
const ThemeManager = {
    init() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.setTheme(savedTheme);

        // Add theme toggle listeners
        document.querySelectorAll('.theme-toggle').forEach(toggle => {
            toggle.addEventListener('click', () => this.toggle());
        });
    },

    setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        // Update toggle buttons
        document.querySelectorAll('.theme-toggle').forEach(toggle => {
            toggle.classList.toggle('active', theme === 'dark');
        });
    },

    toggle() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }
};

// ============================================
// Sidebar Management
// ============================================
const SidebarManager = {
    init() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');

        // Restore collapsed state
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed && window.innerWidth > 1024) {
            sidebar?.classList.add('collapsed');
        }

        // Toggle button
        document.querySelectorAll('.sidebar-toggle').forEach(btn => {
            btn.addEventListener('click', () => this.toggle());
        });

        // Mobile menu toggle
        document.querySelectorAll('.menu-toggle').forEach(btn => {
            btn.addEventListener('click', () => this.toggleMobile());
        });

        // Close on overlay click
        overlay?.addEventListener('click', () => this.closeMobile());

        // Close on nav item click (mobile)
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    this.closeMobile();
                }
            });
        });

        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                this.closeMobile();
            }
        });
    },

    toggle() {
        const sidebar = document.getElementById('sidebar');
        sidebar?.classList.toggle('collapsed');
        localStorage.setItem('sidebar-collapsed', sidebar?.classList.contains('collapsed'));
    },

    toggleMobile() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        sidebar?.classList.toggle('open');
        overlay?.classList.toggle('show');
        document.body.style.overflow = sidebar?.classList.contains('open') ? 'hidden' : '';
    },

    closeMobile() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        sidebar?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }
};

// ============================================
// Toast Notifications
// ============================================
const Toast = {
    container: null,

    init() {
        this.container = document.createElement('div');
        this.container.className = 'toast-container';
        document.body.appendChild(this.container);
    },

    show(message, type = 'info', title = '', duration = 5000) {
        if (!this.container) this.init();

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">
                <i class="fas ${icons[type]}"></i>
            </div>
            <div class="toast-content">
                ${title ? `<div class="toast-title">${title}</div>` : ''}
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;

        this.container.appendChild(toast);

        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        return toast;
    },

    success(message, title = 'Success') {
        return this.show(message, 'success', title);
    },

    error(message, title = 'Error') {
        return this.show(message, 'error', title);
    },

    warning(message, title = 'Warning') {
        return this.show(message, 'warning', title);
    },

    info(message, title = 'Info') {
        return this.show(message, 'info', title);
    }
};

// ============================================
// Modal Management
// ============================================
const Modal = {
    init() {
        // Close on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop')) {
                this.closeAll();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAll();
            }
        });
    },

    open(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = modal?.querySelector('.modal-backdrop') || document.querySelector('.modal-backdrop');

        if (modal) {
            modal.classList.add('show');
            backdrop?.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    },

    close(modalId) {
        const modal = document.getElementById(modalId);
        const backdrop = modal?.querySelector('.modal-backdrop') || document.querySelector('.modal-backdrop');

        if (modal) {
            modal.classList.remove('show');
            backdrop?.classList.remove('show');
            document.body.style.overflow = '';
        }
    },

    closeAll() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            modal.classList.remove('show');
        });
        document.querySelectorAll('.modal-backdrop.show').forEach(backdrop => {
            backdrop.classList.remove('show');
        });
        document.body.style.overflow = '';
    }
};

// ============================================
// Form Utilities
// ============================================
const FormUtils = {
    init() {
        // Password visibility toggle
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                const input = e.target.closest('.password-input-wrapper').querySelector('input');
                const icon = e.target.querySelector('i') || e.target;

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => this.validate(e));
        });
    },

    validate(e) {
        const form = e.target;
        let isValid = true;

        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            Toast.error('Please fill in all required fields');
        }

        return isValid;
    },

    setLoading(form, loading = true) {
        const submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.toggle('btn-loading', loading);
            submitBtn.disabled = loading;
        }
    }
};

// ============================================
// Table Utilities
// ============================================
const TableUtils = {
    init() {
        // Sortable tables
        document.querySelectorAll('th.sortable').forEach(th => {
            th.addEventListener('click', () => this.sort(th));
        });
    },

    sort(th) {
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const index = Array.from(th.parentNode.children).indexOf(th);
        const isAsc = !th.classList.contains('asc');

        // Reset other headers
        table.querySelectorAll('th.sortable').forEach(h => {
            h.classList.remove('asc', 'desc');
        });

        // Set current header
        th.classList.toggle('asc', isAsc);
        th.classList.toggle('desc', !isAsc);

        // Sort rows
        rows.sort((a, b) => {
            const aVal = a.children[index].textContent.trim();
            const bVal = b.children[index].textContent.trim();

            if (isAsc) {
                return aVal.localeCompare(bVal, undefined, { numeric: true });
            } else {
                return bVal.localeCompare(aVal, undefined, { numeric: true });
            }
        });

        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
    }
};

// ============================================
// Tab Management
// ============================================
const TabManager = {
    init() {
        document.querySelectorAll('.tabs').forEach(tabs => {
            tabs.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', () => this.switch(tab));
            });
        });
    },

    switch(tab) {
        const tabs = tab.closest('.tabs');
        const container = tabs.nextElementSibling?.classList.contains('tab-contents') 
            ? tabs.nextElementSibling 
            : tabs.parentElement;

        // Update tabs
        tabs.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        // Update content
        const targetId = tab.dataset.target;
        if (targetId) {
            container.querySelectorAll('.tab-content').forEach(content => {
                content.classList.toggle('active', content.id === targetId);
            });
        }
    }
};

// ============================================
// Dropdown Management
// ============================================
const DropdownManager = {
    init() {
        // Close dropdowns on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        // Toggle dropdowns
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const menu = toggle.nextElementSibling;
                if (menu?.classList.contains('dropdown-menu')) {
                    menu.classList.toggle('show');
                }
            });
        });
    }
};

// ============================================
// Loading States
// ============================================
const LoadingManager = {
    show(element) {
        element.classList.add('loading');
    },

    hide(element) {
        element.classList.remove('loading');
    },

    showPageLoader() {
        const loader = document.querySelector('.page-loader');
        if (loader) {
            loader.classList.remove('hidden');
        }
    },

    hidePageLoader() {
        const loader = document.querySelector('.page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    }
};

// ============================================
// Animation Utilities
// ============================================
const AnimationUtils = {
    fadeIn(element, duration = 300) {
        element.style.opacity = '0';
        element.style.display = 'block';
        element.style.transition = `opacity ${duration}ms ease`;

        requestAnimationFrame(() => {
            element.style.opacity = '1';
        });
    },

    fadeOut(element, duration = 300) {
        element.style.transition = `opacity ${duration}ms ease`;
        element.style.opacity = '0';

        setTimeout(() => {
            element.style.display = 'none';
        }, duration);
    },

    slideDown(element, duration = 300) {
        element.style.height = '0';
        element.style.overflow = 'hidden';
        element.style.transition = `height ${duration}ms ease`;

        requestAnimationFrame(() => {
            element.style.height = element.scrollHeight + 'px';
        });

        setTimeout(() => {
            element.style.height = '';
            element.style.overflow = '';
        }, duration);
    },

    slideUp(element, duration = 300) {
        element.style.height = element.scrollHeight + 'px';
        element.style.overflow = 'hidden';
        element.style.transition = `height ${duration}ms ease`;

        requestAnimationFrame(() => {
            element.style.height = '0';
        });

        setTimeout(() => {
            element.style.display = 'none';
            element.style.height = '';
            element.style.overflow = '';
        }, duration);
    }
};

// ============================================
// Utility Functions
// ============================================
const Utils = {
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    throttle(func, limit) {
        let inThrottle;
        return function executedFunction(...args) {
            if (!inThrottle) {
                func(...args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    formatDate(date, format = 'YYYY-MM-DD') {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');

        return format
            .replace('YYYY', year)
            .replace('MM', month)
            .replace('DD', day);
    },

    formatNumber(num, decimals = 0) {
        return Number(num).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    },

    copyToClipboard(text) {
        return navigator.clipboard.writeText(text).then(() => {
            Toast.success('Copied to clipboard');
        }).catch(() => {
            Toast.error('Failed to copy');
        });
    }
};

// ============================================
// Initialize Everything on DOM Ready
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    ThemeManager.init();
    SidebarManager.init();
    Modal.init();
    FormUtils.init();
    TableUtils.init();
    TabManager.init();
    DropdownManager.init();

    // Hide page loader
    setTimeout(() => {
        LoadingManager.hidePageLoader();
    }, 300);

    // Add animation classes to elements
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        el.classList.add('animate-fadeInUp');
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ThemeManager,
        SidebarManager,
        Toast,
        Modal,
        FormUtils,
        TableUtils,
        TabManager,
        DropdownManager,
        LoadingManager,
        AnimationUtils,
        Utils
    };
}
