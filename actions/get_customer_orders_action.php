<?php
/**
 * Get Customer Orders Action
 * ThriftHub - Fetch customer orders
 * 
 * Returns all orders for the logged-in customer
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/order_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to view your orders.']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

try {
    $orderController = new OrderController();
    
    // Get customer orders
    $result = $orderController->getPastOrdersCtrl($userId);
    
    if ($result['success']) {
        // Enhance orders with payment info and order details
        $orders = $result['orders'];
        $enhancedOrders = [];
        
        require_once __DIR__ . '/../classes/order_class.php';
        $orderClass = new Order();
        
        foreach ($orders as $order) {
            $orderId = $order['order_id'];
            $fullOrder = $orderClass->getOrderById($orderId, $userId);
            
            if ($fullOrder) {
                $enhancedOrders[] = $fullOrder;
            } else {
                // Fallback to basic order info
                $enhancedOrders[] = $order;
            }
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'orders' => $enhancedOrders
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message'] ?? 'Failed to fetch orders.'
        ]);
    }
} catch (Exception $e) {
    error_log("Get customer orders error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching orders.'
    ]);
}

exit;

