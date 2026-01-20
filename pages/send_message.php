<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Not logged in');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conversation_id = $_POST['conversation_id'] ?? 0;
    $sender_id = $_SESSION['user_id'];
    $message = $_POST['message'] ?? '';

    // Security check: ensure user is a participant of the conversation
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM conversation_participants WHERE conversation_id = ? AND user_id = ?');
    $stmt->execute([$conversation_id, $sender_id]);
    if ($stmt->fetchColumn() == 0) {
        http_response_code(403);
        exit('Access denied');
    }

    if ($conversation_id && $sender_id && !empty(trim($message))) {
        try {
            $stmt = $pdo->prepare('INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)');
            $stmt->execute([$conversation_id, $sender_id, $message]);
            http_response_code(200);
        } catch (PDOException $e) {
            http_response_code(500);
            // In a real app, log this error
        }
    } else {
        http_response_code(400); // Bad request
    }
}
