<?php
/**
 * Start Conversation Action
 * Creates or retrieves existing conversation between buyer and seller
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/chat_controller.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: ../view/login.php');
    exit;
}

$buyerId = (int)$_SESSION['user_id'];
$sellerId = isset($_POST['seller_id']) ? (int)$_POST['seller_id'] : 0;
$productId = isset($_POST['product_id']) && !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;

if ($sellerId <= 0) {
    header('Location: ../index.php');
    exit;
}

// Prevent user from messaging themselves
if ($buyerId === $sellerId) {
    header('Location: ../index.php');
    exit;
}

$controller = new ChatController();
$result = $controller->start_or_get_conversation_ctrl($buyerId, $sellerId, $productId);

if ($result['success']) {
    // Redirect to chat page
    header('Location: ../view/chat.php?conversation_id=' . $result['conversation_id']);
    exit;
} else {
    // Error - redirect back
    header('Location: ../index.php');
    exit;
}
?>
