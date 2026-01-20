<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Not logged in');
}

$conversation_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Security check: ensure user is a participant of the conversation
$stmt = $pdo->prepare('SELECT COUNT(*) FROM conversation_participants WHERE conversation_id = ? AND user_id = ?');
$stmt->execute([$conversation_id, $user_id]);
if ($stmt->fetchColumn() == 0) {
    http_response_code(403);
    exit('Access denied');
}


// Fetch messages
$stmt = $pdo->prepare('
    SELECT m.message, m.sender_id, m.created_at, u.username 
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.conversation_id = ?
    ORDER BY m.created_at ASC
');
$stmt->execute([$conversation_id]);
$messages = $stmt->fetchAll();

foreach ($messages as $message) {
    $status = ($message['sender_id'] == $user_id) ? 'sent' : 'received';
    echo "<div class='message {$status}'>";
    echo "<div class='message-bubble'>";
    echo "<p>" . htmlspecialchars($message['message']) . "</p>";
    echo "<small>" . htmlspecialchars($message['username']) . " at " . date('H:i', strtotime($message['created_at'])) . "</small>";
    echo "</div>";
    echo "</div>";
}
