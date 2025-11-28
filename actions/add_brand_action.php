<?php
/**
 * Add Brand Action
 * ThriftHub - Brand Creation Handler
 * 
 * This script receives data from the brand creation form,
 * invokes the relevant function from the brand controller,
 * and returns a JSON response to the caller.
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

// Set content type for JSON response
header('Content-Type: application/json; charset=utf-8');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
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

// Initialize the brand controller
$controller = new BrandController();

// Collect and sanitize form data
$brandName = isset($_POST['brand_name']) ? trim($_POST['brand_name']) : '';

// Validate required fields
if (empty($brandName)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Brand name is required.'
    ]);
    exit;
}

// Get user_id from session
$userId = (int)$_SESSION['user_id'];

// Prepare data for controller
$brandData = [
    'brand_name' => $brandName,
    'user_id' => $userId
];

// Call the controller's addBrand method
$result = $controller->addBrand($brandData);

// Return JSON response
if ($result['success']) {
    http_response_code(200);
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode($result);
}

exit;

