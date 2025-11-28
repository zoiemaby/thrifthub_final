<?php
/**
 * Upload Product Image Action
 * ThriftHub - Product Image Upload Handler
 * 
 * This script handles product image uploads and stores them in the structure:
 * uploads/u{user_id}/p{product_id}/image_1.png
 * 
 * Important: All uploads must go to the uploads/ folder only.
 */

require_once __DIR__ . '/../settings/core.php';

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

// Validate required parameters
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Product ID is required.'
    ]);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$productId = (int)$_POST['product_id'];

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid product ID.'
    ]);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No image file uploaded or upload error occurred.'
    ]);
    exit;
}

$file = $_FILES['product_image'];

// Validate file type (only images)
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type. Only image files (JPEG, PNG, GIF, WebP) are allowed.'
    ]);
    exit;
}

// Validate file size (max 5MB)
$maxSize = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'File size exceeds maximum allowed size of 5MB.'
    ]);
    exit;
}

// Define upload directory structure: uploads/u{user_id}/p{product_id}/
$baseUploadDir = __DIR__ . '/../uploads';
$userDir = "{$baseUploadDir}/u{$userId}";
$productDir = "{$userDir}/p{$productId}";

// Verify that the path is within the uploads directory (security check)
$realBasePath = realpath($baseUploadDir);
$realTargetPath = realpath($productDir);

if ($realBasePath === false) {
    // Base directory doesn't exist, create it
    if (!mkdir($baseUploadDir, 0777, true)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create uploads directory.'
        ]);
        exit;
    }
    $realBasePath = realpath($baseUploadDir);
}

// Create directories if they don't exist
if (!is_dir($userDir)) {
    if (!mkdir($userDir, 0777, true)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create user directory.'
        ]);
        exit;
    }
}

if (!is_dir($productDir)) {
    if (!mkdir($productDir, 0777, true)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create product directory.'
        ]);
        exit;
    }
}

// Verify the target path is within the uploads directory (prevent directory traversal)
$realTargetPath = realpath($productDir);
if ($realTargetPath === false || strpos($realTargetPath, $realBasePath) !== 0) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid upload path. Uploads must be stored in the uploads/ directory only.'
    ]);
    exit;
}

// Count existing images in the directory to determine image number
$existingImages = glob("{$productDir}/image_*.{png,jpg,jpeg,gif,webp}", GLOB_BRACE);
$imageNumber = count($existingImages) + 1;

// Generate safe filename: image_{number}.{extension}
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
// Normalize extension based on mime type
if ($mimeType === 'image/jpeg' || $mimeType === 'image/jpg') {
    $extension = 'jpg';
} elseif ($mimeType === 'image/png') {
    $extension = 'png';
} elseif ($mimeType === 'image/gif') {
    $extension = 'gif';
} elseif ($mimeType === 'image/webp') {
    $extension = 'webp';
}

$filename = "image_{$imageNumber}.{$extension}";
$destination = "{$productDir}/{$filename}";

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save uploaded file.'
    ]);
    exit;
}

// Store relative path from project root
$relativePath = "uploads/u{$userId}/p{$productId}/{$filename}";

// Update product_image field in database (store the first image path, or update if needed)
require_once __DIR__ . '/../controllers/product_controller.php';
$controller = new ProductController();

// Get current product to check if it already has an image
$productResult = $controller->getProduct($productId);
if ($productResult['success']) {
    $currentImage = $productResult['product']['product_image'] ?? '';
    
    // If this is the first image, set it as the main product image
    if (empty($currentImage)) {
        $updateResult = $controller->updateProduct($productId, [
            'product_image' => $relativePath,
            'seller_id' => $userId
        ]);
        
        if (!$updateResult['success']) {
            // Image uploaded but database update failed
            error_log("Failed to update product_image in database for product {$productId}");
        }
    }
}

// Return success response
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Image uploaded successfully.',
    'image_path' => $relativePath,
    'image_url' => '../' . $relativePath
]);

exit;

