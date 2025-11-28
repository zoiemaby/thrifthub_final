<?php
/**
 * Update Order Status
 * ThriftHub - Update order status (for sellers)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/order_class.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
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

// Check if user is a seller
$roleNo = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;
if ($roleNo !== ROLE_SELLER) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Seller access required.'
    ]);
    exit;
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid order ID.'
    ]);
    exit;
}

if (empty($status)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Status is required.'
    ]);
    exit;
}

$order = new Order();
$result = $order->updateOrderStatus($orderId, $status);

http_response_code($result['success'] ? 200 : 400);
echo json_encode($result);
exit;
