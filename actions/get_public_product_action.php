<?php
/**
 * Get Public Product Action
 * ThriftHub - Get Single Product for Public Viewing
 * 
 * Returns a single product by ID (no login required)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

header('Content-Type: application/json; charset=utf-8');

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

if ($result['success']) {
    // Check if product is active
    if (isset($result['product']['product_status']) && $result['product']['product_status'] !== 'active') {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Product not found or not available.'
        ]);
        exit;
    }
}

http_response_code($result['success'] ? 200 : 404);
echo json_encode($result);
exit;

