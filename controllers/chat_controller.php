<?php
/**
 * Chat Controller
 * ThriftHub - Messaging System Controller
 * 
 * Wraps chat class methods with validation and error handling
 */

require_once __DIR__ . '/../classes/chat_class.php';

class ChatController {
    private $chat;
    
    public function __construct() {
        $this->chat = new Chat();
    }
    
    /**
     * Start or get existing conversation
     * 
     * @param int $buyerId
     * @param int $sellerId
     * @param int $productId Optional
     * @return array Result with conversation_id
     */
    public function start_or_get_conversation_ctrl($buyerId, $sellerId, $productId = null) {
        // Check if conversation exists
        $existing = $this->chat->get_conversation($buyerId, $sellerId, $productId);
        
        if ($existing) {
            return [
                'success' => true,
                'conversation_id' => $existing['conversation_id'],
                'existing' => true
            ];
        }
        
        // Create new conversation
        return $this->chat->create_conversation($buyerId, $sellerId, $productId);
    }
    
    /**
     * Send a message in a conversation
     * 
     * @param int $conversationId
     * @param int $senderId
     * @param int $receiverId
     * @param string $messageText
     * @return array Result with message data
     */
    public function send_message_ctrl($conversationId, $senderId, $receiverId, $messageText) {
        // Validate that sender is part of the conversation
        if (!$this->chat->is_user_in_conversation($conversationId, $senderId)) {
            return ['success' => false, 'error' => 'You are not part of this conversation'];
        }
        
        $result = $this->chat->create_message($conversationId, $senderId, $receiverId, $messageText);
        
        if ($result['success']) {
            // Get the created message details
            $messages = $this->chat->get_messages_by_conversation($conversationId);
            $lastMessage = end($messages);
            
            return [
                'success' => true,
                'message' => $lastMessage
            ];
        }
        
        return $result;
    }
    
    /**
     * Get all messages in a conversation
     * 
     * @param int $conversationId
     * @param int $userId User requesting messages (for permission check)
     * @return array Result with messages array
     */
    public function get_conversation_messages_ctrl($conversationId, $userId) {
        // Validate that user is part of the conversation
        if (!$this->chat->is_user_in_conversation($conversationId, $userId)) {
            return ['success' => false, 'error' => 'Access denied'];
        }
        
        $messages = $this->chat->get_messages_by_conversation($conversationId);
        
        // Mark messages as read for this user
        $this->chat->mark_messages_as_read($conversationId, $userId);
        
        return [
            'success' => true,
            'messages' => $messages
        ];
    }
    
    /**
     * Get conversation details
     * 
     * @param int $conversationId
     * @param int $userId User requesting (for permission check)
     * @return array Result with conversation data
     */
    public function get_conversation_ctrl($conversationId, $userId) {
        // Validate that user is part of the conversation
        if (!$this->chat->is_user_in_conversation($conversationId, $userId)) {
            return ['success' => false, 'error' => 'Access denied'];
        }
        
        $conversation = $this->chat->get_conversation_by_id($conversationId);
        
        if ($conversation) {
            return [
                'success' => true,
                'conversation' => $conversation
            ];
        }
        
        return ['success' => false, 'error' => 'Conversation not found'];
    }
    
    /**
     * Get all conversations for a user
     * 
     * @param int $userId
     * @return array Result with conversations array
     */
    public function get_user_inbox_ctrl($userId) {
        $conversations = $this->chat->get_user_conversations($userId);
        
        return [
            'success' => true,
            'conversations' => $conversations
        ];
    }
    
    /**
     * Get unread message count for user
     * 
     * @param int $userId
     * @return array Result with unread count
     */
    public function get_unread_count_ctrl($userId) {
        $count = $this->chat->get_unread_count($userId);
        
        return [
            'success' => true,
            'unread_count' => $count
        ];
    }
}
?>
