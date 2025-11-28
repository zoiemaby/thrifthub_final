<?php
/**
 * Get Categories Action
 * ThriftHub - Get Categories by User Handler
 *
 * Returns categories created by the logged-in user
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

// Check if user is admin
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_SELLER) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Only administrators can access categories.'
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$controller = new CategoryController();

// Get categories created by this user
$result = $controller->getCategoriesByUser($userId);

http_response_code(200);
echo json_encode($result);
exit;

