<?php
session_start();
include '../db.php';
// The navbar is included by index.php, so we don't need it here.

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all conversations for the current user
$stmt = $pdo->prepare('
    SELECT c.id as conversation_id, l.company_name, u.username as other_user
    FROM conversations c
    JOIN conversation_participants cp ON c.id = cp.conversation_id
    JOIN users u ON u.id = cp.user_id
    LEFT JOIN leads l ON l.id = c.lead_id
    WHERE c.id IN (SELECT conversation_id FROM conversation_participants WHERE user_id = ?)
    AND cp.user_id != ?
    GROUP BY c.id
');
$stmt->execute([$user_id, $user_id]);
$conversations = $stmt->fetchAll();

?>

<div class="messaging-container">
    <div class="conversations-list">
        <?php foreach ($conversations as $conv): ?>
            <div class="conversation-item" data-id="<?= $conv['conversation_id'] ?>">
                <strong><?= htmlspecialchars($conv['other_user']) ?></strong>
                <p><?= htmlspecialchars($conv['company_name'] ?? 'General Conversation') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="messages-view">
        <div class="messages-log">
            <p>请在左侧选择一个对话。</p>
        </div>
        <div class="message-form">
            <form id="sendMessageForm">
                <input type="hidden" id="conversationId" name="conversation_id">
                <textarea name="message" placeholder="输入消息..." required></textarea>
                <button type="submit" class="btn">发送</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conversationItems = document.querySelectorAll('.conversation-item');
    const messagesLog = document.querySelector('.messages-log');
    const conversationIdInput = document.getElementById('conversationId');
    const sendMessageForm = document.getElementById('sendMessageForm');

    conversationItems.forEach(item => {
        item.addEventListener('click', function() {
            conversationItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            const convId = this.dataset.id;
            conversationIdInput.value = convId;
            loadMessages(convId);
        });
    });

    function loadMessages(convId) {
        fetch('index.php?page=conversation&id=' + convId)
            .then(response => response.text())
            .then(html => {
                messagesLog.innerHTML = html;
                messagesLog.scrollTop = messagesLog.scrollHeight; // Scroll to bottom
            });
    }
    
    sendMessageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('index.php?page=send_message', {
            method: 'POST',
            body: formData
        })
        .then(() => {
            loadMessages(conversationIdInput.value);
            this.reset();
        });
    });
    
    // If a conversation_id is in the URL, load it.
    const urlParams = new URLSearchParams(window.location.search);
    const conversationId = urlParams.get('conversation_id');
    if (conversationId) {
        const item = document.querySelector(`.conversation-item[data-id="${conversationId}"]`);
        if(item){
            item.click();
        }
    }
});
</script>
<style>
    .messaging-container {
        display: flex;
        height: calc(100vh - 100px); /* Adjust based on navbar and padding */
    }
    .conversations-list {
        width: 300px;
        border-right: 1px solid var(--border-color);
        overflow-y: auto;
    }
    .conversation-item {
        padding: 1rem;
        cursor: pointer;
        border-bottom: 1px solid var(--border-color);
    }
    .conversation-item:hover, .conversation-item.active {
        background-color: #f6f8fa;
    }
    .messages-view {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .messages-log {
        flex-grow: 1;
        padding: 1rem;
        overflow-y: auto;
    }
    .message {
        margin-bottom: 1rem;
    }
    .message.sent {
        text-align: right;
    }
    .message-bubble {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 15px;
        max-width: 70%;
    }
    .message.sent .message-bubble {
        background-color: var(--upwork-green);
        color: white;
    }
    .message.received .message-bubble {
        background-color: #e4e6eb;
    }
    .message-form {
        padding: 1rem;
        border-top: 1px solid var(--border-color);
    }
    .message-form textarea {
        width: 100%;
        padding: 0.5rem;
        border-radius: 8px;
        border: 1px solid #ccc;
        margin-bottom: 0.5rem;
    }
</style>
