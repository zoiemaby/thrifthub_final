<?php
/**
 * Get Cart Action
 * ThriftHub - Get User Cart Handler
 * 
 * Returns user's cart items
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json; charset=utf-8');

$controller = new CartController();

// Get user identification
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

$data = [
    'user_id' => $userId,
    'ip_address' => $ipAddress
];

$result = $controller->getUserCartCtrl($data);

http_response_code(200);
echo json_encode($result);
exit;

