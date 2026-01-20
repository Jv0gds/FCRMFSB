<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'] ?? 0;
$lead_id = $_GET['lead_id'] ?? 0;

if (empty($other_user_id) || empty($lead_id) || $current_user_id == $other_user_id) {
    // Redirect back to the homepage or an error page if parameters are missing or user tries to message themselves
    header('Location: index.php');
    exit();
}

// Check if a conversation already exists for this lead between these two users
$stmt = $pdo->prepare('
    SELECT c.id FROM conversations c
    JOIN conversation_participants cp1 ON c.id = cp1.conversation_id
    JOIN conversation_participants cp2 ON c.id = cp2.conversation_id
    WHERE c.lead_id = ? AND cp1.user_id = ? AND cp2.user_id = ?
');
$stmt->execute([$lead_id, $current_user_id, $other_user_id]);
$conversation = $stmt->fetch();

if ($conversation) {
    // Conversation exists, redirect to it
    header('Location: index.php?page=messages&conversation_id=' . $conversation['id']);
    exit();
} else {
    // Create a new conversation
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO conversations (lead_id) VALUES (?)');
        $stmt->execute([$lead_id]);
        $conversation_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?)');
        $stmt->execute([$conversation_id, $current_user_id, $conversation_id, $other_user_id]);

        $pdo->commit();

        header('Location: index.php?page=messages&conversation_id=' . $conversation_id);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        // Handle error, maybe redirect to an error page
        header('Location: public_detail_view.php?id=' . $lead_id . '&error=start_conversation_failed');
        exit();
    }
}
