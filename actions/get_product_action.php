<?php
/**
 * Get Product Action
 * ThriftHub - Get Single Product Handler
 *
 * Returns a single product by ID for editing
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

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

$controller = new ProductController();
$result = $controller->getProduct($productId);

http_response_code($result['success'] ? 200 : 404);
echo json_encode($result);
exit;

