<?php
/**
 * Get Products Action
 * ThriftHub - Get Products Handler
 *
 * Returns products organized by category and brand for the logged-in seller
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

$controller = new ProductController();

// Get products organized by category and brand
$result = $controller->getProductsOrganized();

http_response_code(200);
echo json_encode($result);
exit;

