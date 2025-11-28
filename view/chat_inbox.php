<?php
/**
 * Chat Inbox Page
 * Shows all conversations for the logged-in user
 */

require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$userName = $_SESSION['customer_name'] ?? $_SESSION['name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - ThriftHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --thrift-green: #0F5E4D;
            --thrift-green-dark: #0A4538;
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
            color: var(--text-dark);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            background: var(--white);
            padding: 24px;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--thrift-green);
        }

        .back-btn {
            padding: 10px 20px;
            background: var(--beige);
            color: var(--thrift-green);
            border: 2px solid var(--border);
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: var(--white);
            border-color: var(--thrift-green);
        }

        .conversations-list {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .conversation-item {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: background 0.2s ease;
            display: flex;
            gap: 16px;
            align-items: start;
        }

        .conversation-item:hover {
            background: var(--beige);
        }

        .conversation-item:last-child {
            border-bottom: none;
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--thrift-green) 0%, #1A7A66 100%);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .conversation-content {
            flex: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .conversation-name {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-dark);
        }

        .conversation-time {
            font-size: 12px;
            color: var(--text-muted);
        }

        .conversation-preview {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .last-message {
            font-size: 14px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
        }

        .unread-badge {
            background: var(--thrift-green);
            color: var(--white);
            border-radius: 50%;
            min-width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            padding: 0 8px;
        }

        .product-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--beige);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-muted);
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💬 Messages</h1>
            <a href="../index.php" class="back-btn">← Back to Home</a>
        </div>

        <div class="conversations-list" id="conversationsList">
            <div class="loading">Loading conversations...</div>
        </div>
    </div>

    <script>
        function formatTime(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        }

        function loadInbox() {
            fetch('../actions/fetch_inbox.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Error loading inbox:', data.error);
                        return;
                    }

                    const conversations = data.conversations || [];
                    const container = document.getElementById('conversationsList');

                    if (conversations.length === 0) {
                        container.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">📭</div>
                                <div>No conversations yet</div>
                            </div>
                        `;
                        return;
                    }

                    container.innerHTML = conversations.map(conv => {
                        const initial = conv.other_party_name ? conv.other_party_name.charAt(0).toUpperCase() : '?';
                        const unreadBadge = conv.unread_count > 0 
                            ? `<span class="unread-badge">${conv.unread_count}</span>` 
                            : '';
                        const productTag = conv.product_title 
                            ? `<div class="product-tag">📦 ${conv.product_title}</div>` 
                            : '';

                        return `
                            <div class="conversation-item" onclick="openChat(${conv.conversation_id})">
                                <div class="conversation-avatar">${initial}</div>
                                <div class="conversation-content">
                                    <div class="conversation-header">
                                        <span class="conversation-name">${conv.other_party_name || 'Unknown'}</span>
                                        <span class="conversation-time">${formatTime(conv.last_message_time)}</span>
                                    </div>
                                    <div class="conversation-preview">
                                        <span class="last-message">${conv.last_message || 'No messages yet'}</span>
                                        ${unreadBadge}
                                    </div>
                                    ${productTag}
                                </div>
                            </div>
                        `;
                    }).join('');
                })
                .catch(error => {
                    console.error('Error loading inbox:', error);
                    document.getElementById('conversationsList').innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">⚠️</div>
                            <div>Error loading conversations</div>
                        </div>
                    `;
                });
        }

        function openChat(conversationId) {
            window.location.href = `chat.php?conversation_id=${conversationId}`;
        }

        // Load inbox on page load
        document.addEventListener('DOMContentLoaded', loadInbox);

        // Refresh inbox every 10 seconds
        setInterval(loadInbox, 10000);
    </script>
</body>
</html>
