<?php
/**
 * Add to Cart Action
 * ThriftHub - Add Product to Cart Handler
 * 
 * Processes add to cart requests
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$controller = new CartController();

// Get user identification
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

// Get POST data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    exit;
}

$data = [
    'product_id' => $productId,
    'user_id' => $userId,
    'ip_address' => $ipAddress,
    'quantity' => $quantity
];

$result = $controller->addToCartCtrl($data);

http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
exit;

