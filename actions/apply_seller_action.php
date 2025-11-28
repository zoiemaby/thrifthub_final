<?php

/**
 * Apply Seller Action
 * ThriftHub - Seller Application Submission Handler
 * 
 * Handles seller verification documentation and creates CSV file
 * All uploads go to uploads/u{user_id}/ subdirectories
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/sellerApplication_controller.php';

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.txt');

header('Content-Type: application/json; charset=utf-8');

// Log the request
file_put_contents(__DIR__ . '/../debug_log.txt', date('Y-m-d H:i:s') . " - Request received\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../debug_log.txt', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/../debug_log.txt', "FILES data: " . print_r($_FILES, true) . "\n", FILE_APPEND);

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to apply as a seller.'
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

$userId = (int)$_SESSION['user_id'];
$controller = new SellerApplicationController();

// Check if user already has an application
$existingApp = $controller->get_application_by_user_ctrl($userId);
if ($existingApp['success'] && in_array($existingApp['application']['status'], ['pending', 'approved'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'You have already applied to become a seller.'
    ]);
    exit;
}

// Validate text fields
$momoNumber = isset($_POST['momo_number']) ? trim($_POST['momo_number']) : '';
$bankName = isset($_POST['bank_name']) ? trim($_POST['bank_name']) : '';
$accountNumber = isset($_POST['account_number']) ? trim($_POST['account_number']) : '';

if (empty($momoNumber) || empty($accountNumber)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Mobile money number and account number are required.'
    ]);
    exit;
}

// Validate uploaded files
$requiredFiles = ['id_path', 'address_path', 'selfie_path'];
foreach ($requiredFiles as $fileKey) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Please upload all required documents: ID, proof of address, and selfie with ID."
        ]);
        exit;
    }
}

// Define upload directory structure
$userDir = __DIR__ . "/../uploads/u{$userId}";
$docsDir = "{$userDir}/docs";

// Create directories if they don't exist
if (!is_dir($userDir)) {
    mkdir($userDir, 0777, true);
}
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0777, true);
}

// Function to validate file type (image or PDF)
function validateFileType($file)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    return in_array($mimeType, $allowedTypes);
}

// Function to generate safe filename
function generateSafeFilename($originalName)
{
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $ext;
}

// Upload files and store paths
$uploadedFiles = [];
try {
    foreach ($requiredFiles as $fileKey) {
        $file = $_FILES[$fileKey];

        // Validate file type
        if (!validateFileType($file)) {
            throw new Exception("Invalid file type for {$fileKey}. Only images and PDFs are allowed.");
        }

        // Generate safe filename
        $safeFilename = generateSafeFilename($file['name']);
        $destination = "{$docsDir}/{$safeFilename}";

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to upload {$fileKey}.");
        }

        // Store relative path from project root
        $uploadedFiles[$fileKey] = "uploads/u{$userId}/docs/{$safeFilename}";
    }

    // Create CSV file: uploads/u{user_id}/documentation.csv
    $csvPath = "{$userDir}/documentation.csv";
    $csvHandle = fopen($csvPath, 'w');

    if (!$csvHandle) {
        throw new Exception("Failed to create documentation CSV file.");
    }

    // Write CSV header
    $headers = ['id_path', 'address_path', 'selfie_path', 'momo_number', 'bank_name', 'account_number'];
    fputcsv($csvHandle, $headers);

    // Write CSV data row
    $data = [
        $uploadedFiles['id_path'],
        $uploadedFiles['address_path'],
        $uploadedFiles['selfie_path'],
        $momoNumber,
        $bankName,
        $accountNumber
    ];
    fputcsv($csvHandle, $data);
    fclose($csvHandle);

    // Store relative path to CSV
    $csvRelativePath = "uploads/u{$userId}/documentation.csv";

    // Create seller application in database
    file_put_contents(__DIR__ . '/../debug_log.txt', "Creating application for user: $userId with CSV: $csvRelativePath\n", FILE_APPEND);
    $result = $controller->create_seller_application_ctrl($userId, $csvRelativePath);

    file_put_contents(__DIR__ . '/../debug_log.txt', "Application creation result: " . print_r($result, true) . "\n", FILE_APPEND);

    if ($result['success']) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => $result['message'] ?? 'Application submitted successfully! Please wait for admin approval.',
            'application_id' => $result['application_id'] ?? null
        ]);
    } else {
        // Clean up uploaded files if DB insert fails
        foreach ($uploadedFiles as $filePath) {
            $fullPath = __DIR__ . "/../{$filePath}";
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        if (file_exists($csvPath)) {
            unlink($csvPath);
        }

        http_response_code(400);
        echo json_encode($result);
    }
} catch (Exception $e) {
    // Clean up any uploaded files on error
    foreach ($uploadedFiles as $filePath) {
        $fullPath = __DIR__ . "/../{$filePath}";
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit;