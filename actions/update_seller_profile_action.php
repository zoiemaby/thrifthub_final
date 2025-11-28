<?php
/**
 * Update Seller Profile Action
 * ThriftHub - Seller Profile and Store Branding Handler
 * 
 * Handles store logo/banner uploads and profile updates
 * All uploads go to uploads/u{user_id}/store/
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/seller_controller.php';
require_once __DIR__ . '/../controllers/sellerApplication_controller.php';

header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in.'
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];

// Check if user is a seller (role 3) OR has a pending/approved seller application
$roleNo = null;
if (isset($_SESSION['user_role_no'])) {
    $roleNo = (int)$_SESSION['user_role_no'];
} elseif (isset($_SESSION['user_role'])) {
    $roleCandidate = $_SESSION['user_role'];
    if (is_numeric($roleCandidate)) {
        $roleNo = (int)$roleCandidate;
    } else {
        $roleNo = function_exists('getRoleNumber') ? getRoleNumber($roleCandidate) : 0;
    }
}
$isSeller = ($roleNo === (defined('ROLE_SELLER') ? ROLE_SELLER : 3));

// If not a seller, check if they have a pending or approved application
if (!$isSeller) {
    $appController = new SellerApplicationController();
    $existingApp = $appController->get_application_by_user_ctrl($userId);
    
    if (!$existingApp['success'] || !in_array($existingApp['application']['status'], ['pending', 'approved'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
            'message' => 'You must be a seller or have a pending/approved seller application to update store profiles.'
    ]);
    exit;
    }
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

$controller = new SellerController();

// Validate required fields
$shopName = isset($_POST['shop_name']) ? trim($_POST['shop_name']) : '';
$typeId = isset($_POST['type_id']) ? (int)$_POST['type_id'] : 0;
$sectorId = isset($_POST['sector_id']) ? (int)$_POST['sector_id'] : 0;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

if (empty($shopName) || $typeId <= 0 || $sectorId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Shop name, business type, and sector are required.'
    ]);
    exit;
}

// Define upload directory structure
$userDir = __DIR__ . "/../uploads/u{$userId}";
$storeDir = "{$userDir}/store";

// Create directories if they don't exist
if (!is_dir($userDir)) {
    mkdir($userDir, 0777, true);
}
if (!is_dir($storeDir)) {
    mkdir($storeDir, 0777, true);
}

// Function to validate image file
function validateImageFile($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    return in_array($mimeType, $allowedTypes);
}

// Function to generate safe filename with prefix
function generateImageFilename($prefix, $originalName) {
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return $prefix . '_' . uniqid() . '.' . $ext;
}

// Handle logo upload
$logoPath = null;
if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] === UPLOAD_ERR_OK) {
    $logoFile = $_FILES['store_logo'];
    
    // Validate file type
    if (!validateImageFile($logoFile)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Store logo must be an image file.'
        ]);
        exit;
    }
    
    // Generate safe filename
    $logoFilename = generateImageFilename('logo', $logoFile['name']);
    $logoDestination = "{$storeDir}/{$logoFilename}";
    
    // Move uploaded file
    if (move_uploaded_file($logoFile['tmp_name'], $logoDestination)) {
        $logoPath = "uploads/u{$userId}/store/{$logoFilename}";
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to upload store logo.'
        ]);
        exit;
    }
}

// Handle banner upload
$bannerPath = null;
if (isset($_FILES['store_banner']) && $_FILES['store_banner']['error'] === UPLOAD_ERR_OK) {
    $bannerFile = $_FILES['store_banner'];
    
    // Validate file type
    if (!validateImageFile($bannerFile)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Store banner must be an image file.'
        ]);
        exit;
    }
    
    // Generate safe filename
    $bannerFilename = generateImageFilename('banner', $bannerFile['name']);
    $bannerDestination = "{$storeDir}/{$bannerFilename}";
    
    // Move uploaded file
    if (move_uploaded_file($bannerFile['tmp_name'], $bannerDestination)) {
        $bannerPath = "uploads/u{$userId}/store/{$bannerFilename}";
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to upload store banner.'
        ]);
        exit;
    }
}

// Check if seller profile exists
$existingSeller = $controller->get_seller_by_user_id_ctrl($userId);

if ($existingSeller['success']) {
    // Update existing seller profile
    $result = $controller->update_seller_profile_ctrl(
        $userId,
        $shopName,
        $typeId,
        $sectorId,
        $logoPath,
        $bannerPath,
        $description
    );
} else {
    // Create new seller profile
    $result = $controller->create_seller_ctrl(
        $userId,
        $shopName,
        $typeId,
        $sectorId,
        $logoPath,
        $bannerPath,
        $description
    );
}

if ($result['success']) {
    http_response_code(200);
    echo json_encode($result);
} else {
    // Clean up uploaded files if operation fails
    if ($logoPath) {
        $fullLogoPath = __DIR__ . "/../{$logoPath}";
        if (file_exists($fullLogoPath)) {
            unlink($fullLogoPath);
        }
    }
    if ($bannerPath) {
        $fullBannerPath = __DIR__ . "/../{$bannerPath}";
        if (file_exists($fullBannerPath)) {
            unlink($fullBannerPath);
        }
    }
    
    http_response_code(400);
    echo json_encode($result);
}

exit;
