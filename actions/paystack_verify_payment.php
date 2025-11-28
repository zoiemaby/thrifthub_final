<?php
/**
 * Paystack Verify Payment Action
 * ThriftHub - Verify Paystack Payment and Create Order
 * 
 * Verifies payment with Paystack and creates order if successful
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/paystack_config.php';
require_once __DIR__ . '/../controllers/order_controller.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../classes/order_class.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please log in to verify payment.'
    ]);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed.'
    ]);
    exit;
}

// Get reference from POST
$reference = isset($_POST['reference']) ? trim($_POST['reference']) : '';
$expectedAmount = isset($_POST['expected_amount']) ? (float)$_POST['expected_amount'] : null;

if (empty($reference)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment reference is required.'
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];

try {
    // Verify payment with Paystack
    $paystackResponse = paystack_verify_transaction($reference);
    
    if ($paystackResponse['status'] !== true) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $paystackResponse['message'] ?? 'Payment verification failed.'
        ]);
        exit;
    }
    
    $transactionData = $paystackResponse['data'];
    
    // Check if transaction was successful
    if ($transactionData['status'] !== 'success') {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Payment was not successful. Status: ' . $transactionData['status']
        ]);
        exit;
    }
    
    // Verify amount (convert from pesewas to GHS)
    $paidAmountGHS = (float)($transactionData['amount'] / 100);
    
    // Get expected amount from session or recalculate from cart
    if ($expectedAmount === null) {
        $cartController = new CartController();
        $cartData = [
            'user_id' => $userId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        $cartResult = $cartController->getUserCartCtrl($cartData);
        $expectedAmount = $cartResult['total'] ?? 0;
    }
    
    // Allow small rounding differences (0.01 GHS)
    $amountDifference = abs($paidAmountGHS - $expectedAmount);
    if ($amountDifference > 0.01) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Payment amount mismatch. Expected: ₵' . number_format($expectedAmount, 2) . ', Paid: ₵' . number_format($paidAmountGHS, 2)
        ]);
        exit;
    }
    
    // Verify currency
    if ($transactionData['currency'] !== 'GHS') {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid currency. Expected GHS.'
        ]);
        exit;
    }
    
    // Get cart items before creating order
    $cartController = new CartController();
    $cartData = [
        'user_id' => $userId,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    $cartResult = $cartController->getUserCartCtrl($cartData);
    
    if (!$cartResult['success'] || empty($cartResult['items'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Cart is empty. Cannot create order.'
        ]);
        exit;
    }
    
    $cartItems = $cartResult['items'];
    
    // Note: We'll use the existing methods which may not support transactions
    // In production, you might want to add transaction support to the Database class
    
    // Create order
    $orderController = new OrderController();
    $orderData = [
        'customer_id' => $userId,
        'total_amount' => $paidAmountGHS,
        'order_status' => 'paid' // Payment already successful
    ];
    
    $orderResult = $orderController->createOrderCtrl($orderData);
    
    if (!$orderResult['success']) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to create order: ' . ($orderResult['message'] ?? 'Unknown error')
        ]);
        exit;
    }
    
    $orderId = $orderResult['order_id'];
    
    // Add order details
    foreach ($cartItems as $item) {
        $detailData = [
            'order_id' => $orderId,
            'product_id' => $item['product_id'],
            'quantity' => $item['qty'],
            'price' => $item['product_price']
        ];
        
        $detailResult = $orderController->addOrderDetailsCtrl($detailData);
        
        if (!$detailResult['success']) {
            error_log("Failed to add order detail for order $orderId, product {$item['product_id']}");
            // Continue with other items
        }
    }
    
    // Get payment channel and authorization code from Paystack response
    $paymentChannel = $transactionData['channel'] ?? 'card';
    $authorizationCode = isset($transactionData['authorization']['authorization_code']) 
        ? $transactionData['authorization']['authorization_code'] 
        : null;
    
    // Map Paystack channel to our payment method enum
    $paymentMethod = 'card'; // Default
    if (strpos(strtolower($paymentChannel), 'mobile') !== false || strpos(strtolower($paymentChannel), 'momo') !== false) {
        $paymentMethod = 'momo';
    } elseif (strpos(strtolower($paymentChannel), 'card') !== false) {
        $paymentMethod = 'card';
    } elseif (strpos(strtolower($paymentChannel), 'bank') !== false) {
        $paymentMethod = 'bank_transfer';
    }
    
    // Record payment
    $paymentData = [
        'amount' => $paidAmountGHS,
        'customer_id' => $userId,
        'order_id' => $orderId,
        'payment_method' => $paymentMethod,
        'payment_status' => 'successful',
        'transaction_ref' => $reference,
        'currency' => 'GHS'
    ];
    
    $paymentResult = $orderController->recordPaymentCtrl($paymentData);
    
    if (!$paymentResult['success']) {
        error_log("Failed to record payment for order $orderId");
        // Order is already created, so we'll still return success but log the error
    }
    
    // Empty the cart
    $emptyResult = $cartController->emptyCartCtrl($cartData);
    
    if (!$emptyResult['success']) {
        error_log("Failed to empty cart for user $userId");
        // Non-critical error, continue
    }
    
    // Clear Paystack session data
    unset($_SESSION['paystack_reference']);
    unset($_SESSION['paystack_expected_amount']);
    
    // Return success
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Payment verified and order created successfully.',
        'order_id' => $orderId,
        'payment_reference' => $reference,
        'amount' => $paidAmountGHS,
        'items_count' => count($cartItems)
    ]);
    
} catch (Exception $e) {
    error_log("Paystack verify payment error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while verifying payment. Please contact support.'
    ]);
}

exit;

