<?php
/**
 * Get Seller Orders
 * ThriftHub - Fetch orders for seller's products
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/order_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Please log in.'
    ]);
    exit;
}

// Check if user is a seller
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_SELLER) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Seller access required.'
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$controller = new OrderController();

try {
    // Get limit and offset from query parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : null;
    
    // Get all orders for this seller's products
    $result = $controller->getSellerOrdersCtrl($userId, $limit, $offset);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'orders' => $result['orders'],
            'count' => $result['count']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch orders.',
            'orders' => []
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching orders.',
        'error' => $e->getMessage()
    ]);
}
exit;
