<?php
/**
 * Cart Page
 * ThriftHub - Shopping Cart View
 * 
 * Displays all items in the user's cart
 */

require_once __DIR__ . '/../settings/core.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Shopping Cart — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --thrift-green: #0F5E4D;
      --thrift-green-dark: #0A4538;
      --thrift-green-light: #1A7A66;
      --beige: #F6F2EA;
      --white: #FFFFFF;
      --text-dark: #2C2C2C;
      --text-muted: #6B6B6B;
      --text-light: #9A9A9A;
      --gold: #C9A961;
      --border: #E8E3D8;
      --shadow-sm: 0 2px 8px rgba(15, 94, 77, 0.08);
      --shadow-md: 0 4px 16px rgba(15, 94, 77, 0.12);
      --shadow-lg: 0 8px 32px rgba(15, 94, 77, 0.16);
      --gradient-green: linear-gradient(135deg, #0F5E4D 0%, #1A7A66 100%);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      color: var(--text-dark);
      background: linear-gradient(to bottom, #F6F2EA 0%, #FFFFFF 100%);
      min-height: 100vh;
    }

    /* Header */
    .header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 18px 40px;
      box-shadow: var(--shadow-sm);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--border);
    }

    .header-content {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      font-weight: 700;
      color: var(--thrift-green);
      text-decoration: none;
    }

    /* Main Container */
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px;
    }

    .page-header {
      margin-bottom: 32px;
    }

    .page-title {
      font-size: 36px;
      font-weight: 800;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 8px;
    }

    .back-button {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 12px 24px;
      background: var(--white);
      color: var(--thrift-green);
      border: 2px solid var(--thrift-green);
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
    }

    .back-button:hover {
      background: var(--gradient-green);
      color: var(--white);
      transform: translateX(-4px);
      box-shadow: var(--shadow-md);
    }

    /* Cart Content */
    .cart-content {
      display: grid;
      grid-template-columns: 1fr 400px;
      gap: 40px;
    }

    @media (max-width: 968px) {
      .cart-content {
        grid-template-columns: 1fr;
      }
    }

    .cart-items-section {
      background: var(--white);
      border-radius: 20px;
      padding: 32px;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
    }

    .cart-item {
      display: grid;
      grid-template-columns: 120px 1fr auto auto auto;
      gap: 20px;
      padding: 24px 0;
      border-bottom: 1px solid var(--border);
      align-items: center;
    }

    .cart-item:last-child {
      border-bottom: none;
    }

    .cart-item-image img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 12px;
    }

    .cart-item-title {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .cart-item-meta {
      display: flex;
      gap: 12px;
      margin-bottom: 8px;
    }

    .cart-item-meta span {
      background: var(--beige);
      padding: 4px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
    }

    .cart-item-price {
      font-size: 16px;
      font-weight: 600;
      color: var(--thrift-green);
    }

    .cart-item-quantity {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .qty-btn {
      width: 36px;
      height: 36px;
      border: 2px solid var(--border);
      background: var(--white);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 600;
    }

    .qty-btn:hover {
      border-color: var(--thrift-green);
      background: var(--thrift-green);
      color: var(--white);
    }

    .qty-input {
      width: 60px;
      padding: 8px;
      border: 2px solid var(--border);
      border-radius: 8px;
      text-align: center;
      font-weight: 600;
    }

    .subtotal-amount {
      font-size: 18px;
      font-weight: 700;
      color: var(--thrift-green);
    }

    .remove-btn {
      width: 40px;
      height: 40px;
      border: none;
      background: #ffebee;
      color: #d32f2f;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .remove-btn:hover {
      background: #d32f2f;
      color: var(--white);
    }

    /* Cart Summary */
    .cart-summary {
      background: var(--white);
      border-radius: 20px;
      padding: 32px;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
      height: fit-content;
      position: sticky;
      top: 100px;
    }

    .summary-title {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid var(--border);
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 16px;
      font-size: 16px;
    }

    .summary-total {
      display: flex;
      justify-content: space-between;
      margin-top: 24px;
      padding-top: 24px;
      border-top: 2px solid var(--border);
      font-size: 24px;
      font-weight: 800;
      color: var(--thrift-green);
    }

    .checkout-btn, .continue-btn, .empty-btn {
      width: 100%;
      padding: 16px;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 16px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .checkout-btn {
      background: var(--gradient-green);
      color: var(--white);
      box-shadow: var(--shadow-md);
    }

    .checkout-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .continue-btn {
      background: var(--beige);
      color: var(--thrift-green);
      border: 2px solid var(--thrift-green);
    }

    .continue-btn:hover {
      background: var(--white);
    }

    .empty-btn {
      background: transparent;
      color: #d32f2f;
      border: 2px solid #d32f2f;
    }

    .empty-btn:hover {
      background: #d32f2f;
      color: var(--white);
    }

    /* Empty Cart */
    .empty-cart {
      text-align: center;
      padding: 80px 20px;
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow-md);
    }

    .empty-cart-icon {
      font-size: 64px;
      margin-bottom: 24px;
      opacity: 0.5;
    }

    .empty-cart h2 {
      font-size: 28px;
      margin-bottom: 16px;
      color: var(--text-dark);
    }

    .empty-cart p {
      color: var(--text-muted);
      margin-bottom: 32px;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="header-content">
      <a href="../index.php" class="logo">ThriftHub</a>
      <ul class="nav-links" style="display: flex; align-items: center; gap: 24px; list-style: none;">
        <li><a href="../index.php" style="color: var(--text-dark); text-decoration: none; font-weight: 500;">Home</a></li>
        <li><a href="browse_products.php" style="color: var(--text-dark); text-decoration: none; font-weight: 500;">Shop</a></li>
      </ul>
    </div>
  </header>

  <!-- Main Container -->
  <div class="main-container">
    <a href="browse_products.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
      Continue Shopping
    </a>

    <div class="page-header">
      <h1 class="page-title">Shopping Cart</h1>
    </div>

    <div class="cart-content">
      <div class="cart-items-section">
        <div id="emptyCartMessage" class="empty-cart" style="display: none;">
          <div class="empty-cart-icon">🛒</div>
          <h2>Your cart is empty</h2>
          <p>Add some items to your cart to get started!</p>
          <a href="browse_products.php" class="continue-btn" style="display: inline-block; text-decoration: none; width: auto; padding: 12px 32px;">
            Browse Products
          </a>
        </div>
        <div id="cartItems"></div>
      </div>

      <div class="cart-summary">
        <h2 class="summary-title">Order Summary</h2>
        <div class="summary-row">
          <span>Subtotal</span>
          <span id="cartSubtotal">₵0.00</span>
        </div>
        <div class="summary-row">
          <span>Shipping</span>
          <span>Free</span>
        </div>
        <div class="summary-total">
          <span>Total</span>
          <span id="cartTotal">₵0.00</span>
        </div>
        <button class="checkout-btn" onclick="window.location.href='checkout.php'">
          <i class="fas fa-lock"></i> Proceed to Checkout
        </button>
        <button class="continue-btn" onclick="window.location.href='browse_products.php'">
          Continue Shopping
        </button>
        <button class="empty-btn" onclick="emptyCart()">
          <i class="fas fa-trash"></i> Empty Cart
        </button>
      </div>
    </div>
  </div>

  <script src="../assets/js/cart.js"></script>
  <script>
    // Update subtotal when cart loads
    function updateSubtotal(total) {
      document.getElementById('cartSubtotal').textContent = `₵${total.toFixed(2)}`;
    }

    // Override renderCart to also update subtotal
    const originalRenderCart = window.renderCart;
    window.renderCart = function(items, total) {
      if (originalRenderCart) originalRenderCart(items, total);
      updateSubtotal(total);
    };
  </script>
</body>
</html>

