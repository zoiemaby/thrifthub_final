<?php
/**
 * Chat Page
 * One-on-one conversation between buyer and seller
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/chat_controller.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : 0;
$userId = (int)$_SESSION['user_id'];

if ($conversationId <= 0) {
    header('Location: chat_inbox.php');
    exit;
}

// Get conversation details
$controller = new ChatController();
$conversationResult = $controller->get_conversation_ctrl($conversationId, $userId);

if (!$conversationResult['success']) {
    header('Location: chat_inbox.php');
    exit;
}

$conversation = $conversationResult['conversation'];

// Determine other party and if this is a support conversation
$isSupport = !$conversation['product_id']; // No product means support chat
$currentUserRole = isset($_SESSION['user_role_no']) ? (int)$_SESSION['user_role_no'] : 0;

if ($userId == $conversation['buyer_id']) {
    $otherPartyName = $conversation['seller_name'];
    // If support chat and other party is admin, show "Support" instead of "Seller"
    if ($isSupport && $currentUserRole !== ROLE_ADMIN) {
        $otherPartyRole = 'Support';
    } else {
        $otherPartyRole = 'Seller';
    }
} else {
    $otherPartyName = $conversation['buyer_name'];
    // If support chat and current user is admin, don't show role or show appropriate label
    if ($isSupport && $currentUserRole === ROLE_ADMIN) {
        $otherPartyRole = 'Seller'; // Admin sees "Seller" for support requests
    } else {
        $otherPartyRole = 'Buyer';
    }
}

// Determine back link based on user role and context
$backLink = 'chat_inbox.php';
if ($isSupport) {
    if ($currentUserRole === ROLE_ADMIN) {
        $backLink = '../admin/admin_dashboard.php#messages';
    } else {
        $backLink = 'seller_verification.php';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($otherPartyName); ?> - ThriftHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --thrift-green: #0F5E4D;
            --thrift-green-dark: #0A4538;
            --thrift-green-light: #1A7A66;
            --beige: #F6F2EA;
            --white: #FFFFFF;
            --text-dark: #2C2C2C;
            --text-muted: #6B6B6B;
            --border: #E8E3D8;
            --shadow-md: 0 4px 16px rgba(15, 94, 77, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--beige);
            height: 100vh;
            overflow: hidden;
        }

        .chat-container {
            max-width: 900px;
            margin: 20px auto;
            height: calc(100vh - 40px);
            display: flex;
            flex-direction: column;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .chat-header {
            padding: 20px 24px;
            background: var(--white);
            border-bottom: 2px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--thrift-green) 0%, var(--thrift-green-light) 100%);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
        }

        .chat-header-text h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .chat-header-text p {
            font-size: 13px;
            color: var(--text-muted);
        }

        .back-btn {
            padding: 10px 20px;
            background: var(--beige);
            color: var(--thrift-green);
            border: 2px solid var(--border);
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: var(--white);
            border-color: var(--thrift-green);
        }

        .product-info {
            padding: 16px 24px;
            background: var(--beige);
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .product-thumbnail {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--white);
        }

        .product-details h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .product-details p {
            font-size: 14px;
            font-weight: 700;
            color: var(--thrift-green);
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: linear-gradient(to bottom, #F6F2EA 0%, #FFFFFF 100%);
        }

        .message {
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
        }

        .message.sent {
            align-items: flex-end;
        }

        .message.received {
            align-items: flex-start;
        }

        .message-bubble {
            max-width: 60%;
            padding: 12px 16px;
            border-radius: 16px;
            word-wrap: break-word;
        }

        .message.sent .message-bubble {
            background: var(--thrift-green);
            color: var(--white);
            border-bottom-right-radius: 4px;
        }

        .message.received .message-bubble {
            background: var(--white);
            color: var(--text-dark);
            border: 1px solid var(--border);
            border-bottom-left-radius: 4px;
        }

        .message-text {
            font-size: 14px;
            line-height: 1.5;
        }

        .message-time {
            font-size: 11px;
            margin-top: 6px;
            padding: 0 4px;
        }

        .message.sent .message-time {
            color: var(--text-muted);
        }

        .message.received .message-time {
            color: var(--text-muted);
        }

        .message-input-container {
            padding: 20px 24px;
            background: var(--white);
            border-top: 2px solid var(--border);
            display: flex;
            gap: 12px;
        }

        .message-input {
            flex: 1;
            padding: 14px 18px;
            border: 2px solid var(--border);
            border-radius: 24px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: var(--beige);
            transition: all 0.3s ease;
            resize: none;
            max-height: 120px;
        }

        .message-input:focus {
            outline: none;
            border-color: var(--thrift-green);
            background: var(--white);
        }

        .send-btn {
            padding: 14px 28px;
            background: var(--thrift-green);
            color: var(--white);
            border: none;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .send-btn:hover {
            background: var(--thrift-green-dark);
        }

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .chat-container {
                margin: 0;
                height: 100vh;
                border-radius: 0;
            }

            .message-bubble {
                max-width: 80%;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="avatar"><?php echo strtoupper(substr($otherPartyName, 0, 1)); ?></div>
                <div class="chat-header-text">
                    <h2><?php echo htmlspecialchars($otherPartyName); ?></h2>
                    <p><?php echo htmlspecialchars($otherPartyRole); ?></p>
                </div>
            </div>
            <a href="<?php echo htmlspecialchars($backLink); ?>" class="back-btn">← Back</a>
        </div>

        <!-- Product Info (if applicable) -->
        <?php if ($conversation['product_title']): ?>
        <div class="product-info">
            <?php if ($conversation['product_image']): ?>
                <img src="../<?php echo htmlspecialchars($conversation['product_image']); ?>" 
                     alt="Product" class="product-thumbnail">
            <?php else: ?>
                <div class="product-thumbnail" style="display:flex;align-items:center;justify-content:center;font-size:24px;">📦</div>
            <?php endif; ?>
            <div class="product-details">
                <h4><?php echo htmlspecialchars($conversation['product_title']); ?></h4>
                <p>₵<?php echo number_format($conversation['product_price'], 2); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Messages -->
        <div class="messages-container" id="messagesContainer">
            <div class="empty-state">
                <div class="empty-state-icon">💬</div>
                <div>Loading messages...</div>
            </div>
        </div>

        <!-- Input -->
        <div class="message-input-container">
            <textarea class="message-input" 
                      id="messageInput" 
                      placeholder="Type your message..." 
                      rows="1"></textarea>
            <button class="send-btn" id="sendBtn" onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        const conversationId = <?php echo $conversationId; ?>;
        const currentUserId = <?php echo $userId; ?>;
        let lastMessageId = 0;
        let isLoadingMessages = false;

        function formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const messageDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());

            const timeStr = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

            if (messageDate.getTime() === today.getTime()) {
                return timeStr;
            } else if (messageDate.getTime() === today.getTime() - 86400000) {
                return 'Yesterday ' + timeStr;
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' ' + timeStr;
            }
        }

        function loadMessages() {
            if (isLoadingMessages) return;
            isLoadingMessages = true;

            fetch(`../actions/fetch_messages.php?conversation_id=${conversationId}`)
                .then(response => response.json())
                .then(data => {
                    isLoadingMessages = false;

                    if (!data.success) {
                        console.error('Error loading messages:', data.error);
                        return;
                    }

                    const messages = data.messages || [];
                    const container = document.getElementById('messagesContainer');
                    const shouldScroll = container.scrollHeight - container.scrollTop - container.clientHeight < 100;

                    if (messages.length === 0) {
                        container.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">💬</div>
                                <div>No messages yet. Start the conversation!</div>
                            </div>
                        `;
                        return;
                    }

                    // Only update if there are new messages
                    const latestMessageId = messages.length > 0 ? parseInt(messages[messages.length - 1].message_id) : 0;
                    if (latestMessageId === lastMessageId) {
                        return;
                    }
                    lastMessageId = latestMessageId;

                    container.innerHTML = messages.map(msg => {
                        const isSent = parseInt(msg.sender_id) === currentUserId;
                        const messageClass = isSent ? 'sent' : 'received';

                        return `
                            <div class="message ${messageClass}">
                                <div class="message-bubble">
                                    <div class="message-text">${escapeHtml(msg.message_text)}</div>
                                </div>
                                <div class="message-time">${formatTime(msg.created_at)}</div>
                            </div>
                        `;
                    }).join('');

                    if (shouldScroll) {
                        container.scrollTop = container.scrollHeight;
                    }
                })
                .catch(error => {
                    isLoadingMessages = false;
                    console.error('Error loading messages:', error);
                });
        }

        function sendMessage() {
            const input = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            const messageText = input.value.trim();

            if (!messageText) return;

            sendBtn.disabled = true;

            const formData = new FormData();
            formData.append('conversation_id', conversationId);
            formData.append('message_text', messageText);

            fetch('../actions/send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                sendBtn.disabled = false;

                if (data.success) {
                    input.value = '';
                    input.style.height = 'auto';
                    loadMessages();
                } else {
                    alert('Failed to send message: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                sendBtn.disabled = false;
                console.error('Error sending message:', error);
                alert('Failed to send message. Please try again.');
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Auto-resize textarea
        document.getElementById('messageInput').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Send on Enter (Shift+Enter for new line)
        document.getElementById('messageInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Load messages on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadMessages();
            // Poll for new messages every 3 seconds
            setInterval(loadMessages, 3000);
        });
    </script>
</body>
</html>
