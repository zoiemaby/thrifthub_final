<?php
/**
 * Login Customer Action
 * ThriftHub - Customer Login Handler
 * 
 * This script receives data from the customer login form,
 * invokes the relevant function from the customer controller,
 * sets session variables, and returns a message to the caller.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the customer controller
require_once __DIR__ . '/../controllers/customer_controller.php';
require_once __DIR__ . '/../controllers/seller_controller.php';

// Set content type for JSON response (for role-based redirects)
header('Content-Type: application/json; charset=utf-8');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.',
        'role' => null
    ]);
    exit;
}

// Initialize the customer controller
$controller = new CustomerController();

// Collect and sanitize form data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate required fields
if (empty($email)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email address is required.',
        'role' => null
    ]);
    exit;
}

if (empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Password is required.',
        'role' => null
    ]);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format.',
        'role' => null
    ]);
    exit;
}

// Prepare kwargs array for login_customer_ctr method
$kwargs = [
    'email' => $email,
    'password' => $password
];

// Call the controller's login_customer_ctr method
$result = $controller->login_customer_ctr($kwargs);

// Handle the response
if ($result['success']) {
    // Login successful - session variables are already set by the controller
    
    // Debug: Log session data
    error_log("Login success - Session user_role_no: " . ($_SESSION['user_role_no'] ?? 'not set'));
    error_log("Login success - Session is_seller_verified: " . (isset($_SESSION['is_seller_verified']) ? var_export($_SESSION['is_seller_verified'], true) : 'not set'));
    
    // Determine numeric role for client routing
    $roleNo = null;
    if (isset($_SESSION['user_role_no'])) {
        $roleNo = (int)$_SESSION['user_role_no'];
    } elseif (isset($_SESSION['user_role'])) {
        $roleCandidate = $_SESSION['user_role'];
        if (is_numeric($roleCandidate)) {
            $roleNo = (int)$roleCandidate;
        } else {
            // Map string to number if needed
            if (!function_exists('getRoleNumber')) {
                require_once __DIR__ . '/../settings/core.php';
            }
            $roleNo = getRoleNumber($roleCandidate);
        }
    }
    if (!$roleNo) { $roleNo = defined('ROLE_CUSTOMER') ? ROLE_CUSTOMER : 2; }

    // Determine seller verified flag
    $verified = null;
    $roleSeller = defined('ROLE_SELLER') ? ROLE_SELLER : 3;
    if ($roleNo === $roleSeller) {
        if (isset($_SESSION['is_seller_verified'])) {
            $verified = (bool)$_SESSION['is_seller_verified'];
        } else {
            // Fallback to controller lookup
            $sellerController = new SellerController();
            $userId = (int)($_SESSION['user_id'] ?? 0);
            if ($userId > 0) {
                $seller = $sellerController->get_seller_by_user_id_ctrl($userId);
                $verified = ($seller && isset($seller['success']) && $seller['success'] && isset($seller['seller']) && isset($seller['seller']['verified'])) ? (bool)$seller['seller']['verified'] : false;
            } else {
                $verified = false;
            }
        }
    }
    
    // Return success with role information for JavaScript to handle redirect
    http_response_code(200);
    error_log("Login response - Role: $roleNo, Verified: " . var_export($verified, true));
    echo json_encode([
        'success' => true,
        'message' => 'Login successful! Redirecting...',
        'role' => $roleNo,  // 1=admin, 2=customer, 3=seller
        'verified' => $verified
    ]);
} else {
    // Login failed - return JSON error response
    // Use 200 status code but include success: false so JavaScript can parse it
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Invalid email or password.',
        'role' => null
    ]);
}

exit;

