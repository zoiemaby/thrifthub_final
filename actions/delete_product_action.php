<?php
/**
 * Delete Product Action
 * ThriftHub - Product Deletion Handler
 */

// Start output buffering to prevent any accidental output
ob_start();

// Suppress errors that might break JSON response
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// Clear any previous output
ob_clean();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Please log in.'
    ]);
    exit;
}

$controller = new ProductController();
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$result = $controller->deleteProduct($productId, ['seller_id' => $userId]);

http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
exit;

