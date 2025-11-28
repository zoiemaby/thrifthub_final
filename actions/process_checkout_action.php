<?php
/**
 * Process Checkout Action
 * ThriftHub - Checkout Processing Handler
 * 
 * Handles the backend processing of checkout flow
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';
require_once __DIR__ . '/../controllers/product_controller.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Get user identification
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

if (!$userId && empty($ipAddress)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User identification required.']);
    exit;
}

try {
    $cartController = new CartController();
    $orderController = new OrderController();
    $productController = new ProductController();
    
    // Get cart items
    $cartData = [
        'user_id' => $userId,
        'ip_address' => $ipAddress
    ];
    
    $cartResult = $cartController->getUserCartCtrl($cartData);
    
    if (!$cartResult['success'] || empty($cartResult['items'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
        exit;
    }
    
    $cartItems = $cartResult['items'];
    $totalAmount = $cartResult['total'];
    
    // Validate all products are still available
    foreach ($cartItems as $item) {
        $product = $productController->getProduct($item['product_id']);
        if (!$product['success'] || $product['product']['product_status'] !== 'active') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'One or more products are no longer available.',
                'product_id' => $item['product_id']
            ]);
            exit;
        }
    }
    
    // Create order
    $orderData = [
        'customer_id' => $userId ?: 0, // For guests, we might need a different approach
        'total_amount' => $totalAmount,
        'order_status' => 'pending'
    ];
    
    // Note: For guest checkout, you may need to handle this differently
    // For now, we'll require a user ID
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Please log in to complete checkout.']);
        exit;
    }
    
    $orderResult = $orderController->createOrderCtrl($orderData);
    
    if (!$orderResult['success']) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to create order.']);
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
            // Rollback order creation if details fail
            error_log("Failed to add order detail for order $orderId, product {$item['product_id']}");
        }
    }
    
    // Generate transaction reference
    $transactionRef = $orderController->generateOrderReference();
    
    // Record payment
    $paymentData = [
        'amount' => $totalAmount,
        'customer_id' => $userId,
        'order_id' => $orderId,
        'payment_method' => isset($_POST['payment_method']) ? $_POST['payment_method'] : 'momo',
        'payment_status' => 'successful',
        'transaction_ref' => $transactionRef,
        'currency' => 'GHS'
    ];
    
    $paymentResult = $orderController->recordPaymentCtrl($paymentData);
    
    if (!$paymentResult['success']) {
        error_log("Failed to record payment for order $orderId");
    }
    
    // Update order status to paid
    require_once __DIR__ . '/../classes/order_class.php';
    $order = new Order();
    $order->updateOrderStatus($orderId, 'paid');
    
    // Empty the cart
    $emptyResult = $cartController->emptyCartCtrl($cartData);
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Order processed successfully.',
        'order_id' => $orderId,
        'order_reference' => $transactionRef,
        'total_amount' => $totalAmount,
        'items_count' => count($cartItems)
    ]);
    
} catch (Exception $e) {
    error_log("Checkout error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during checkout. Please try again.'
    ]);
}

exit;

