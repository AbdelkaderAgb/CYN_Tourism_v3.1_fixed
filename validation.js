/**
 * CYN Tourism - Form Validation JavaScript
 * Client-side validation for all forms
 * 
 * @package CYN_Tourism
 * @version 1.0.0
 */

(function() {
    'use strict';

    // Validation messages
    const messages = {
        required: 'This field is required',
        email: 'Please enter a valid email address',
        phone: 'Please enter a valid phone number',
        min: 'This field must be at least {min} characters',
        max: 'This field must not exceed {max} characters',
        numeric: 'This field must be numeric',
        date: 'Please enter a valid date',
        url: 'Please enter a valid URL',
        match: 'Fields do not match',
        password: 'Password must be at least 8 characters with uppercase, lowercase, number, and special character'
    };

    /**
     * FormValidator class
     */
    class FormValidator {
        constructor(form) {
            this.form = form;
            this.errors = {};
            this.init();
        }

        init() {
            // Add validation on submit
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
            
            // Add real-time validation on blur
            const inputs = this.form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => this.clearError(input));
            });
        }

        handleSubmit(e) {
            this.errors = {};
            
            const inputs = this.form.querySelectorAll('input, textarea, select');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                this.showFirstError();
                return false;
            }
            
            // Disable submit button to prevent double submission
            const submitBtn = this.form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
            }
            
            return true;
        }

        validateField(field) {
            const rules = this.getRules(field);
            const value = field.value.trim();
            let isValid = true;
            
            for (const rule of rules) {
                if (!this.checkRule(rule, value, field)) {
                    isValid = false;
                    this.showError(field, this.getErrorMessage(rule, field));
                    break;
                }
            }
            
            if (isValid) {
                this.clearError(field);
            }
            
            return isValid;
        }

        getRules(field) {
            const rules = [];
            
            if (field.required || field.dataset.required === 'true') {
                rules.push('required');
            }
            
            if (field.type === 'email' || field.dataset.validate === 'email') {
                rules.push('email');
            }
            
            if (field.dataset.validate === 'phone') {
                rules.push('phone');
            }
            
            if (field.dataset.validate === 'numeric') {
                rules.push('numeric');
            }
            
            if (field.dataset.validate === 'date') {
                rules.push('date');
            }
            
            if (field.dataset.validate === 'url') {
                rules.push('url');
            }
            
            if (field.dataset.min) {
                rules.push('min:' + field.dataset.min);
            }
            
            if (field.dataset.max) {
                rules.push('max:' + field.dataset.max);
            }
            
            if (field.dataset.match) {
                rules.push('match:' + field.dataset.match);
            }
            
            if (field.dataset.validate === 'password') {
                rules.push('password');
            }
            
            return rules;
        }

        checkRule(rule, value, field) {
            const [ruleName, ruleValue] = rule.split(':');
            
            switch (ruleName) {
                case 'required':
                    return value.length > 0;
                    
                case 'email':
                    if (!value) return true;
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(value);
                    
                case 'phone':
                    if (!value) return true;
                    const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
                    return phoneRegex.test(value.replace(/\s/g, ''));
                    
                case 'numeric':
                    if (!value) return true;
                    return !isNaN(value);
                    
                case 'date':
                    if (!value) return true;
                    const date = new Date(value);
                    return !isNaN(date.getTime());
                    
                case 'url':
                    if (!value) return true;
                    try {
                        new URL(value);
                        return true;
                    } catch {
                        return false;
                    }
                    
                case 'min':
                    return value.length >= parseInt(ruleValue);
                    
                case 'max':
                    return value.length <= parseInt(ruleValue);
                    
                case 'match':
                    const matchField = document.getElementById(ruleValue) || 
                                      document.querySelector('[name="' + ruleValue + '"]');
                    return value === (matchField ? matchField.value : '');
                    
                case 'password':
                    if (!value) return true;
                    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                    return passwordRegex.test(value);
                    
                default:
                    return true;
            }
        }

        getErrorMessage(rule, field) {
            const [ruleName, ruleValue] = rule.split(':');
            let message = messages[ruleName] || 'Invalid value';
            
            // Replace placeholders
            message = message.replace('{min}', ruleValue || '');
            message = message.replace('{max}', ruleValue || '');
            
            // Check for custom message
            if (field.dataset.errorMessage) {
                message = field.dataset.errorMessage;
            }
            
            return message;
        }

        showError(field, message) {
            this.clearError(field);
            
            field.classList.add('is-invalid');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            
            // Insert after field or its parent (for input groups)
            const parent = field.closest('.input-group') || field.parentElement;
            parent.appendChild(errorDiv);
            
            this.errors[field.name] = message;
        }

        clearError(field) {
            field.classList.remove('is-invalid');
            
            const parent = field.closest('.input-group') || field.parentElement;
            const errorDiv = parent.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
            
            delete this.errors[field.name];
        }

        showFirstError() {
            const firstErrorField = this.form.querySelector('.is-invalid');
            if (firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    // Initialize validation on all forms with data-validate attribute
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            new FormValidator(form);
        });
    });

    // Global validation functions
    window.CYNValidation = {
        // Validate email
        isEmail: function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        // Validate phone
        isPhone: function(phone) {
            const regex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
            return regex.test(phone.replace(/\s/g, ''));
        },

        // Validate date
        isDate: function(date) {
            const d = new Date(date);
            return !isNaN(d.getTime());
        },

        // Validate URL
        isUrl: function(url) {
            try {
                new URL(url);
                return true;
            } catch {
                return false;
            }
        },

        // Validate Turkish ID
        isTCKN: function(tckn) {
            if (!/^\d{11}$/.test(tckn) || tckn[0] === '0') {
                return false;
            }
            
            const digits = tckn.split('').map(Number);
            const sum1 = digits[0] + digits[2] + digits[4] + digits[6] + digits[8];
            const sum2 = digits[1] + digits[3] + digits[5] + digits[7];
            const check10 = ((sum1 * 7) - sum2) % 10;
            
            if (check10 !== digits[9]) {
                return false;
            }
            
            const sumAll = digits.slice(0, 10).reduce((a, b) => a + b, 0);
            return sumAll % 10 === digits[10];
        },

        // Format phone number
        formatPhone: function(phone) {
            const cleaned = phone.replace(/\D/g, '');
            if (cleaned.length === 10) {
                return cleaned.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '($1) $2 $3 $4');
            }
            return phone;
        },

        // Format date
        formatDate: function(date, format = 'DD/MM/YYYY') {
            const d = new Date(date);
            if (isNaN(d.getTime())) return '';
            
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            
            return format
                .replace('DD', day)
                .replace('MM', month)
                .replace('YYYY', year);
        },

        // Show toast notification
        showToast: function(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        },

        // Confirm action
        confirm: function(message, callback) {
            if (confirm(message)) {
                callback();
            }
        },

        // Prevent double submission
        preventDoubleSubmit: function(form) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
            }
        },

        // Reset form submission
        resetSubmit: function(form) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && submitBtn.dataset.originalText) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitBtn.dataset.originalText;
            }
        }
    };

})();
