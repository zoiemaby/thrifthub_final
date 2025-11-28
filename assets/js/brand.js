/**
 * Brand Management JavaScript
 * ThriftHub - Brand Form Validation and API Handler
 * 
 * This script validates brand information, checks types,
 * asynchronously invokes the brand action scripts (fetch, add, update, delete),
 * and informs the user of success/failure using SweetAlert.
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

    // Brand name validation regex (alphanumeric, spaces, hyphens, underscores, 2-100 chars)
    const BRAND_NAME_REGEX = /^[a-zA-Z0-9\s\-_]{2,100}$/;
    
    // Store brands data
    let brands = [];

    /**
     * Validate brand name
     * @param {string} brandName - Brand name to validate
     * @returns {object} Validation result with isValid and message
     */
    function validateBrandName(brandName) {
        if (!brandName || typeof brandName !== 'string') {
            return {
                isValid: false,
                message: 'Brand name is required and must be a string.'
            };
        }

        const trimmed = brandName.trim();
        
        if (trimmed.length === 0) {
            return {
                isValid: false,
                message: 'Brand name cannot be empty.'
            };
        }

        if (trimmed.length < 2) {
            return {
                isValid: false,
                message: 'Brand name must be at least 2 characters long.'
            };
        }

        if (trimmed.length > 100) {
            return {
                isValid: false,
                message: 'Brand name must not exceed 100 characters.'
            };
        }

        if (!BRAND_NAME_REGEX.test(trimmed)) {
            return {
                isValid: false,
                message: 'Brand name can only contain letters, numbers, spaces, hyphens, and underscores.'
            };
        }

        return {
            isValid: true,
            message: 'Valid brand name.'
        };
    }

    /**
     * Validate brand ID
     * @param {number|string} brandId - Brand ID to validate
     * @returns {object} Validation result with isValid and message
     */
    function validateBrandId(brandId) {
        if (brandId === null || brandId === undefined || brandId === '') {
            return {
                isValid: false,
                message: 'Brand ID is required.'
            };
        }

        const id = parseInt(brandId);
        
        if (isNaN(id) || id <= 0) {
            return {
                isValid: false,
                message: 'Brand ID must be a positive number.'
            };
        }

        return {
            isValid: true,
            message: 'Valid brand ID.',
            id: id
        };
    }

    /**
     * Fetch all brands from the server
     * @returns {Promise<object>} Promise that resolves with brands data
     */
    async function fetchBrands() {
        try {
            // Use the fetch_brand_action.php endpoint
            const response = await fetch('../actions/fetch_brand_action.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to fetch brands.');
            }

            if (result.success) {
                brands = result.brands || [];
                return {
                    success: true,
                    brands: brands,
                    message: result.message || 'Brands fetched successfully.'
                };
            } else {
                throw new Error(result.message || 'Failed to fetch brands.');
            }
        } catch (error) {
            console.error('Error fetching brands:', error);
            return {
                success: false,
                message: error.message || 'An error occurred while fetching brands.'
            };
        }
    }

    /**
     * Add a new brand
     * @param {string} brandName - Brand name to add
     * @returns {Promise<object>} Promise that resolves with result
     */
    async function addBrand(brandName) {
        // Validate brand name
        const validation = validateBrandName(brandName);
        if (!validation.isValid) {
            return {
                success: false,
                message: validation.message
            };
        }

        try {
            const formData = new FormData();
            formData.append('brand_name', brandName.trim());

            const response = await fetch('../actions/add_brand_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to add brand.');
            }

            return result;
        } catch (error) {
            console.error('Error adding brand:', error);
            return {
                success: false,
                message: error.message || 'An error occurred while adding the brand.'
            };
        }
    }

    /**
     * Update an existing brand
     * @param {number} brandId - Brand ID to update
     * @param {string} brandName - New brand name
     * @returns {Promise<object>} Promise that resolves with result
     */
    async function updateBrand(brandId, brandName) {
        // Validate brand ID
        const idValidation = validateBrandId(brandId);
        if (!idValidation.isValid) {
            return {
                success: false,
                message: idValidation.message
            };
        }

        // Validate brand name
        const nameValidation = validateBrandName(brandName);
        if (!nameValidation.isValid) {
            return {
                success: false,
                message: nameValidation.message
            };
        }

        try {
            const formData = new FormData();
            formData.append('brand_id', idValidation.id);
            formData.append('brand_name', brandName.trim());

            const response = await fetch('../actions/update_brand_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to update brand.');
            }

            return result;
        } catch (error) {
            console.error('Error updating brand:', error);
            return {
                success: false,
                message: error.message || 'An error occurred while updating the brand.'
            };
        }
    }

    /**
     * Delete a brand
     * @param {number} brandId - Brand ID to delete
     * @returns {Promise<object>} Promise that resolves with result
     */
    async function deleteBrand(brandId) {
        // Validate brand ID
        const validation = validateBrandId(brandId);
        if (!validation.isValid) {
            return {
                success: false,
                message: validation.message
            };
        }

        try {
            const formData = new FormData();
            formData.append('brand_id', validation.id);

            const response = await fetch('../actions/delete_brand_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete brand.');
            }

            return result;
        } catch (error) {
            console.error('Error deleting brand:', error);
            return {
                success: false,
                message: error.message || 'An error occurred while deleting the brand.'
            };
        }
    }

    /**
     * Show success message using SweetAlert
     * @param {string} message - Success message
     */
    function showSuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            alert('Success: ' + message);
        }
    }

    /**
     * Show error message using SweetAlert
     * @param {string} message - Error message
     */
    function showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#0F5E4D'
            });
        } else {
            alert('Error: ' + message);
        }
    }

    /**
     * Show confirmation dialog using SweetAlert
     * @param {string} message - Confirmation message
     * @returns {Promise<boolean>} Promise that resolves to true if confirmed
     */
    function showConfirm(message) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: message,
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0F5E4D',
                cancelButtonColor: '#6B6B6B'
            }).then((result) => {
                return result.isConfirmed;
            });
        } else {
            return Promise.resolve(confirm(message));
        }
    }

    /**
     * Show loading state
     * @returns {object} Swal instance for closing
     */
    function showLoading() {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        return null;
    }

    // Initialize form handlers if elements exist
    const addBrandForm = document.getElementById('addBrandForm');
    const brandNameInput = document.getElementById('brandNameInput');
    const addBrandBtn = document.getElementById('addBrandBtn');
    const brandsGrid = document.getElementById('brandsGrid');
    const brandCount = document.getElementById('brandCount');

    // Handle add brand form submission
    if (addBrandForm) {
        addBrandForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const brandName = brandNameInput ? brandNameInput.value : '';

            // Validate before submission
            const validation = validateBrandName(brandName);
            if (!validation.isValid) {
                showError(validation.message);
                return;
            }

            // Show loading
            const loadingSwal = showLoading();

            // Disable submit button
            if (addBrandBtn) {
                addBrandBtn.disabled = true;
                const originalText = addBrandBtn.innerHTML;
                addBrandBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            }

            try {
                // Add brand
                const result = await addBrand(brandName);

                // Close loading
                if (loadingSwal) {
                    Swal.close();
                }

                if (result.success) {
                    showSuccess(result.message || 'Brand added successfully!');
                    
                    // Reset form
                    addBrandForm.reset();
                    
                    // Reload brands list
                    await loadBrands();
                } else {
                    showError(result.message || 'Failed to add brand.');
                }
            } catch (error) {
                if (loadingSwal) {
                    Swal.close();
                }
                showError(error.message || 'An error occurred while adding the brand.');
            } finally {
                // Re-enable submit button
                if (addBrandBtn) {
                    addBrandBtn.disabled = false;
                    addBrandBtn.innerHTML = '<i class="fas fa-plus"></i> <span>Add Brand</span>';
                }
            }
        });
    }

    // Real-time validation for brand name input
    if (brandNameInput) {
        brandNameInput.addEventListener('blur', function() {
            const validation = validateBrandName(this.value);
            if (!validation.isValid && this.value.trim() !== '') {
                this.setCustomValidity(validation.message);
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });

        brandNameInput.addEventListener('input', function() {
            if (this.validity.customError) {
                this.setCustomValidity('');
            }
        });
    }

    /**
     * Render brands list
     */
    function renderBrands() {
        if (!brandsGrid) return;

        if (brandCount) {
            brandCount.textContent = `${brands.length} total`;
        }

        if (brands.length === 0) {
            brandsGrid.innerHTML = `
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <div class="empty-state-icon">🏷️</div>
                    <div class="empty-state-text">No brands yet. Add your first brand above.</div>
                </div>
            `;
            return;
        }

        brandsGrid.innerHTML = brands.map(brand => `
            <div class="item-card">
                <div class="item-name">${escapeHtml(brand.brand_name)}</div>
                <div class="item-actions">
                    <button class="item-action-btn edit" onclick="editBrandHandler(${brand.brand_id}, '${escapeHtml(brand.brand_name).replace(/'/g, "\\'")}')" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button class="item-action-btn delete" onclick="deleteBrandHandler(${brand.brand_id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Load brands and render
     */
    async function loadBrands() {
        const result = await fetchBrands();
        if (result.success) {
            brands = result.brands || [];
            renderBrands();
        } else {
            showError(result.message || 'Failed to load brands.');
        }
    }

    /**
     * Edit brand handler (called from onclick)
     * @param {number} brandId - Brand ID
     * @param {string} currentName - Current brand name
     */
    window.editBrandHandler = async function(brandId, currentName) {
        const { value: newName } = await Swal.fire({
            title: 'Edit Brand',
            input: 'text',
            inputLabel: 'Brand Name',
            inputValue: currentName,
            inputPlaceholder: 'Enter brand name',
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0F5E4D',
            inputValidator: (value) => {
                const validation = validateBrandName(value);
                if (!validation.isValid) {
                    return validation.message;
                }
            }
        });

        if (newName && newName.trim() !== currentName) {
            const loadingSwal = showLoading();
            
            try {
                const result = await updateBrand(brandId, newName);
                
                if (loadingSwal) {
                    Swal.close();
                }

                if (result.success) {
                    showSuccess(result.message || 'Brand updated successfully!');
                    await loadBrands();
                } else {
                    showError(result.message || 'Failed to update brand.');
                }
            } catch (error) {
                if (loadingSwal) {
                    Swal.close();
                }
                showError(error.message || 'An error occurred while updating the brand.');
            }
        }
    };

    /**
     * Delete brand handler (called from onclick)
     * @param {number} brandId - Brand ID
     */
    window.deleteBrandHandler = async function(brandId) {
        const confirmed = await showConfirm('Are you sure you want to delete this brand? This action cannot be undone.');
        
        if (!confirmed) {
            return;
        }

        const loadingSwal = showLoading();

        try {
            const result = await deleteBrand(brandId);

            if (loadingSwal) {
                Swal.close();
            }

            if (result.success) {
                showSuccess(result.message || 'Brand deleted successfully!');
                await loadBrands();
            } else {
                showError(result.message || 'Failed to delete brand.');
            }
        } catch (error) {
            if (loadingSwal) {
                Swal.close();
            }
            showError(error.message || 'An error occurred while deleting the brand.');
        }
    };

    // Initialize: Load brands on page load
    if (brandsGrid) {
        loadBrands();
    }

    // Export functions for global access if needed
    window.brandManager = {
        fetchBrands,
        addBrand,
        updateBrand,
        deleteBrand,
        loadBrands,
        validateBrandName,
        validateBrandId
    };
});

