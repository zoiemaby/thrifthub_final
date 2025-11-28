<?php
/**
 * Approve Seller Action
 * ThriftHub - Admin Seller Approval Handler
 * 
 * Approves pending seller application:
 * - Creates/updates sellers table record
 * - Sets verified = 1
 * - Updates user_role to 3 (seller)
 * - Marks application as approved
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/sellerApplication_controller.php';
require_once __DIR__ . '/../controllers/seller_controller.php';
require_once __DIR__ . '/../settings/db_class.php';

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
        'message' => 'Only administrators can approve seller applications.'
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

$appController = new SellerApplicationController();
$sellerController = new SellerController();

// Fetch application details
$appResult = $appController->get_application_by_id_ctrl($applicationId);
if (!$appResult['success']) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Application not found.'
    ]);
    exit;
}

$application = $appResult['application'];
$applicantUserId = (int)$application['user_id'];

// Check if application is still pending
if ($application['status'] !== 'pending') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Application has already been processed.'
    ]);
    exit;
}

try {
    // Check if seller profile exists
    $existingSeller = $sellerController->get_seller_by_user_id_ctrl($applicantUserId);
    
    if (!$existingSeller['success']) {
        // Create new seller profile with minimal data (to be filled by seller later)
        $createResult = $sellerController->create_seller_ctrl(
            $applicantUserId,
            'New Store',  // Placeholder, seller will update
            1,            // Default type_id, seller will update
            1,            // Default sector_id, seller will update
            null,         // No logo yet
            null,         // No banner yet
            ''            // No description yet
        );
        
        if (!$createResult['success']) {
            throw new Exception('Failed to create seller profile.');
        }
    }
    
    // Set seller as verified
    $verifyResult = $sellerController->set_seller_verified_ctrl($applicantUserId, 1);
    if (!$verifyResult['success']) {
        throw new Exception('Failed to verify seller.');
    }
    
    // Update application status to approved
    $approveResult = $appController->update_application_status_ctrl(
        $applicationId,
        'approved',
        $adminUserId
    );
    
    if (!$approveResult['success']) {
        throw new Exception('Failed to update application status.');
    }
    
    // Update user_role to 3 (seller) if currently customer (role_no 2)
    $db = new Database();
    $updateRoleSql = "UPDATE users SET user_role = 3 WHERE user_id = $applicantUserId AND user_role = 2";
    $db->query($updateRoleSql);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Seller application approved successfully.'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;
