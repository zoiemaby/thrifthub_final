<?php
/**
 * Get Categories for Product Form
 * ThriftHub - Get Categories for Product Dropdown
 *
 * Returns all categories for product form (accessible to logged-in users)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to access categories.'
    ]);
    exit;
}

$controller = new CategoryController();

// Get all categories (for product form, sellers need to see all categories)
$result = $controller->getAllCategories();

http_response_code(200);
echo json_encode($result);
exit;

