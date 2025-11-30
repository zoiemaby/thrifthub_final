/**
 * Product Management JavaScript
 * ThriftHub - Product Form Validation and API Handler
 * 
 * This script validates product information, checks types,
 * asynchronously invokes the product action scripts (add, update, upload_image),
 * and informs the user of success/failure using SweetAlert.
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Check if SweetAlert is loaded
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded. Please include SweetAlert2 library.');
    }

    // Store current editing product ID
    let editingProductId = null;

    /**
     * Validate product title
     */
    function validateProductTitle(title) {
        if (!title || typeof title !== 'string') {
            return { isValid: false, message: 'Product title is required and must be a string.' };
        }
        const trimmed = title.trim();
        if (trimmed.length < 3) {
            return { isValid: false, message: 'Product title must be at least 3 characters long.' };
        }
        if (trimmed.length > 200) {
            return { isValid: false, message: 'Product title must not exceed 200 characters.' };
        }
        return { isValid: true };
    }

    /**
     * Validate product price
     */
    function validateProductPrice(price) {
        const numPrice = parseFloat(price);
        if (isNaN(numPrice) || numPrice <= 0) {
            return { isValid: false, message: 'Product price must be a positive number.' };
        }
        return { isValid: true, value: numPrice };
    }

    /**
     * Validate product category
     */
    function validateProductCategory(categoryId) {
        if (!categoryId || categoryId === '' || parseInt(categoryId) <= 0) {
            return { isValid: false, message: 'Product category is required.' };
        }
        return { isValid: true };
    }

    /**
     * Populate category dropdown
     */
    async function populateCategoryDropdown() {
        const categorySelect = document.getElementById('product_cat');
        if (!categorySelect) return;
        
        try {
            const response = await fetch('../actions/fetch_categories_action.php');
            const result = await response.json();
            
            if (result.success && result.categories) {
                categorySelect.innerHTML = '<option value="">Select category</option>';
                result.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.cat_id;
                    option.textContent = category.cat_name;
                    categorySelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    /**
     * Populate brand dropdown
     */
    async function populateBrandDropdown() {
        const brandSelect = document.getElementById('product_brand');
        if (!brandSelect) return;
        
        try {
            // Use seller-scoped brands endpoint
            const response = await fetch('../actions/fetch_brand_action.php');
            const result = await response.json();
            
            if (result.success && result.brands) {
                brandSelect.innerHTML = '<option value="">Select brand (optional)</option>';
                result.brands.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.brand_id;
                    option.textContent = brand.brand_name;
                    brandSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading brands:', error);
        }
    }

    /**
     * Show success message using SweetAlert
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
     * Show loading state
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

    /**
     * Reset form to add mode
     */
    function resetForm() {
        const form = document.getElementById('productForm');
        if (form) {
            form.reset();
            document.getElementById('productId').value = '';
            editingProductId = null;
            document.getElementById('submitBtn').textContent = 'Add Product';
            document.getElementById('cancelBtn').style.display = 'none';
            document.getElementById('imagePreview').innerHTML = '';
        }
    }

    /**
     * Load product data into form for editing
     */
    function loadProductForEdit(product) {
        editingProductId = product.product_id;
        document.getElementById('productId').value = product.product_id;
        document.getElementById('product_title').value = product.product_title || '';
        document.getElementById('product_price').value = product.product_price || '';
        document.getElementById('product_cat').value = product.product_cat || '';
        document.getElementById('product_brand').value = product.product_brand || '';
        document.getElementById('product_condition').value = product.product_condition || 'good';
        document.getElementById('product_keywords').value = product.product_keywords || '';
        document.getElementById('product_desc').value = product.product_desc || '';
        document.getElementById('submitBtn').textContent = 'Update Product';
        document.getElementById('cancelBtn').style.display = 'inline-flex';
        
        // Show existing image if available
        if (product.product_image) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = `<img src="../${product.product_image}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;" />`;
        }
        
        // Scroll to form
        document.querySelector('.section-header').scrollIntoView({ behavior: 'smooth' });
    }

    /**
     * Handle form submission (Add/Update)
     */
    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate form
            const title = document.getElementById('product_title').value;
            const price = document.getElementById('product_price').value;
            const category = document.getElementById('product_cat').value;
            const imageFile = document.getElementById('product_image').files[0];

            const titleValidation = validateProductTitle(title);
            if (!titleValidation.isValid) {
                showError(titleValidation.message);
                return;
            }

            const priceValidation = validateProductPrice(price);
            if (!priceValidation.isValid) {
                showError(priceValidation.message);
                return;
            }

            const categoryValidation = validateProductCategory(category);
            if (!categoryValidation.isValid) {
                showError(categoryValidation.message);
                return;
            }

            // Check if image is required (for new products)
            if (!editingProductId && !imageFile) {
                showError('Product image is required.');
                return;
            }

            const loadingSwal = showLoading();
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;

            try {
                if (editingProductId) {
                    // Update existing product
                    const formData = new FormData(productForm);
                    formData.append('product_id', editingProductId);

                    const response = await fetch('../actions/update_product_action.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (loadingSwal) Swal.close();

                    if (result.success) {
                        showSuccess(result.message || 'Product updated successfully!');
                        resetForm();
                        await loadProducts();
                    } else {
                        showError(result.message || 'Failed to update product.');
                    }
                } else {
                    // Add new product
                    const formData = new FormData(productForm);

                    // First, add the product
                    const addResponse = await fetch('../actions/add_product_action.php', {
                        method: 'POST',
                        body: formData
                    });

                    const addResult = await addResponse.json();

                    if (!addResult.success) {
                        if (loadingSwal) Swal.close();
                        showError(addResult.message || 'Failed to add product.');
                        submitBtn.disabled = false;
                        return;
                    }

                    const newProductId = addResult.product_id;

                    // Then, upload image if provided
                    if (imageFile) {
                        const imageFormData = new FormData();
                        imageFormData.append('product_id', newProductId);
                        imageFormData.append('product_image', imageFile);

                        const imageResponse = await fetch('../actions/upload_product_image_action.php', {
                            method: 'POST',
                            body: imageFormData
                        });

                        const imageResult = await imageResponse.json();

                        if (!imageResult.success) {
                            console.warn('Product added but image upload failed:', imageResult.message);
                        }
                    }

                    if (loadingSwal) Swal.close();
                    showSuccess(addResult.message || 'Product added successfully!');
                    resetForm();
                    await loadProducts();
                }
            } catch (error) {
                if (loadingSwal) Swal.close();
                console.error('Error:', error);
                showError('An error occurred. Please try again.');
            } finally {
                submitBtn.disabled = false;
            }
        });
    }

    /**
     * Cancel edit mode
     */
    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            resetForm();
        });
    }

    /**
     * Image preview
     */
    const productImageInput = document.getElementById('product_image');
    if (productImageInput) {
        productImageInput.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '8px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    /**
     * Render products organized by category and brand
     */
    function renderProducts(organized) {
        const display = document.getElementById('productsDisplay');
        if (!display) return;

        if (!organized || Object.keys(organized).length === 0) {
            display.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <div class="empty-state-text">No products yet. Add your first product above.</div>
                </div>
            `;
            return;
        }

        let html = '<div class="products-organized">';
        
        for (const catId in organized) {
            const category = organized[catId];
            html += `<div class="category-section">`;
            html += `<div class="category-header">${escapeHtml(category.cat_name)}</div>`;
            
            for (const brandId in category.brands) {
                const brand = category.brands[brandId];
                html += `<div class="brand-section">`;
                html += `<div class="brand-header">${escapeHtml(brand.brand_name)}</div>`;
                html += `<div class="products-grid">`;
                
                brand.products.forEach(product => {
                    const imageUrl = product.product_image ? `../${product.product_image}` : '../assets/images/landback.jpg';
                    html += `
                        <div class="product-card">
                            <img src="${imageUrl}" alt="${escapeHtml(product.product_title)}" class="product-image" onerror="this.src='../assets/images/landback.jpg'" />
                            <div class="product-title">${escapeHtml(product.product_title)}</div>
                            <div class="product-price">₵${parseFloat(product.product_price).toFixed(2)}</div>
                            <div class="product-actions">
                                <button class="btn-edit" onclick="editProduct(${product.product_id})">Edit</button>
                                <button class="btn-delete" onclick="deleteProduct(${product.product_id})">Delete</button>
                            </div>
                        </div>
                    `;
                });
                
                html += `</div></div>`;
            }
            
            html += `</div>`;
        }
        
        html += '</div>';
        display.innerHTML = html;
    }

    /**
     * Load products from server
     */
    async function loadProducts() {
        try {
            const response = await fetch('../actions/get_products_action.php');
            const result = await response.json();
            
            if (result.success && result.organized) {
                renderProducts(result.organized);
            } else {
                renderProducts({});
            }
        } catch (error) {
            console.error('Error loading products:', error);
            renderProducts({});
        }
    }

    /**
     * Edit product handler (called from onclick)
     */
    window.editProduct = async function(productId) {
        try {
            // Fetch product details
            const response = await fetch(`../actions/get_product_action.php?product_id=${productId}`);
            const result = await response.json();
            
            if (result.success && result.product) {
                loadProductForEdit(result.product);
            } else {
                showError('Failed to load product details.');
            }
        } catch (error) {
            console.error('Error loading product:', error);
            showError('An error occurred while loading product details.');
        }
    };

    /**
     * Delete product handler (called from onclick)
     */
    window.deleteProduct = async function(productId) {
        if (typeof Swal !== 'undefined') {
            const confirmed = await Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'This will permanently delete the product. This action cannot be undone.',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#D32F2F',
                cancelButtonColor: '#6B6B6B'
            });

            if (!confirmed.isConfirmed) {
                return;
            }
        } else {
            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }
        }

        const loadingSwal = showLoading();

        try {
            const formData = new FormData();
            formData.append('product_id', productId);

            const response = await fetch('../actions/delete_product_action.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (loadingSwal) Swal.close();

            if (result.success) {
                showSuccess(result.message || 'Product deleted successfully!');
                await loadProducts();
            } else {
                showError(result.message || 'Failed to delete product.');
            }
        } catch (error) {
            if (loadingSwal) Swal.close();
            console.error('Error deleting product:', error);
            showError('An error occurred while deleting the product.');
        }
    };

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize on page load
    populateCategoryDropdown();
    populateBrandDropdown();
    loadProducts();
});

