<?php
/**
 * Reject Seller Action
 * ThriftHub - Admin Seller Rejection Handler
 * 
 * Rejects pending seller application:
 * - Updates application status to rejected
 * - Does NOT create seller profile
 * - Does NOT change user role
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/sellerApplication_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in.'
    ]);
    exit;
}

$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_ADMIN) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Only administrators can reject seller applications.'
    ]);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

$adminUserId = (int)$_SESSION['user_id'];
$applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;

if ($applicationId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid application ID.'
    ]);
    exit;
}

$controller = new SellerApplicationController();

// Fetch application details
$appResult = $controller->get_application_by_id_ctrl($applicationId);
if (!$appResult['success']) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Application not found.'
    ]);
    exit;
}

$application = $appResult['application'];

// Check if application is still pending
if ($application['status'] !== 'pending') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Application has already been processed.'
    ]);
    exit;
}

// Update application status to rejected
$result = $controller->update_application_status_ctrl(
    $applicationId,
    'rejected',
    $adminUserId
);

if ($result['success']) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Seller application rejected.'
    ]);
} else {
    http_response_code(400);
    echo json_encode($result);
}

exit;
