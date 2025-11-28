/**
 * Cart JavaScript
 * ThriftHub - Cart UI Interactions
 * 
 * Handles all cart UI interactions and communicates with backend
 */

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    updateCartBadge();
});

/**
 * Load cart items from server
 */
async function loadCart() {
    try {
        const response = await fetch('../actions/get_cart_action.php');
        const result = await response.json();
        
        if (result.success) {
            renderCart(result.items, result.total);
        } else {
            showError('Failed to load cart items.');
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        showError('Error loading cart. Please try again.');
    }
}

/**
 * Render cart items
 */
function renderCart(items, total) {
    const cartContainer = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    
    if (!items || items.length === 0) {
        if (cartContainer) cartContainer.innerHTML = '';
        if (emptyCartMessage) emptyCartMessage.style.display = 'block';
        if (cartTotal) cartTotal.textContent = '₵0.00';
        return;
    }
    
    if (emptyCartMessage) emptyCartMessage.style.display = 'none';
    
    if (cartContainer) {
        cartContainer.innerHTML = items.map(item => {
            const imageUrl = item.product_image ? `../${item.product_image}` : '../assets/images/landback.jpg';
            const subtotal = parseFloat(item.product_price) * parseInt(item.qty);
            
            return `
                <div class="cart-item" data-cart-id="${item.cart_id}">
                    <div class="cart-item-image">
                        <img src="${imageUrl}" alt="${escapeHtml(item.product_title)}" onerror="this.src='../assets/images/landback.jpg'">
                    </div>
                    <div class="cart-item-details">
                        <h3 class="cart-item-title">${escapeHtml(item.product_title)}</h3>
                        <div class="cart-item-price">₵${parseFloat(item.product_price).toFixed(2)}</div>
                    </div>
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="updateQuantity(${item.cart_id}, ${parseInt(item.qty) - 1})">-</button>
                        <input type="number" class="qty-input" value="${item.qty}" min="1" 
                               onchange="updateQuantity(${item.cart_id}, this.value)" 
                               data-cart-id="${item.cart_id}">
                        <button class="qty-btn" onclick="updateQuantity(${item.cart_id}, ${parseInt(item.qty) + 1})">+</button>
                    </div>
                    <div class="cart-item-subtotal">
                        <div class="subtotal-amount">₵${subtotal.toFixed(2)}</div>
                    </div>
                    <div class="cart-item-actions">
                        <button class="remove-btn" onclick="removeFromCart(${item.cart_id})" title="Remove item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    if (cartTotal) {
        cartTotal.textContent = `₵${total.toFixed(2)}`;
    }
}

/**
 * Add product to cart
 */
async function addToCart(productId, quantity = 1) {
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        
        const response = await fetch('../actions/add_to_cart_action.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message || 'Product added to cart!');
            updateCartBadge();
            // Reload cart if on cart page
            if (document.getElementById('cartItems')) {
                loadCart();
            }
        } else {
            showError(result.message || 'Failed to add product to cart.');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showError('Error adding product to cart. Please try again.');
    }
}

/**
 * Remove item from cart
 */
async function removeFromCart(cartId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('cart_id', cartId);
        
        const response = await fetch('../actions/remove_from_cart_action.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message || 'Item removed from cart.');
            loadCart();
            updateCartBadge();
        } else {
            showError(result.message || 'Failed to remove item from cart.');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showError('Error removing item from cart. Please try again.');
    }
}

/**
 * Update quantity
 */
async function updateQuantity(cartId, quantity) {
    quantity = parseInt(quantity);
    
    if (quantity < 1) {
        quantity = 1;
    }
    
    try {
        const formData = new FormData();
        formData.append('cart_id', cartId);
        formData.append('quantity', quantity);
        
        const response = await fetch('../actions/update_quantity_action.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadCart(); // Reload to get updated totals
            updateCartBadge();
        } else {
            showError(result.message || 'Failed to update quantity.');
            loadCart(); // Reload to reset display
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        showError('Error updating quantity. Please try again.');
        loadCart(); // Reload to reset display
    }
}

/**
 * Empty cart
 */
async function emptyCart() {
    if (!confirm('Are you sure you want to empty your cart? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('../actions/empty_cart_action.php', {
            method: 'POST'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess(result.message || 'Cart emptied successfully.');
            loadCart();
            updateCartBadge();
        } else {
            showError(result.message || 'Failed to empty cart.');
        }
    } catch (error) {
        console.error('Error emptying cart:', error);
        showError('Error emptying cart. Please try again.');
    }
}

/**
 * Update cart badge in header
 */
async function updateCartBadge() {
    try {
        const response = await fetch('../actions/get_cart_action.php');
        const result = await response.json();
        
        if (result.success) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                const count = result.count || 0;
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        }
    } catch (error) {
        console.error('Error updating cart badge:', error);
    }
}

/**
 * Show success message
 */
function showSuccess(message) {
    // Use SweetAlert if available, otherwise alert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        alert(message);
    }
}

/**
 * Show error message
 */
function showError(message) {
    // Use SweetAlert if available, otherwise alert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    } else {
        alert(message);
    }
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

