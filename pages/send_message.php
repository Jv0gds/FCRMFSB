<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Only allow POST requests
    header('Location: ../index.php');
    exit();
}

$sender_id = $_SESSION['user_id'];

// --- New Logic for Admin Contact Form ---
if (isset($_POST['recipient_id'], $_POST['subject'], $_POST['message'])) {
    $recipient_id = $_POST['recipient_id'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($recipient_id) || empty($subject) || empty($message)) {
        // Handle error: empty fields
        header('Location: contact_admin.php?recipient_id=' . $recipient_id . '&error=empty');
        exit();
    }

    if ($sender_id == $recipient_id) {
        // User trying to message themselves
        header('Location: ../index.php');
        exit();
    }
    
    $pdo->beginTransaction();
    try {
        // Step 1: Create a new conversation (assuming `subject` column exists and `lead_id` is nullable)
        $stmt = $pdo->prepare("INSERT INTO conversations (subject, lead_id) VALUES (?, NULL)");
        $stmt->execute([$subject]);
        $conversation_id = $pdo->lastInsertId();

        // Step 2: Add participants
        $stmt = $pdo->prepare('INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?)');
        $stmt->execute([$conversation_id, $sender_id, $conversation_id, $recipient_id]);
        
        // Step 3: Insert the first message
        $stmt = $pdo->prepare('INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)');
        $stmt->execute([$conversation_id, $sender_id, $message]);

        $pdo->commit();
        
        // Redirect to messages page with success status
        header('Location: ../index.php?page=messages&status=sent');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        // Redirect back to form with an error
        // In a real app, you would log the error: error_log($e->getMessage());
        header('Location: contact_admin.php?recipient_id=' . $recipient_id . '&error=dberror');
        exit();
    }

// --- Existing Logic for AJAX Message Sending ---
} elseif (isset($_POST['conversation_id'], $_POST['message'])) {
    $conversation_id = $_POST['conversation_id'];
    $message = $_POST['message'];

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
            exit();
        } catch (PDOException $e) {
            http_response_code(500);
            exit('DB Error');
        }
    } else {
        http_response_code(400); // Bad request
        exit('Invalid input');
    }
} else {
    // Invalid POST request
    header('Location: ../index.php');
    exit();
}
