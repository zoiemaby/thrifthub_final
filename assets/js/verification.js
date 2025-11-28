/**
 * Seller Verification Form - Client-Side Validation & AJAX
 * ThriftHub - Enhanced UX for Seller Application Submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('sellerVerificationForm');
    if (!form) return;

    // Form elements
    const idInput = document.getElementById('id_path');
    const addressInput = document.getElementById('address_path');
    const selfieInput = document.getElementById('selfie_path');
    const momoInput = document.getElementById('momo_number');
    const bankNameInput = document.getElementById('bank_name');
    const accountNumberInput = document.getElementById('account_number');
    const submitBtn = document.getElementById('submitBtn');

    // Preview containers
    const idPreview = document.getElementById('id_preview');
    const addressPreview = document.getElementById('address_preview');
    const selfiePreview = document.getElementById('selfie_preview');

    // Error display containers
    const errorContainer = document.getElementById('errorContainer');
    const successContainer = document.getElementById('successContainer');

    // Validation state
    const validationState = {
        id_path: false,
        address_path: false,
        selfie_path: false,
        momo_number: false,
        bank_name: false,
        account_number: false
    };

    // Allowed file types
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
    const maxFileSize = 5 * 1024 * 1024; // 5MB

    /**
     * Validate file input
     */
    function validateFile(input, fieldName) {
        const file = input.files[0];
        
        if (!file) {
            validationState[fieldName] = false;
            showFieldError(input, 'Please select a file');
            return false;
        }

        // Check file size
        if (file.size > maxFileSize) {
            validationState[fieldName] = false;
            showFieldError(input, 'File size must be less than 5MB');
            return false;
        }

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            validationState[fieldName] = false;
            showFieldError(input, 'Only images (JPEG, PNG, GIF) and PDF files are allowed');
            return false;
        }

        validationState[fieldName] = true;
        clearFieldError(input);
        return true;
    }

    /**
     * Validate mobile money number
     */
    function validateMomoNumber(value) {
        // Ghana format: 10 digits starting with 0
        const momoPattern = /^0\d{9}$/;
        
        if (!value || value.trim() === '') {
            validationState.momo_number = false;
            showFieldError(momoInput, 'Mobile money number is required');
            return false;
        }

        if (!momoPattern.test(value.trim())) {
            validationState.momo_number = false;
            showFieldError(momoInput, 'Enter a valid 10-digit mobile money number (e.g., 0241234567)');
            return false;
        }

        validationState.momo_number = true;
        clearFieldError(momoInput);
        return true;
    }

    /**
     * Validate bank name
     */
    function validateBankName(value) {
        if (!value || value.trim().length < 2) {
            validationState.bank_name = false;
            showFieldError(bankNameInput, 'Bank name is required');
            return false;
        }

        validationState.bank_name = true;
        clearFieldError(bankNameInput);
        return true;
    }

    /**
     * Validate account number
     */
    function validateAccountNumber(value) {
        // Most bank accounts are 10-16 digits
        const accountPattern = /^\d{10,16}$/;
        
        if (!value || value.trim() === '') {
            validationState.account_number = false;
            showFieldError(accountNumberInput, 'Account number is required');
            return false;
        }

        if (!accountPattern.test(value.trim())) {
            validationState.account_number = false;
            showFieldError(accountNumberInput, 'Enter a valid account number (10-16 digits)');
            return false;
        }

        validationState.account_number = true;
        clearFieldError(accountNumberInput);
        return true;
    }

    /**
     * Show field-specific error
     */
    function showFieldError(input, message) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        // Remove existing error
        const existingError = formGroup.querySelector('.field-error');
        if (existingError) existingError.remove();

        // Add error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.color = '#f44336';
        errorDiv.style.fontSize = '13px';
        errorDiv.style.marginTop = '5px';
        
        formGroup.appendChild(errorDiv);
        input.style.borderColor = '#f44336';
    }

    /**
     * Clear field error
     */
    function clearFieldError(input) {
        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        const errorDiv = formGroup.querySelector('.field-error');
        if (errorDiv) errorDiv.remove();
        
        input.style.borderColor = '#ddd';
    }

    /**
     * Show file preview
     */
    function showFilePreview(file, previewContainer) {
        if (!previewContainer) return;

        previewContainer.innerHTML = '';
        previewContainer.style.display = 'block';

        if (file.type.startsWith('image/')) {
            // Image preview
            const img = document.createElement('img');
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.style.borderRadius = '5px';
            img.style.border = '2px solid #4CAF50';
            img.style.marginTop = '10px';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            previewContainer.appendChild(img);
        } else if (file.type === 'application/pdf') {
            // PDF preview
            const pdfIcon = document.createElement('div');
            pdfIcon.innerHTML = `
                <div style="margin-top: 10px; padding: 15px; background: #f5f5f5; border-radius: 5px; display: inline-block;">
                    <span style="font-size: 30px;">📄</span>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #666;">${file.name}</p>
                    <p style="margin: 3px 0 0 0; font-size: 12px; color: #999;">${(file.size / 1024).toFixed(2)} KB</p>
                </div>
            `;
            previewContainer.appendChild(pdfIcon);
        }

        // Add file info
        const fileInfo = document.createElement('p');
        fileInfo.style.fontSize = '13px';
        fileInfo.style.color = '#4CAF50';
        fileInfo.style.marginTop = '5px';
        fileInfo.innerHTML = `✓ ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
        previewContainer.appendChild(fileInfo);
    }

    /**
     * Update submit button state
     */
    function updateSubmitButton() {
        const allValid = Object.values(validationState).every(val => val === true);
        
        if (submitBtn) {
            submitBtn.disabled = !allValid;
            submitBtn.style.opacity = allValid ? '1' : '0.5';
            submitBtn.style.cursor = allValid ? 'pointer' : 'not-allowed';
        }
    }

    /**
     * Show error message
     */
    function showError(message) {
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.style.display = 'block';
            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        if (successContainer) {
            successContainer.style.display = 'none';
        }
    }

    /**
     * Show success message
     */
    function showSuccess(message) {
        if (successContainer) {
            successContainer.textContent = message;
            successContainer.style.display = 'block';
            successContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        if (errorContainer) {
            errorContainer.style.display = 'none';
        }
    }

    /**
     * Hide all messages
     */
    function hideMessages() {
        if (errorContainer) errorContainer.style.display = 'none';
        if (successContainer) successContainer.style.display = 'none';
    }

    // Event listeners for file inputs
    if (idInput) {
        idInput.addEventListener('change', function() {
            if (validateFile(this, 'id_path')) {
                showFilePreview(this.files[0], idPreview);
            } else if (idPreview) {
                idPreview.style.display = 'none';
            }
            updateSubmitButton();
        });
    }

    if (addressInput) {
        addressInput.addEventListener('change', function() {
            if (validateFile(this, 'address_path')) {
                showFilePreview(this.files[0], addressPreview);
            } else if (addressPreview) {
                addressPreview.style.display = 'none';
            }
            updateSubmitButton();
        });
    }

    if (selfieInput) {
        selfieInput.addEventListener('change', function() {
            if (validateFile(this, 'selfie_path')) {
                showFilePreview(this.files[0], selfiePreview);
            } else if (selfiePreview) {
                selfiePreview.style.display = 'none';
            }
            updateSubmitButton();
        });
    }

    // Event listeners for text inputs
    if (momoInput) {
        momoInput.addEventListener('input', function() {
            // Allow only digits
            this.value = this.value.replace(/[^\d]/g, '');
            validateMomoNumber(this.value);
            updateSubmitButton();
        });

        momoInput.addEventListener('blur', function() {
            validateMomoNumber(this.value);
            updateSubmitButton();
        });
    }

    if (bankNameInput) {
        bankNameInput.addEventListener('input', function() {
            validateBankName(this.value);
            updateSubmitButton();
        });

        bankNameInput.addEventListener('blur', function() {
            validateBankName(this.value);
            updateSubmitButton();
        });
    }

    if (accountNumberInput) {
        accountNumberInput.addEventListener('input', function() {
            // Allow only digits
            this.value = this.value.replace(/[^\d]/g, '');
            validateAccountNumber(this.value);
            updateSubmitButton();
        });

        accountNumberInput.addEventListener('blur', function() {
            validateAccountNumber(this.value);
            updateSubmitButton();
        });
    }

    /**
     * Form submission with AJAX
     */
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        hideMessages();

        // Final validation check
        const allValid = Object.values(validationState).every(val => val === true);
        
        if (!allValid) {
            showError('Please fill in all required fields correctly.');
            return;
        }

        // Disable submit button and show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span style="display: inline-block; animation: spin 1s linear infinite;">⏳</span> Uploading...';
        }

        // Create FormData
        const formData = new FormData(form);

        // Send AJAX request
        fetch('../actions/apply_seller_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Application submitted successfully! You will be notified once reviewed.');
                
                // Reset form and validation state
                form.reset();
                Object.keys(validationState).forEach(key => validationState[key] = false);
                
                // Clear previews
                if (idPreview) idPreview.style.display = 'none';
                if (addressPreview) addressPreview.style.display = 'none';
                if (selfiePreview) selfiePreview.style.display = 'none';
                
                // Redirect after 3 seconds
                setTimeout(() => {
                    window.location.href = '../seller/seller_dashboard.php';
                }, 3000);
            } else {
                showError(data.message || 'Failed to submit application. Please try again.');
                
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Submit Application';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while submitting your application. Please try again.');
            
            // Re-enable submit button
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Application';
            }
        });
    });

    // Initial button state
    updateSubmitButton();
});

// CSS animation for loading spinner
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
