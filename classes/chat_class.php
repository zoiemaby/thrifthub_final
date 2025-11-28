<?php
/**
 * Chat Class
 * ThriftHub - Messaging System
 * 
 * Handles conversations and messages between buyers and sellers
 */

require_once __DIR__ . '/../settings/db_class.php';

class Chat extends Database {
    
    /**
     * Create a new conversation between buyer and seller
     * 
     * @param int $buyerId User ID of buyer
     * @param int $sellerId User ID of seller
     * @param int $productId Optional product ID (nullable)
     * @return array ['success' => bool, 'conversation_id' => int]
     */
    public function create_conversation($buyerId, $sellerId, $productId = null) {
        $buyerId = (int)$buyerId;
        $sellerId = (int)$sellerId;
        $productId = $productId ? (int)$productId : null;
        
        if ($buyerId <= 0 || $sellerId <= 0) {
            return ['success' => false, 'error' => 'Invalid buyer or seller ID'];
        }
        
        // Check if conversation already exists
        $existing = $this->get_conversation($buyerId, $sellerId, $productId);
        if ($existing) {
            return ['success' => true, 'conversation_id' => $existing['conversation_id'], 'existing' => true];
        }
        
        $productValue = $productId ? $productId : 'NULL';
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO conversations (buyer_id, seller_id, product_id, created_at, updated_at) 
                VALUES ($buyerId, $sellerId, $productValue, '$now', '$now')";
        
        $result = $this->query($sql);
        
        if ($result) {
            $conversationId = $this->insert_id();
            return ['success' => true, 'conversation_id' => $conversationId];
        }
        
        return ['success' => false, 'error' => 'Failed to create conversation'];
    }
    
    /**
     * Get existing conversation between buyer and seller
     * 
     * @param int $buyerId
     * @param int $sellerId
     * @param int $productId Optional
     * @return array|false Conversation data or false
     */
    public function get_conversation($buyerId, $sellerId, $productId = null) {
        $buyerId = (int)$buyerId;
        $sellerId = (int)$sellerId;
        
        $sql = "SELECT * FROM conversations 
                WHERE buyer_id = $buyerId AND seller_id = $sellerId";
        
        if ($productId) {
            $productId = (int)$productId;
            $sql .= " AND product_id = $productId";
        } else {
            $sql .= " AND product_id IS NULL";
        }
        
        $sql .= " LIMIT 1";
        
        return $this->fetchOne($sql);
    }
    
    /**
     * Get conversation by ID
     * 
     * @param int $conversationId
     * @return array|false Conversation with buyer, seller, and product details
     */
    public function get_conversation_by_id($conversationId) {
        $conversationId = (int)$conversationId;
        
        $sql = "SELECT c.*, 
                       buyer.name as buyer_name, buyer.email as buyer_email,
                       seller.name as seller_name, seller.email as seller_email,
                       p.product_title, p.product_image, p.product_price
                FROM conversations c
                JOIN users buyer ON c.buyer_id = buyer.user_id
                JOIN users seller ON c.seller_id = seller.user_id
                LEFT JOIN products p ON c.product_id = p.product_id
                WHERE c.conversation_id = $conversationId";
        
        return $this->fetchOne($sql);
    }
    
    /**
     * Create a new message in a conversation
     * 
     * @param int $conversationId
     * @param int $senderId
     * @param int $receiverId
     * @param string $messageText
     * @return array ['success' => bool, 'message_id' => int]
     */
    public function create_message($conversationId, $senderId, $receiverId, $messageText) {
        $conversationId = (int)$conversationId;
        $senderId = (int)$senderId;
        $receiverId = (int)$receiverId;
        $messageText = $this->escape(trim($messageText));
        
        if (empty($messageText)) {
            return ['success' => false, 'error' => 'Message cannot be empty'];
        }
        
        $now = date('Y-m-d H:i:s');
        
        $sql = "INSERT INTO messages (conversation_id, sender_id, receiver_id, message_text, is_read, created_at) 
                VALUES ($conversationId, $senderId, $receiverId, '$messageText', 0, '$now')";
        
        $result = $this->query($sql);
        
        if ($result) {
            $messageId = $this->insert_id();
            
            // Update conversation updated_at timestamp
            $updateSql = "UPDATE conversations SET updated_at = '$now' WHERE conversation_id = $conversationId";
            $this->query($updateSql);
            
            return ['success' => true, 'message_id' => $messageId];
        }
        
        return ['success' => false, 'error' => 'Failed to send message'];
    }
    
    /**
     * Get all messages in a conversation
     * 
     * @param int $conversationId
     * @return array Array of messages
     */
    public function get_messages_by_conversation($conversationId) {
        $conversationId = (int)$conversationId;
        
        $sql = "SELECT m.*, 
                       sender.name as sender_name,
                       receiver.name as receiver_name
                FROM messages m
                JOIN users sender ON m.sender_id = sender.user_id
                JOIN users receiver ON m.receiver_id = receiver.user_id
                WHERE m.conversation_id = $conversationId
                ORDER BY m.created_at ASC";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Get all conversations for a user (as buyer or seller)
     * 
     * @param int $userId
     * @return array Array of conversations with last message and unread count
     */
    public function get_user_conversations($userId) {
        $userId = (int)$userId;
        
        $sql = "SELECT c.*,
                       CASE 
                           WHEN c.buyer_id = $userId THEN seller.name
                           ELSE buyer.name
                       END as other_party_name,
                       CASE 
                           WHEN c.buyer_id = $userId THEN seller.user_id
                           ELSE buyer.user_id
                       END as other_party_id,
                       CASE 
                           WHEN c.buyer_id = $userId THEN 'seller'
                           ELSE 'buyer'
                       END as other_party_role,
                       p.product_title, p.product_image,
                       (SELECT message_text FROM messages 
                        WHERE conversation_id = c.conversation_id 
                        ORDER BY created_at DESC LIMIT 1) as last_message,
                       (SELECT created_at FROM messages 
                        WHERE conversation_id = c.conversation_id 
                        ORDER BY created_at DESC LIMIT 1) as last_message_time,
                       (SELECT COUNT(*) FROM messages 
                        WHERE conversation_id = c.conversation_id 
                        AND receiver_id = $userId 
                        AND is_read = 0) as unread_count
                FROM conversations c
                JOIN users buyer ON c.buyer_id = buyer.user_id
                JOIN users seller ON c.seller_id = seller.user_id
                LEFT JOIN products p ON c.product_id = p.product_id
                WHERE c.buyer_id = $userId OR c.seller_id = $userId
                ORDER BY c.updated_at DESC";
        
        return $this->fetchAll($sql);
    }
    
    /**
     * Mark all messages in a conversation as read for a specific user
     * 
     * @param int $conversationId
     * @param int $userId The user who is reading the messages
     * @return bool Success status
     */
    public function mark_messages_as_read($conversationId, $userId) {
        $conversationId = (int)$conversationId;
        $userId = (int)$userId;
        
        $sql = "UPDATE messages 
                SET is_read = 1 
                WHERE conversation_id = $conversationId 
                AND receiver_id = $userId 
                AND is_read = 0";
        
        return $this->query($sql) !== false;
    }
    
    /**
     * Check if user is part of conversation (buyer or seller)
     * 
     * @param int $conversationId
     * @param int $userId
     * @return bool True if user is part of conversation
     */
    public function is_user_in_conversation($conversationId, $userId) {
        $conversationId = (int)$conversationId;
        $userId = (int)$userId;
        
        $sql = "SELECT conversation_id FROM conversations 
                WHERE conversation_id = $conversationId 
                AND (buyer_id = $userId OR seller_id = $userId)";
        
        $result = $this->fetchOne($sql);
        return $result !== false;
    }
    
    /**
     * Get unread message count for a user
     * 
     * @param int $userId
     * @return int Unread count
     */
    public function get_unread_count($userId) {
        $userId = (int)$userId;
        
        $sql = "SELECT COUNT(*) as count FROM messages 
                WHERE receiver_id = $userId AND is_read = 0";
        
        $result = $this->fetchOne($sql);
        return $result ? (int)$result['count'] : 0;
    }
}
?>
