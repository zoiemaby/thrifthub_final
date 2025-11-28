/**
 * Register Form Validation and Submission
 * ThriftHub - Registration Form Handler
 * 
 * This script validates the registration form using regex patterns,
 * asynchronously submits to register_customer_action.php,
 * and uses SweetAlert for user notifications.
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Check if SweetAlert is loaded
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded. Please include SweetAlert2 library.');
        // Load SweetAlert2 from CDN if not present
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        document.head.appendChild(script);
        
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css';
        document.head.appendChild(link);
    }

    // Get form elements
    const registerForm = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');
    const passwordStrength = document.getElementById('passwordStrength');

    // Regex patterns for validation
    const regexPatterns = {
        fullname: /^[a-zA-Z\s'-]{2,50}$/, // Letters, spaces, hyphens, apostrophes, 2-50 chars
        username: /^[a-zA-Z0-9_]{3,20}$/, // Alphanumeric and underscore, 3-20 chars
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, // Standard email format
        phone: /^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/, // International phone format
        password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{6,}$/, // At least 6 chars, 1 uppercase, 1 lowercase, 1 number
        passwordSimple: /^.{6,}$/ // Simple: at least 6 characters
    };

    /**
     * Show field error message
     */
    function showFieldError(fieldId, message) {
        const errorEl = document.getElementById(fieldId + 'Error');
        const field = document.getElementById(fieldId);
        
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.add('show');
        }
        
        if (field) {
            field.classList.add('error');
        }
    }

    /**
     * Clear field error message
     */
    function clearFieldError(fieldId) {
        const errorEl = document.getElementById(fieldId + 'Error');
        const field = document.getElementById(fieldId);
        
        if (errorEl) {
            errorEl.classList.remove('show');
            errorEl.textContent = '';
        }
        
        if (field) {
            field.classList.remove('error');
        }
    }

    /**
     * Validate fullname using regex
     */
    function validateFullname() {
        const fullname = document.getElementById('fullname').value.trim();
        
        if (!fullname) {
            showFieldError('fullname', 'Full name is required');
            return false;
        }
        
        if (!regexPatterns.fullname.test(fullname)) {
            showFieldError('fullname', 'Full name must be 2-50 characters and contain only letters, spaces, hyphens, or apostrophes');
            return false;
        }
        
        clearFieldError('fullname');
        return true;
    }

    /**
     * Validate username using regex
     */
    function validateUsername() {
        const username = document.getElementById('username').value.trim();
        
        if (!username) {
            showFieldError('username', 'Username is required');
            return false;
        }
        
        if (!regexPatterns.username.test(username)) {
            showFieldError('username', 'Username must be 3-20 characters and contain only letters, numbers, and underscores');
            return false;
        }
        
        clearFieldError('username');
        return true;
    }

    /**
     * Validate email using regex
     */
    function validateEmail() {
        const email = document.getElementById('email').value.trim();
        
        if (!email) {
            showFieldError('email', 'Email address is required');
            return false;
        }
        
        if (!regexPatterns.email.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
            return false;
        }
        
        clearFieldError('email');
        return true;
    }

    /**
     * Validate phone using regex (optional field)
     */
    function validatePhone() {
        const phone = document.getElementById('phone').value.trim();
        
        // Phone is optional, but if provided, validate it
        if (phone && !regexPatterns.phone.test(phone)) {
            showFieldError('phone', 'Please enter a valid phone number');
            return false;
        }
        
        clearFieldError('phone');
        return true;
    }

    /**
     * Validate password using regex
     */
    function validatePassword() {
        const password = passwordInput.value;
        
        if (!password) {
            showFieldError('password', 'Password is required');
            return false;
        }
        
        if (password.length < 6) {
            showFieldError('password', 'Password must be at least 6 characters long');
            return false;
        }
        
        // Use simple validation (at least 6 chars) - can be enhanced
        if (!regexPatterns.passwordSimple.test(password)) {
            showFieldError('password', 'Password must be at least 6 characters long');
            return false;
        }
        
        clearFieldError('password');
        return true;
    }

    /**
     * Validate password confirmation
     */
    function validateConfirmPassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (!confirmPassword) {
            showFieldError('confirmPassword', 'Please confirm your password');
            return false;
        }
        
        if (password !== confirmPassword) {
            showFieldError('confirmPassword', 'Passwords do not match');
            return false;
        }
        
        clearFieldError('confirmPassword');
        return true;
    }

    /**
     * Validate terms checkbox
     */
    function validateTerms() {
        const terms = document.getElementById('terms').checked;
        
        if (!terms) {
            showFieldError('terms', 'You must agree to the Terms of Service and Privacy Policy');
            return false;
        }
        
        clearFieldError('terms');
        return true;
    }

    /**
     * Validate entire form
     */
    function validateForm() {
        let isValid = true;
        
        // Clear all errors first
        ['fullname', 'username', 'email', 'phone', 'password', 'confirmPassword', 'terms'].forEach(id => {
            clearFieldError(id);
        });
        
        // Validate each field
        if (!validateFullname()) isValid = false;
        if (!validateUsername()) isValid = false;
        if (!validateEmail()) isValid = false;
        if (!validatePhone()) isValid = false;
        if (!validatePassword()) isValid = false;
        if (!validateConfirmPassword()) isValid = false;
        if (!validateTerms()) isValid = false;
        
        return isValid;
    }

    // Password toggle functionality
    if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            toggleIcon.textContent = type === 'password' ? '👁️' : '🙈';
        });
    }

    if (toggleConfirmPassword && confirmPasswordInput && toggleConfirmIcon) {
        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            toggleConfirmIcon.textContent = type === 'password' ? '👁️' : '🙈';
        });
    }

    // Password strength indicator
    if (passwordInput && passwordStrength) {
        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            passwordStrength.className = 'password-strength-bar';
            if (strength <= 2) {
                passwordStrength.classList.add('weak');
            } else if (strength <= 3) {
                passwordStrength.classList.add('medium');
            } else {
                passwordStrength.classList.add('strong');
            }
        });
    }

    // Real-time validation on blur
    ['fullname', 'username', 'email', 'phone', 'password', 'confirmPassword'].forEach(id => {
        const field = document.getElementById(id);
        if (field) {
            field.addEventListener('blur', function() {
                switch(id) {
                    case 'fullname':
                        validateFullname();
                        break;
                    case 'username':
                        validateUsername();
                        break;
                    case 'email':
                        validateEmail();
                        break;
                    case 'phone':
                        validatePhone();
                        break;
                    case 'password':
                        validatePassword();
                        break;
                    case 'confirmPassword':
                        validateConfirmPassword();
                        break;
                }
            });
        }
    });

    // Real-time password confirmation check
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', () => {
            if (confirmPasswordInput.value) {
                validateConfirmPassword();
            } else {
                clearFieldError('confirmPassword');
            }
        });
    }

    // Clear errors on input
    const inputs = document.querySelectorAll('.form-input');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            const fieldId = input.id;
            clearFieldError(fieldId);
        });
    });

    // Form submission handler
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate form before submission
            if (!validateForm()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fix the errors in the form before submitting.',
                    confirmButtonColor: '#0F5E4D'
                });
                return;
            }

            // Disable submit button and show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = '';
                
                // Show loading alert
                Swal.fire({
                    title: 'Creating Account...',
                    text: 'Please wait while we register your account',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Prepare form data
                const formData = new FormData(registerForm);
                
                try {
                    // Asynchronously invoke register_customer_action.php
                    const response = await fetch(registerForm.action, {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.text();
                    
                    // Close loading alert
                    Swal.close();
                    
                    if (response.ok) {
                        // Success - show success message and redirect
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: result || 'Your account has been created successfully.',
                            confirmButtonColor: '#0F5E4D',
                            confirmButtonText: 'Go to Login',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'login.php?registered=1';
                            }
                        });
                    } else {
                        // Error - show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: result || 'An error occurred during registration. Please try again.',
                            confirmButtonColor: '#0F5E4D'
                        });
                        
                        // Re-enable submit button
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('loading');
                            submitBtn.textContent = originalText;
                        }
                    }
                } catch (error) {
                    // Network or other error
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to connect to the server. Please check your internet connection and try again.',
                        confirmButtonColor: '#0F5E4D'
                    });
                    
                    // Re-enable submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('loading');
                        submitBtn.textContent = originalText;
                    }
                }
            }
        });
    }
});

