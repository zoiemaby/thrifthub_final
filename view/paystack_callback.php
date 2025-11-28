<?php
/**
 * Paystack Callback Page
 * ThriftHub - Paystack Payment Callback Handler
 * 
 * Handles the redirect from Paystack after payment
 */

require_once __DIR__ . '/../settings/core.php';

// Get reference from query parameter
$reference = isset($_GET['reference']) ? trim($_GET['reference']) : '';

if (empty($reference)) {
    header('Location: payment_success.php?error=no_reference');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Verifying Payment — ThriftHub</title>
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

    .verification-container {
      background: var(--white);
      border-radius: 20px;
      padding: 60px 40px;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
      text-align: center;
      max-width: 500px;
      width: 100%;
    }

    .spinner {
      width: 60px;
      height: 60px;
      border: 4px solid var(--border);
      border-top-color: var(--thrift-green);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto 24px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .verification-title {
      font-size: 24px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 12px;
    }

    .verification-text {
      font-size: 16px;
      color: var(--text-muted);
      margin-bottom: 32px;
    }

    .reference-code {
      font-family: monospace;
      font-size: 14px;
      color: var(--thrift-green);
      background: var(--beige);
      padding: 8px 16px;
      border-radius: 8px;
      display: inline-block;
      margin-top: 16px;
    }
  </style>
</head>
<body>
  <div class="verification-container">
    <div class="spinner"></div>
    <h1 class="verification-title">Verifying Payment...</h1>
    <p class="verification-text">Please wait while we verify your payment with Paystack.</p>
    <div class="reference-code">Ref: <?php echo htmlspecialchars($reference); ?></div>
  </div>

  <script>
    // Automatically verify payment on page load
    document.addEventListener('DOMContentLoaded', function() {
      verifyPayment();
    });

    async function verifyPayment() {
      const reference = '<?php echo addslashes($reference); ?>';
      
      try {
        const formData = new FormData();
        formData.append('reference', reference);
        
        // Include expected amount from session if available
        <?php if (isset($_SESSION['paystack_expected_amount'])): ?>
        formData.append('expected_amount', <?php echo (float)$_SESSION['paystack_expected_amount']; ?>);
        <?php endif; ?>
        
        const response = await fetch('../actions/paystack_verify_payment.php', {
          method: 'POST',
          body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
          // Redirect to success page
          window.location.href = `payment_success.php?order_id=${result.order_id}&ref=${encodeURIComponent(result.payment_reference)}`;
        } else {
          // Redirect to error page or show error
          window.location.href = `payment_success.php?error=${encodeURIComponent(result.message || 'Payment verification failed')}`;
        }
      } catch (error) {
        console.error('Error verifying payment:', error);
        window.location.href = `payment_success.php?error=${encodeURIComponent('An error occurred while verifying payment')}`;
      }
    }
  </script>
</body>
</html>

