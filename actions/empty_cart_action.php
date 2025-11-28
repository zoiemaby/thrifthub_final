<?php
/**
 * Empty Cart Action
 * ThriftHub - Empty Cart Handler
 * 
 * Processes empty cart requests
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$controller = new CartController();

// Get user identification
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

$data = [
    'user_id' => $userId,
    'ip_address' => $ipAddress
];

$result = $controller->emptyCartCtrl($data);

http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
exit;

