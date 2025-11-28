<?php
/**
 * Update Quantity Action
 * ThriftHub - Update Cart Item Quantity Handler
 * 
 * Processes quantity update requests
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

// Get POST data
$cartId = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

if ($cartId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid cart ID.']);
    exit;
}

$data = [
    'user_id' => $userId,
    'ip_address' => $ipAddress,
    'quantity' => $quantity
];

$result = $controller->updateCartItemCtrl($cartId, $data);

http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
exit;

