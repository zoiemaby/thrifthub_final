<?php
/**
 * Start Support Chat Action
 * Allows a seller to initiate a support conversation with the admin (role 1)
 */
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/chat_controller.php';
require_once __DIR__ . '/../settings/db_class.php';

// Require logged in user
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: ../view/login.php');
    exit;
}

$db = new Database();
// Find admin user (first admin)
$adminRow = $db->fetchOne("SELECT user_id FROM users WHERE user_role = (SELECT role_no FROM roles WHERE role_description='admin' LIMIT 1) ORDER BY user_id ASC LIMIT 1");
if (!$adminRow) {
    // No admin found
    header('Location: ../view/seller_verification.php?support_error=No+admin+available');
    exit;
}
$adminId = (int)$adminRow['user_id'];
$currentUserId = (int)$_SESSION['user_id'];

// Treat the initiating seller as buyer_id and admin as seller_id for existing schema
$chatController = new ChatController();
$result = $chatController->start_or_get_conversation_ctrl($currentUserId, $adminId, null);

if ($result['success']) {
    $cid = $result['conversation']['conversation_id'] ?? $result['conversation_id'] ?? null;
    if ($cid) {
        header("Location: ../view/chat.php?conversation_id=" . $cid);
        exit;
    }
}
// Fallback
header('Location: ../view/seller_verification.php?support_error=Unable+to+start+conversation');
exit;
