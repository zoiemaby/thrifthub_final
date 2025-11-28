<?php
/**
 * Fetch Messages Action
 * Returns all messages in a conversation
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/chat_controller.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;
$userId = (int)$_SESSION['user_id'];

if ($conversationId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid conversation ID']);
    exit;
}

$controller = new ChatController();
$result = $controller->get_conversation_messages_ctrl($conversationId, $userId);

echo json_encode($result);
?>
