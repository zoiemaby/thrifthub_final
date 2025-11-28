<?php
/**
 * Payment Success Page
 * ThriftHub - Payment Success Confirmation
 * 
 * Displays success message after verified payment
 */

require_once __DIR__ . '/../settings/core.php';

// Get order ID and reference from query parameters
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$reference = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$error = isset($_GET['error']) ? trim($_GET['error']) : '';

// If there's an error, show error page
$isError = !empty($error) || ($orderId <= 0 && empty($reference));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php echo $isError ? 'Payment Error' : 'Payment Successful'; ?> — ThriftHub</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet" />
  <style>
    :root {
      --thrift-green: #0F5E4D;
      --thrift-green-dark: #0A4538;
      --beige: #F6F2EA;
      --white: #FFFFFF;
      --text-dark: #2C2C2C;
      --text-muted: #6B6B6B;
      --text-light: #9A9A9A;
      --error-red: #D32F2F;
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
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .success-container {
      background: var(--white);
      border-radius: 20px;
      padding: 60px 40px;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
      text-align: center;
      max-width: 600px;
      width: 100%;
    }

    .success-icon {
      width: 80px;
      height: 80px;
      background: var(--gradient-green);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 40px;
      color: var(--white);
    }

    .error-icon {
      width: 80px;
      height: 80px;
      background: var(--error-red);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 40px;
      color: var(--white);
    }

    .success-title {
      font-size: 32px;
      font-weight: 800;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 16px;
    }

    .error-title {
      font-size: 32px;
      font-weight: 800;
      color: var(--error-red);
      margin-bottom: 16px;
    }

    .success-message {
      font-size: 18px;
      color: var(--text-muted);
      margin-bottom: 32px;
      line-height: 1.6;
    }

    .order-details {
      background: var(--beige);
      border-radius: 12px;
      padding: 24px;
      margin-bottom: 32px;
      text-align: left;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-label {
      font-size: 14px;
      color: var(--text-muted);
      font-weight: 500;
    }

    .detail-value {
      font-size: 16px;
      color: var(--text-dark);
      font-weight: 700;
    }

    .button-group {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 28px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-md);
      cursor: pointer;
      border: none;
    }

    .btn-primary {
      background: var(--gradient-green);
      color: var(--white);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .btn-secondary {
      background: var(--white);
      color: var(--thrift-green);
      border: 2px solid var(--thrift-green);
    }

    .btn-secondary:hover {
      background: var(--beige);
      transform: translateY(-2px);
    }

    .reference-code {
      font-family: monospace;
      font-size: 14px;
      color: var(--thrift-green);
      background: var(--white);
      padding: 6px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      display: inline-block;
    }

    @media (max-width: 600px) {
      .success-container {
        padding: 40px 24px;
      }

      .button-group {
        flex-direction: column;
      }

      .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <div class="success-container">
    <?php if ($isError): ?>
      <!-- Error State -->
      <div class="error-icon">
        <i class="fas fa-times"></i>
      </div>
      <h1 class="error-title">Payment Error</h1>
      <p class="success-message">
        <?php echo htmlspecialchars($error ?: 'An error occurred during payment processing.'); ?>
      </p>
      <div class="button-group">
        <a href="checkout.php" class="btn btn-primary">
          <i class="fas fa-arrow-left"></i>
          Back to Checkout
        </a>
        <a href="browse_products.php" class="btn btn-secondary">
          <i class="fas fa-store"></i>
          Continue Shopping
        </a>
      </div>
    <?php else: ?>
      <!-- Success State -->
      <div class="success-icon">
        <i class="fas fa-check"></i>
      </div>
      <h1 class="success-title">Payment Successful!</h1>
      <p class="success-message">
        Thank you for your purchase. Your order has been confirmed and payment has been processed successfully.
      </p>
      
      <div class="order-details">
        <?php if ($orderId > 0): ?>
          <div class="detail-row">
            <span class="detail-label">Order ID:</span>
            <span class="detail-value">#<?php echo $orderId; ?></span>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($reference)): ?>
          <div class="detail-row">
            <span class="detail-label">Payment Reference:</span>
            <span class="detail-value reference-code"><?php echo htmlspecialchars($reference); ?></span>
          </div>
        <?php endif; ?>
        
        <div class="detail-row">
          <span class="detail-label">Payment Date:</span>
          <span class="detail-value"><?php echo date('F j, Y g:i A'); ?></span>
        </div>
      </div>
      
      <div class="button-group">
        <a href="view_orders.php" class="btn btn-primary">
          <i class="fas fa-list"></i>
          View My Orders
        </a>
        <a href="browse_products.php" class="btn btn-secondary">
          <i class="fas fa-store"></i>
          Continue Shopping
        </a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>

