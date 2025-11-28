<?php
/**
 * Checkout Page
 * ThriftHub - Checkout View
 * 
 * Displays checkout summary and payment simulation
 */

require_once __DIR__ . '/../settings/core.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Checkout — ThriftHub</title>
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
      --beige: #F6F2EA;
      --white: #FFFFFF;
      --text-dark: #2C2C2C;
      --text-muted: #6B6B6B;
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
    }

    .logo {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      font-weight: 700;
      color: var(--thrift-green);
      text-decoration: none;
    }

    .main-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 40px;
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

    .page-title {
      font-size: 36px;
      font-weight: 800;
      background: var(--gradient-green);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 32px;
    }

    .checkout-content {
      display: grid;
      grid-template-columns: 1fr 400px;
      gap: 40px;
    }

    @media (max-width: 968px) {
      .checkout-content {
        grid-template-columns: 1fr;
      }
    }

    .checkout-items {
      background: var(--white);
      border-radius: 20px;
      padding: 32px;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
    }

    .checkout-item {
      display: grid;
      grid-template-columns: 100px 1fr auto;
      gap: 20px;
      padding: 20px 0;
      border-bottom: 1px solid var(--border);
    }

    .checkout-item:last-child {
      border-bottom: none;
    }

    .checkout-item-image img {
      width: 100%;
      height: 100px;
      object-fit: cover;
      border-radius: 12px;
    }

    .checkout-item-details h4 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .checkout-item-meta {
      display: flex;
      gap: 16px;
      font-size: 14px;
      color: var(--text-muted);
    }

    .checkout-item-subtotal {
      font-size: 18px;
      font-weight: 700;
      color: var(--thrift-green);
      text-align: right;
    }

    .checkout-summary {
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

    .summary-total {
      display: flex;
      justify-content: space-between;
      margin-top: 24px;
      padding-top: 24px;
      border-top: 2px solid var(--border);
      font-size: 28px;
      font-weight: 800;
      color: var(--thrift-green);
    }

    .payment-btn {
      width: 100%;
      padding: 18px;
      background: var(--gradient-green);
      color: var(--white);
      border: none;
      border-radius: 12px;
      font-size: 18px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 24px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: var(--shadow-md);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .payment-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .payment-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .simulate-btn {
      background: var(--text-muted) !important;
      font-size: 14px;
      text-transform: none;
      letter-spacing: 0;
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="header-content">
      <a href="../index.php" class="logo">ThriftHub</a>
    </div>
  </header>

  <div class="main-container">
    <a href="cart.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
      Back to Cart
    </a>

    <h1 class="page-title">Checkout</h1>

    <div class="checkout-content">
      <div class="checkout-items">
        <h2 style="margin-bottom: 24px; font-size: 20px; font-weight: 700;">Order Items</h2>
        <div id="checkoutSummary"></div>
      </div>

      <div class="checkout-summary">
        <h2 class="summary-title">Order Summary</h2>
        <div class="summary-total">
          <span>Total</span>
          <span id="checkoutTotal">₵0.00</span>
        </div>
        <?php if (isset($_SESSION['customer_email'])): ?>
          <input type="hidden" id="customerEmail" value="<?php echo htmlspecialchars($_SESSION['customer_email']); ?>">
        <?php endif; ?>
        <button class="payment-btn paystack-btn" onclick="initiatePaystackPayment()" id="paystackBtn">
          <i class="fas fa-credit-card"></i> Pay with Paystack
        </button>
        <button class="payment-btn simulate-btn" onclick="showPaymentModal()" style="margin-top: 12px; background: var(--text-muted);">
          <i class="fas fa-flask"></i> Simulate Payment (Test)
        </button>
      </div>
    </div>
  </div>

  <script src="../assets/js/checkout.js"></script>
</body>
</html>

