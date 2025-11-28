<?php
/**
 * View Orders Page
 * ThriftHub - Customer Orders View
 * 
 * Displays all orders for the logged-in customer
 */

require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>My Orders — ThriftHub</title>
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

    .nav-links {
      display: flex;
      align-items: center;
      gap: 24px;
      list-style: none;
    }

    .nav-links a {
      color: var(--text-dark);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .nav-links a:hover {
      color: var(--thrift-green);
    }

    .header-icons {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .header-icon {
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      background: var(--white);
      color: var(--thrift-green);
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      font-size: 18px;
      border: 2px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .header-icon:hover {
      background: var(--gradient-green);
      color: var(--white);
      border-color: var(--thrift-green);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
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

    /* Orders List */
    .orders-container {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .order-card {
      background: var(--white);
      border-radius: 20px;
      padding: 32px;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
      transition: all 0.3s ease;
    }

    .order-card:hover {
      box-shadow: var(--shadow-lg);
      transform: translateY(-2px);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 2px solid var(--border);
    }

    .order-info {
      flex: 1;
    }

    .order-id {
      font-size: 18px;
      font-weight: 700;
      color: var(--thrift-green);
      margin-bottom: 8px;
    }

    .order-date {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 4px;
    }

    .order-reference {
      font-size: 13px;
      color: var(--text-light);
      font-family: monospace;
      background: var(--beige);
      padding: 4px 12px;
      border-radius: 8px;
      display: inline-block;
    }

    .order-status {
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .order-status.pending {
      background: #FFF3CD;
      color: #856404;
    }

    .order-status.paid {
      background: #D1ECF1;
      color: #0C5460;
    }

    .order-status.shipped {
      background: #D4EDDA;
      color: #155724;
    }

    .order-status.completed {
      background: #D1ECF1;
      color: #0C5460;
    }

    .order-status.cancelled {
      background: #F8D7DA;
      color: #721C24;
    }

    .order-items {
      margin-bottom: 24px;
    }

    .order-item {
      display: grid;
      grid-template-columns: 80px 1fr auto;
      gap: 20px;
      padding: 16px;
      background: var(--beige);
      border-radius: 12px;
      margin-bottom: 12px;
      align-items: center;
    }

    .order-item-image {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      background: var(--white);
    }

    .order-item-details {
      flex: 1;
    }

    .order-item-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 4px;
    }

    .order-item-meta {
      font-size: 13px;
      color: var(--text-muted);
    }

    .order-item-price {
      font-size: 18px;
      font-weight: 700;
      color: var(--thrift-green);
      text-align: right;
    }

    .order-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 20px;
      border-top: 2px solid var(--border);
    }

    .order-total {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
    }

    .order-total-label {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 4px;
    }

    .order-total-amount {
      font-size: 28px;
      font-weight: 800;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .payment-info {
      font-size: 13px;
      color: var(--text-muted);
      margin-top: 8px;
    }

    .empty-state {
      text-align: center;
      padding: 80px 20px;
      background: var(--white);
      border-radius: 20px;
      box-shadow: var(--shadow-md);
    }

    .empty-state-icon {
      font-size: 64px;
      color: var(--text-light);
      margin-bottom: 24px;
    }

    .empty-state-title {
      font-size: 24px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .empty-state-text {
      font-size: 16px;
      color: var(--text-muted);
      margin-bottom: 32px;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      background: var(--gradient-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-md);
      cursor: pointer;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .loading {
      text-align: center;
      padding: 80px 20px;
      color: var(--text-muted);
      font-size: 18px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 20px;
    }

    .loading::after {
      content: '';
      width: 40px;
      height: 40px;
      border: 4px solid var(--border);
      border-top-color: var(--thrift-green);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
      .order-header {
        flex-direction: column;
        gap: 16px;
      }

      .order-item {
        grid-template-columns: 60px 1fr;
        gap: 12px;
      }

      .order-item-price {
        grid-column: 1 / -1;
        text-align: left;
        margin-top: 8px;
      }

      .order-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
      }

      .order-total {
        align-items: flex-start;
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="header-content">
      <a href="../index.php" class="logo">ThriftHub</a>
      <ul class="nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="browse_products.php">Shop</a></li>
        <li><a href="about.php">About Us</a></li>
      </ul>
      <div class="header-icons">
        <a href="cart.php" class="header-icon" title="View Cart">
          <i class="fas fa-shopping-bag"></i>
        </a>
        <a href="browse_products.php" class="header-icon" title="Browse Products">
          <i class="fas fa-store"></i>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Container -->
  <div class="main-container">
    <a href="browse_products.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
      Back to Shop
    </a>
    
    <div class="page-header">
      <h1 class="page-title">My Orders</h1>
    </div>

    <div id="ordersContainer">
      <div class="loading">Loading your orders...</div>
    </div>
  </div>

  <script>
    async function loadOrders() {
      try {
        const response = await fetch('../actions/get_customer_orders_action.php');
        const result = await response.json();
        
        if (result.success && result.orders && result.orders.length > 0) {
          renderOrders(result.orders);
        } else {
          renderEmptyState();
        }
      } catch (error) {
        console.error('Error loading orders:', error);
        document.getElementById('ordersContainer').innerHTML = `
          <div class="empty-state">
            <div class="empty-state-icon">⚠️</div>
            <h2 class="empty-state-title">Error Loading Orders</h2>
            <p class="empty-state-text">An error occurred while loading your orders. Please try again later.</p>
            <button class="btn-primary" onclick="loadOrders()">
              <i class="fas fa-redo"></i>
              Retry
            </button>
          </div>
        `;
      }
    }

    function renderOrders(orders) {
      const container = document.getElementById('ordersContainer');
      
      container.innerHTML = `
        <div class="orders-container">
          ${orders.map(order => renderOrder(order)).join('')}
        </div>
      `;
    }

    function renderOrder(order) {
      const orderDate = new Date(order.order_date);
      const formattedDate = orderDate.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
      
      const statusClass = order.order_status || 'pending';
      const statusLabel = (order.order_status || 'pending').charAt(0).toUpperCase() + (order.order_status || 'pending').slice(1);
      
      const items = order.items || [];
      const totalAmount = parseFloat(order.total_amount || 0);
      
      const paymentInfo = order.payment ? `
        <div class="payment-info">
          <i class="fas fa-credit-card"></i> 
          Paid via ${order.payment.payment_method || 'Mobile Money'} 
          ${order.payment.transaction_ref ? `(${order.payment.transaction_ref})` : ''}
        </div>
      ` : '';

      return `
        <div class="order-card">
          <div class="order-header">
            <div class="order-info">
              <div class="order-id">Order</div>
              <div class="order-date">
                <i class="fas fa-calendar"></i> ${formattedDate}
              </div>
              ${order.payment && order.payment.transaction_ref ? `
                <div class="order-reference">Ref: ${escapeHtml(order.payment.transaction_ref)}</div>
              ` : ''}
            </div>
            <div class="order-status ${statusClass}">${statusLabel}</div>
          </div>
          
          <div class="order-items">
            ${items.map(item => renderOrderItem(item)).join('')}
          </div>
          
          <div class="order-footer">
            <div>
              ${paymentInfo}
            </div>
            <div class="order-total">
              <div class="order-total-label">Total Amount</div>
              <div class="order-total-amount">₵${totalAmount.toFixed(2)}</div>
            </div>
          </div>
        </div>
      `;
    }

    function renderOrderItem(item) {
      const imageUrl = item.product_image ? `../${item.product_image}` : '../assets/images/landback.jpg';
      const itemTotal = parseFloat(item.price || 0) * parseInt(item.qty || 1);
      
      return `
        <div class="order-item">
          <img src="${imageUrl}" alt="${escapeHtml(item.product_title || 'Product')}" 
               class="order-item-image" 
               onerror="this.src='../assets/images/landback.jpg'">
          <div class="order-item-details">
            <div class="order-item-title">${escapeHtml(item.product_title || 'Product')}</div>
            <div class="order-item-meta">
              Quantity: ${item.qty || 1} × ₵${parseFloat(item.price || 0).toFixed(2)}
              ${item.cat_name ? ` • ${escapeHtml(item.cat_name)}` : ''}
              ${item.brand_name ? ` • ${escapeHtml(item.brand_name)}` : ''}
            </div>
          </div>
          <div class="order-item-price">₵${itemTotal.toFixed(2)}</div>
        </div>
      `;
    }

    function renderEmptyState() {
      document.getElementById('ordersContainer').innerHTML = `
        <div class="empty-state">
          <div class="empty-state-icon">📦</div>
          <h2 class="empty-state-title">No Orders Yet</h2>
          <p class="empty-state-text">You haven't placed any orders yet. Start shopping to see your orders here!</p>
          <a href="browse_products.php" class="btn-primary">
            <i class="fas fa-shopping-bag"></i>
            Start Shopping
          </a>
        </div>
      `;
    }

    function escapeHtml(text) {
      if (!text) return '';
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      loadOrders();
    });
  </script>
</body>
</html>

