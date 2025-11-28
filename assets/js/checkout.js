/**
 * Checkout JavaScript
 * ThriftHub - Checkout and Payment Processing
 * 
 * Manages the simulated payment modal and checkout flow
 */

let cartData = null;

// Load cart data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCartForCheckout();
});

/**
 * Load cart data for checkout
 */
async function loadCartForCheckout() {
    try {
        const response = await fetch('../actions/get_cart_action.php');
        const result = await response.json();
        
        if (result.success && result.items && result.items.length > 0) {
            cartData = result;
            renderCheckoutSummary(result.items, result.total);
        } else {
            // Redirect to cart if empty
            window.location.href = 'cart.php';
        }
    } catch (error) {
        console.error('Error loading cart:', error);
        showError('Error loading cart. Please try again.');
    }
}

/**
 * Render checkout summary
 */
function renderCheckoutSummary(items, total) {
    const summaryContainer = document.getElementById('checkoutSummary');
    const totalElement = document.getElementById('checkoutTotal');
    
    if (summaryContainer) {
        summaryContainer.innerHTML = items.map(item => {
            const imageUrl = item.product_image ? `../${item.product_image}` : '../assets/images/landback.jpg';
            const subtotal = parseFloat(item.product_price) * parseInt(item.qty);
            
            return `
                <div class="checkout-item">
                    <div class="checkout-item-image">
                        <img src="${imageUrl}" alt="${escapeHtml(item.product_title)}" onerror="this.src='../assets/images/landback.jpg'">
                    </div>
                    <div class="checkout-item-details">
                        <h4>${escapeHtml(item.product_title)}</h4>
                        <div class="checkout-item-meta">
                            <span>Quantity: ${item.qty}</span>
                            <span>₵${parseFloat(item.product_price).toFixed(2)} each</span>
                        </div>
                    </div>
                    <div class="checkout-item-subtotal">
                        ₵${subtotal.toFixed(2)}
                    </div>
                </div>
            `;
        }).join('');
    }
    
    if (totalElement) {
        totalElement.textContent = `₵${total.toFixed(2)}`;
    }
}

/**
 * Initiate Paystack payment
 */
async function initiatePaystackPayment() {
    console.log('initiatePaystackPayment called', { cartData });
    
    if (!cartData || !cartData.items || cartData.items.length === 0) {
        showError('Cart is empty. Please add items to your cart first.');
        return;
    }
    
    const total = cartData.total;
    const paystackBtn = document.getElementById('paystackBtn');
    
    // Disable button during processing
    if (paystackBtn) {
        paystackBtn.disabled = true;
        paystackBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    }
    
    try {
        // Show loading
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Initializing Payment...',
                text: 'Please wait while we connect to Paystack.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Get email - prompt if not available
        let email = '';
        
        // Try to get email from a hidden input or prompt
        const emailInput = document.getElementById('customerEmail');
        if (emailInput) {
            email = emailInput.value;
        }
        
        // If no email, prompt user
        if (!email || !isValidEmail(email)) {
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Enter Your Email',
                    input: 'email',
                    inputLabel: 'Email address for payment receipt',
                    inputPlaceholder: 'your.email@example.com',
                    inputValidator: (value) => {
                        if (!value || !isValidEmail(value)) {
                            return 'Please enter a valid email address';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    confirmButtonColor: '#0F5E4D',
                    cancelButtonText: 'Cancel'
                });
                
                if (result.isDismissed || !result.value || !isValidEmail(result.value)) {
                    if (paystackBtn) {
                        paystackBtn.disabled = false;
                        paystackBtn.innerHTML = '<i class="fas fa-credit-card"></i> Pay with Paystack';
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.close();
                    }
                    return;
                }
                
                email = result.value;
            } else {
                email = prompt('Please enter your email address for payment receipt:');
                if (!email || !isValidEmail(email)) {
                    showError('Valid email address is required.');
                    if (paystackBtn) {
                        paystackBtn.disabled = false;
                        paystackBtn.innerHTML = '<i class="fas fa-credit-card"></i> Pay with Paystack';
                    }
                    return;
                }
            }
        }
        
        // Ensure we have email at this point
        if (!email || !isValidEmail(email)) {
            showError('Valid email address is required.');
            if (paystackBtn) {
                paystackBtn.disabled = false;
                paystackBtn.innerHTML = '<i class="fas fa-credit-card"></i> Pay with Paystack';
            }
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            return;
        }
        
        // Call Paystack init transaction
        const formData = new FormData();
        formData.append('email', email);
        
        const response = await fetch('../actions/paystack_init_transaction.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success' && result.authorization_url) {
            // Close any open modals
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
            
            // Redirect to Paystack
            window.location.href = result.authorization_url;
        } else {
            const errorMsg = result.message || 'Failed to initialize payment. Please try again.';
            console.error('Paystack init error:', result);
            showError(errorMsg);
            if (paystackBtn) {
                paystackBtn.disabled = false;
                paystackBtn.innerHTML = '<i class="fas fa-credit-card"></i> Pay with Paystack';
            }
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        }
    } catch (error) {
        console.error('Error initiating Paystack payment:', error);
        showError('An error occurred while initializing payment. Please try again.');
        if (paystackBtn) {
            paystackBtn.disabled = false;
            paystackBtn.innerHTML = '<i class="fas fa-credit-card"></i> Pay with Paystack';
        }
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }
}

// Make function globally accessible
window.initiatePaystackPayment = initiatePaystackPayment;

// Also attach event listener as backup
document.addEventListener('DOMContentLoaded', function() {
    const paystackBtn = document.getElementById('paystackBtn');
    if (paystackBtn) {
        // Remove inline onclick and use event listener
        paystackBtn.removeAttribute('onclick');
        paystackBtn.addEventListener('click', function(e) {
            e.preventDefault();
            initiatePaystackPayment();
        });
    }
});

/**
 * Validate email address
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Show payment modal
 */
function showPaymentModal() {
    if (!cartData || !cartData.items || cartData.items.length === 0) {
        showError('Cart is empty. Please add items to your cart first.');
        return;
    }
    
    const total = cartData.total;
    
    // Use SweetAlert for payment modal
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Simulate Payment',
            html: `
                <div style="text-align: left; padding: 20px;">
                    <p style="font-size: 18px; margin-bottom: 20px;"><strong>Total Amount: ₵${total.toFixed(2)}</strong></p>
                    <p style="margin-bottom: 15px;">This is a simulated payment. Click "Yes, I've paid" to complete the order.</p>
                    <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; margin-top: 20px;">
                        <p style="margin: 0; font-size: 14px; color: #666;">💡 This is a demo. No actual payment will be processed.</p>
                    </div>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: "Yes, I've paid",
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0F5E4D',
            cancelButtonColor: '#6B6B6B',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                processCheckout();
            }
        });
    } else {
        // Fallback to confirm dialog
        if (confirm(`Total Amount: ₵${total.toFixed(2)}\n\nSimulate payment? Click OK to complete order.`)) {
            processCheckout();
        }
    }
}

/**
 * Process checkout
 */
async function processCheckout() {
    try {
        // Show loading
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your order.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        const formData = new FormData();
        formData.append('payment_method', 'momo'); // Default payment method
        
        const response = await fetch('../actions/process_checkout_action.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Order Placed Successfully!',
                    html: `
                        <div style="text-align: left; padding: 20px;">
                            <p><strong>Order Reference:</strong> ${result.order_reference}</p>
                            <p><strong>Order ID:</strong> ${result.order_id}</p>
                            <p><strong>Total Amount:</strong> ₵${result.total_amount.toFixed(2)}</p>
                            <p><strong>Items:</strong> ${result.items_count}</p>
                        </div>
                    `,
                    confirmButtonText: 'View Orders',
                    confirmButtonColor: '#0F5E4D'
                }).then(() => {
                    // Redirect to view orders page
                    window.location.href = '../view/view_orders.php';
                });
            } else {
                alert(`Order placed successfully!\nOrder Reference: ${result.order_reference}\nOrder ID: ${result.order_id}`);
                window.location.href = '../view/view_orders.php';
            }
        } else {
            showError(result.message || 'Failed to process checkout. Please try again.');
        }
    } catch (error) {
        console.error('Error processing checkout:', error);
        showError('An error occurred during checkout. Please try again.');
    }
}

/**
 * Show error message
 */
function showError(message) {
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

