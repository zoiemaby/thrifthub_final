<?php
/**
 * Fetch Brands Action
 * ThriftHub - Fetch Brands Handler
 *
 * Returns brands created by the logged-in admin user as JSON.
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Please log in.'
    ]);
    exit;
}

$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_SELLER) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Admin access required.'
    ]);
    exit;
}

$controller = new BrandController();
$userId = (int)$_SESSION['user_id'];

// Get brands for the logged-in user
$result = $controller->getBrandsByUser($userId);

http_response_code(200);
echo json_encode($result);
exit;

