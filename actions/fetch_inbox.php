<?php
/**
 * Fetch Inbox Action
 * Returns all conversations for the logged-in user
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/chat_controller.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$userId = (int)$_SESSION['user_id'];

$controller = new ChatController();
$result = $controller->get_user_inbox_ctrl($userId);

echo json_encode($result);
?>
