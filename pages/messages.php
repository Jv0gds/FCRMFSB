<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


include 'db.php';
// The navbar is included by index.php, so we don't need it here.

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare('
    SELECT c.id as conversation_id, cp.user_id, u.username, l.company_name, l.first_name, l.last_name
    FROM conversations c
    JOIN conversation_participants cp ON c.id = cp.conversation_id
    JOIN users u ON cp.user_id = u.id
    LEFT JOIN leads l ON l.id = c.lead_id
    WHERE c.id IN (SELECT conversation_id FROM conversation_participants WHERE user_id = ?)
    ORDER BY c.id
');
$stmt->execute([$user_id]);
$results = $stmt->fetchAll(PDO::FETCH_GROUP);

$conversations = [];
if ($results) {
    foreach($results as $conv_id => $participants) {
        $display_name = null;
        $other_user_name = null;
        
        $lead_data = $participants[0]; // Lead data is the same for all participants

        // Find the other user who is a real registered user
        foreach($participants as $p) {
            if ($p['user_id'] != $user_id) {
                $other_user_name = $p['username'];
                break;
            }
        }
        
        // Prioritize lead's name for display, if available
        $lead_full_name = trim(($lead_data['first_name'] ?? '') . ' ' . ($lead_data['last_name'] ?? ''));
        if (!empty($lead_full_name)) {
            $display_name = $lead_full_name;
        } else {
            // Fallback to the other registered user's name
            $display_name = $other_user_name;
        }
        
        // Ultimate fallback if something is wrong, to prevent errors
        if ($display_name === null && !empty($participants)) {
            $display_name = $participants[0]['username'];
        }

        if ($display_name) {
             $conversations[] = [
                'conversation_id' => $conv_id,
                'display_name' => $display_name,
                'company_name' => $lead_data['company_name'] ?? t('general_conversation')
            ];
        }
    }
}
?>

<div class="messaging-container">
    <div class="conversations-list">
        <?php foreach ($conversations as $conv): ?>
            <div class="conversation-item" data-id="<?= $conv['conversation_id'] ?>">
                <strong><?= htmlspecialchars($conv['display_name']) ?></strong>
                <p><?= htmlspecialchars($conv['company_name']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="messages-view">
        <div class="messages-log">
            <p><?php echo t('select_a_conversation'); ?></p>
        </div>
        <div class="message-form">
            <form id="sendMessageForm">
                <input type="hidden" id="conversationId" name="conversation_id">
                <textarea name="message" placeholder="<?php echo t('enter_message_placeholder'); ?>" required></textarea>
                <button type="submit" class="btn" disabled><?php echo t("send_button"); ?></button>
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
    const messageTextarea = sendMessageForm.querySelector('textarea[name="message"]');
    const sendButton = sendMessageForm.querySelector('button[type="submit"]');

    function updateSendButtonState() {
        const conversationSelected = conversationIdInput.value.trim() !== '';
        const messageExists = messageTextarea.value.trim() !== '';
        sendButton.disabled = !(conversationSelected && messageExists);
    }

    conversationItems.forEach(item => {
        item.addEventListener('click', function() {
            conversationItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            const convId = this.dataset.id;
            conversationIdInput.value = convId;
            loadMessages(convId);
            updateSendButtonState();
        });
    });

    function loadMessages(convId) {
        fetch('pages/conversation.php?id=' + convId)
            .then(response => response.text())
            .then(html => {
                messagesLog.innerHTML = html;
                setTimeout(() => {
                    messagesLog.scrollTop = messagesLog.scrollHeight;
                }, 10); // A small delay to ensure the DOM is updated
            });
    }
    
    messageTextarea.addEventListener('input', updateSendButtonState);

    messageTextarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendButton.disabled) {
                sendMessageForm.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }
    });

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
            messageTextarea.value = ''; // Explicitly clear textarea
            updateSendButtonState();
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
    updateSendButtonState(); // Initial check
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
