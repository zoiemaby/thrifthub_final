<?php
/**
 * Delete Brand Action
 * ThriftHub - Brand Deletion Handler
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

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
if ($roleNo !== ROLE_ADMIN && $roleNo !== ROLE_SELLER) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Seller or admin access required.'
    ]);
    exit;
}

$controller = new BrandController();
$brandId = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;

// Get user_id from session
$userId = (int)$_SESSION['user_id'];

$result = $controller->deleteBrand($brandId, ['user_id' => $userId]);
http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
exit;

