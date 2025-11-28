<?php
/**
 * Add Product Action
 * ThriftHub - Product Creation Handler
 * 
 * This script receives data from the product creation form,
 * invokes the relevant function from the product controller,
 * and returns a JSON response to the caller.
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/seller_controller.php';

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

$userId = (int)$_SESSION['user_id'];
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
$isAdmin = ($roleNo === ROLE_ADMIN);

// Check if user has a seller record
$sellerController = new SellerController();
$sellerResult = $sellerController->get_seller_by_user_id_ctrl($userId);

if (!$sellerResult['success']) {
    // User doesn't have a seller record
    if ($isAdmin) {
        // Admins can add products - create a seller record automatically for testing
        $createResult = $sellerController->create_seller_ctrl(
            $userId,
            'Admin Store',  // Default shop name for admin
            1,              // Default type_id (Individual)
            1,              // Default sector_id (clothing)
            null,           // No logo yet
            null,           // No banner yet
            'Admin account for testing'  // Description
        );
        
        if (!$createResult['success']) {
            // If creation fails, it might already exist, try to get it again
            $sellerResult = $sellerController->get_seller_by_user_id_ctrl($userId);
            if (!$sellerResult['success']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to set up seller account for admin. Please contact system administrator.'
                ]);
                exit;
            }
        }
    } else {
        // Non-admin users need seller record
        // Check if they have an approved seller application
        require_once __DIR__ . '/../controllers/sellerApplication_controller.php';
        $appController = new SellerApplicationController();
        $appResult = $appController->get_application_by_user_ctrl($userId);
        
        if ($appResult['success'] && $appResult['application']['status'] === 'approved') {
            // They have an approved application but no seller record - create one
            $createResult = $sellerController->create_seller_ctrl(
                $userId,
                'My Store',  // Default shop name
                1,            // Default type_id (Individual)
                1,            // Default sector_id (clothing)
                null,         // No logo yet
                null,         // No banner yet
                ''            // No description yet
            );
            
            if (!$createResult['success']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Your seller account is not fully set up. Please complete your seller profile first by visiting the seller dashboard.'
                ]);
                exit;
            }
        } else {
            // No seller record and no approved application
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'You must be an approved seller to add products. Please complete your seller application and wait for approval, or contact an administrator.'
            ]);
            exit;
        }
    }
}

// Initialize the product controller
$controller = new ProductController();

// Collect and sanitize form data
$productData = [
    'product_title' => isset($_POST['product_title']) ? trim($_POST['product_title']) : '',
    'product_cat' => isset($_POST['product_cat']) ? (int)$_POST['product_cat'] : 0,
    'product_brand' => isset($_POST['product_brand']) && !empty($_POST['product_brand']) ? (int)$_POST['product_brand'] : null,
    'product_price' => isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0,
    'product_desc' => isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '',
    'product_keywords' => isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '',
    'product_condition' => isset($_POST['product_condition']) ? trim($_POST['product_condition']) : 'good',
    'seller_id' => $userId
];

// Validate required fields
if (empty($productData['product_title'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Product title is required.'
    ]);
    exit;
}

if (empty($productData['product_cat']) || $productData['product_cat'] <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Product category is required.'
    ]);
    exit;
}

if (empty($productData['product_price']) || $productData['product_price'] <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Valid product price is required.'
    ]);
    exit;
}

// Call the controller's addProduct method
$result = $controller->addProduct($productData);

// Return JSON response
if ($result['success']) {
    http_response_code(200);
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode($result);
}

exit;

