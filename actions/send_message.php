<?php
/**
 * Send Message Action
 * Sends a message in an existing conversation
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/chat_controller.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$conversationId = isset($_POST['conversation_id']) ? (int)$_POST['conversation_id'] : 0;
$messageText = isset($_POST['message_text']) ? trim($_POST['message_text']) : '';
$senderId = (int)$_SESSION['user_id'];

if ($conversationId <= 0 || empty($messageText)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$controller = new ChatController();

// Get conversation to determine receiver
$conversationResult = $controller->get_conversation_ctrl($conversationId, $senderId);

if (!$conversationResult['success']) {
    echo json_encode(['success' => false, 'error' => 'Conversation not found or access denied']);
    exit;
}

$conversation = $conversationResult['conversation'];

// Determine receiver based on sender
if ($senderId == $conversation['buyer_id']) {
    $receiverId = $conversation['seller_id'];
} elseif ($senderId == $conversation['seller_id']) {
    $receiverId = $conversation['buyer_id'];
} else {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Send message
$result = $controller->send_message_ctrl($conversationId, $senderId, $receiverId, $messageText);

echo json_encode($result);
?>
