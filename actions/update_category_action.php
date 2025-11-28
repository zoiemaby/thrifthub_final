<?php
/**
 * Update Category Action
 * ThriftHub - Category Update Handler
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to update categories.']);
    exit;
}

// Check if user is admin or seller
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_ADMIN && $roleNo !== ROLE_SELLER) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only administrators and sellers can update categories.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Add user_id from session to POST data for ownership verification
$_POST['user_id'] = (int)$_SESSION['user_id'];

$controller = new CategoryController();
$catId = $_POST['cat_id'] ?? null;
$result = $controller->updateCategory($catId, $_POST);

http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
exit;
