/**
 * Login Form Validation and Submission
 * ThriftHub - Login Form Handler
 * 
 * This script validates the login form using regex patterns,
 * asynchronously submits to login_customer_action.php,
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
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const toggleIcon = document.getElementById('toggleIcon');

    // Regex patterns for validation
    const regexPatterns = {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, // Standard email format
        password: /^.{1,}$/ // At least 1 character (password should not be empty)
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
     * Validate email using regex
     */
    function validateEmail() {
        const email = emailInput.value.trim();
        
        if (!email) {
            showFieldError('email', 'Email address is required');
            return false;
        }
        
        // Check email type
        if (typeof email !== 'string') {
            showFieldError('email', 'Email must be a valid string');
            return false;
        }
        
        // Validate email format using regex
        if (!regexPatterns.email.test(email)) {
            showFieldError('email', 'Please enter a valid email address');
            return false;
        }
        
        clearFieldError('email');
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
        
        // Check password type
        if (typeof password !== 'string') {
            showFieldError('password', 'Password must be a valid string');
            return false;
        }
        
        // Validate password is not empty using regex
        if (!regexPatterns.password.test(password)) {
            showFieldError('password', 'Password cannot be empty');
            return false;
        }
        
        // Check minimum length
        if (password.length < 1) {
            showFieldError('password', 'Password is required');
            return false;
        }
        
        clearFieldError('password');
        return true;
    }

    /**
     * Validate entire form
     */
    function validateForm() {
        let isValid = true;
        
        // Clear all errors first
        ['email', 'password'].forEach(id => {
            clearFieldError(id);
        });
        
        // Validate each field
        if (!validateEmail()) isValid = false;
        if (!validatePassword()) isValid = false;
        
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

    // Real-time validation on blur
    if (emailInput) {
        emailInput.addEventListener('blur', validateEmail);
    }

    if (passwordInput) {
        passwordInput.addEventListener('blur', validatePassword);
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
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
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
                    title: 'Logging In...',
                    text: 'Please wait while we verify your credentials',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Prepare form data
                const formData = new FormData(loginForm);
                
                try {
                    // Asynchronously invoke login_customer_action.php
                    const response = await fetch('../actions/login_customer_action.php', {
                        method: 'POST',
                        body: formData
                    });

                    let result;
                    let userRole = null;
                    let message = 'Welcome back! Redirecting...';
                    
                    // Get response text first
                    const responseText = await response.text();
                    
                    // Try to parse as JSON
                    try {
                        result = JSON.parse(responseText);
                        userRole = result.role || null;
                        message = result.message || message;
                    } catch (e) {
                        // If not JSON, treat as plain text
                        result = responseText;
                        message = responseText || message;
                    }
                    
                    // Close loading alert
                    Swal.close();
                    
                    // Check if login was successful (check both response.ok and result.success)
                    const isSuccess = response.ok && (typeof result === 'object' ? result.success : true);
                    
                    if (isSuccess) {
                        // Success - show success message and redirect based on actual role from database
                        // userRole comes from the server response (actual database role as number: 1, 2, or 3)
                        const actualRole = userRole !== null ? parseInt(userRole) : 2; // Default to 2 (customer) if role not provided
                        
                        // Debug logging
                        console.log('Login successful. Role:', actualRole, 'Type:', typeof actualRole);
                        console.log('Full result:', result);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: message,
                            confirmButtonColor: '#0F5E4D',
                            confirmButtonText: 'Continue',
                            allowOutsideClick: false,
                            timer: 1500,
                            timerProgressBar: true
                        }).then(() => {
                            // Determine redirect based on the actual role from the server (stored in session)
                            console.log('Redirecting based on role:', actualRole, 'Type:', typeof actualRole);

                            if (actualRole === 1) {
                                // Redirect admin to admin dashboard
                                console.log('Redirecting to admin dashboard');
                                window.location.href = '../admin/admin_dashboard.php';
                            } else if (actualRole === 3) {
                                // Redirect sellers based on verification status from server
                                const isVerified = (typeof result === 'object' && 'verified' in result) ? !!result.verified : false;
                                console.log('Seller role detected. Verified:', isVerified);
                                if (isVerified) {
                                    console.log('Seller verified: redirecting to dashboard');
                                    window.location.href = '../seller/seller_dashboard.php';
                                } else {
                                    console.log('Seller not verified: redirecting to verification');
                                    window.location.href = 'seller_verification.php';
                                }
                            } else {
                                // Redirect buyers/customers to browse products page
                                console.log('Redirecting customer to browse products');
                                window.location.href = 'browse_products.php';
                            }
                        });
                    } else {
                        // Error - parse error message
                        let errorMessage = 'Invalid email or password. Please try again.';
                        if (typeof result === 'object') {
                            if (result.message) {
                                errorMessage = result.message;
                            } else if (result.success === false) {
                                errorMessage = result.message || 'Login failed. Please check your credentials.';
                            }
                        } else if (typeof result === 'string') {
                            errorMessage = result;
                        }
                        
                        console.error('Login failed:', result);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: errorMessage,
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
                    console.error('Login error:', error);
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error',
                        text: 'Unable to connect to the server. Please check your internet connection and try again. Error: ' + error.message,
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

