<?php
/**
 * Paystack Initialize Transaction Action
 * ThriftHub - Initialize Paystack Payment
 * 
 * Handles the initialization of Paystack payment transactions
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/paystack_config.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'Please log in to proceed with payment.'
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

try {
    $userId = (int)$_SESSION['user_id'];
    
    // Get email from request or session
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    // If email not provided, try to get from session
    if (empty($email) && isset($_SESSION['customer_email'])) {
        $email = $_SESSION['customer_email'];
    }
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Valid email address is required.'
        ]);
        exit;
    }
    
    // Get cart total
    $cartController = new CartController();
    $cartData = [
        'user_id' => $userId,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    $cartResult = $cartController->getUserCartCtrl($cartData);
    
    if (!$cartResult['success'] || empty($cartResult['items']) || $cartResult['total'] <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Your cart is empty or invalid.'
        ]);
        exit;
    }
    
    $amountGHS = (float)$cartResult['total'];
    
    // Validate amount
    if ($amountGHS <= 0) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid amount.'
        ]);
        exit;
    }
    
    // Convert GHS to pesewas (multiply by 100)
    $amountKobo = (int) round($amountGHS * 100);
    
    // Generate unique transaction reference
    // Format: TH-{customer_id}-{timestamp}
    $reference = 'TH-' . $userId . '-' . time() . '-' . mt_rand(1000, 9999);
    
    // Initialize Paystack transaction
    $paystackResponse = paystack_initialize_transaction(
        $email,
        $amountKobo,
        $reference,
        PAYSTACK_CALLBACK_URL
    );
    
    if ($paystackResponse['status'] === true && isset($paystackResponse['data']['authorization_url'])) {
        // Store reference in session for verification
        $_SESSION['paystack_reference'] = $reference;
        $_SESSION['paystack_expected_amount'] = $amountGHS;
        
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'authorization_url' => $paystackResponse['data']['authorization_url'],
            'reference' => $reference,
            'access_code' => $paystackResponse['data']['access_code'] ?? null
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $paystackResponse['message'] ?? 'Failed to initialize payment. Please try again.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Paystack init transaction error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while initializing payment. Please try again.'
    ]);
}

exit;

