<?php
/**
 * Get Seller Products
 * ThriftHub - Fetch products for logged-in seller
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

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
$controller = new ProductController();

try {
    // Get all products for this seller
    $result = $controller->getAllProducts(['seller_id' => $userId]);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'products' => $result['products'],
            'count' => $result['count']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch products.',
            'products' => []
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching products.',
        'error' => $e->getMessage()
    ]);
}
exit;
