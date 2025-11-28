<?php
/**
 * Get Brands for Product Form
 * ThriftHub - Get Brands for Product Dropdown
 *
 * Returns all brands for product form (accessible to logged-in users)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to access brands.'
    ]);
    exit;
}

$controller = new BrandController();

// Get all brands (for product form, sellers need to see all brands)
$result = $controller->getAllBrands();

http_response_code(200);
echo json_encode($result);
exit;

