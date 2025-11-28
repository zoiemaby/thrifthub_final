<?php
/**
 * Update Product Action
 * ThriftHub - Product Update Handler
 * 
 * This script receives data from the product update form,
 * invokes the relevant function from the product controller,
 * and returns a JSON response to the caller.
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

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

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Please log in.'
    ]);
    exit;
}

// Initialize the product controller
$controller = new ProductController();

// Collect and sanitize form data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Validate product ID
if (empty($productId) || $productId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

// Prepare update data (only include fields that are provided)
$productData = [
    'seller_id' => (int)$_SESSION['user_id']
];

if (isset($_POST['product_title'])) {
    $productData['product_title'] = trim($_POST['product_title']);
}

if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {
    $productData['product_cat'] = (int)$_POST['product_cat'];
}

if (isset($_POST['product_brand'])) {
    $productData['product_brand'] = !empty($_POST['product_brand']) ? (int)$_POST['product_brand'] : null;
}

if (isset($_POST['product_price'])) {
    $productData['product_price'] = (float)$_POST['product_price'];
}

if (isset($_POST['product_desc'])) {
    $productData['product_desc'] = trim($_POST['product_desc']);
}

if (isset($_POST['product_keywords'])) {
    $productData['product_keywords'] = trim($_POST['product_keywords']);
}

if (isset($_POST['product_condition'])) {
    $productData['product_condition'] = trim($_POST['product_condition']);
}

// Call the controller's updateProduct method
$result = $controller->updateProduct($productId, $productData);

// Return JSON response
if ($result['success']) {
    http_response_code(200);
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode($result);
}

exit;

