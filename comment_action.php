<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lead_id = $_POST['lead_id'] ?? 0;
    $user_id = $_SESSION['user_id'];
    $comment = $_POST['comment'] ?? '';

    if ($lead_id && $user_id && !empty(trim($comment))) {
        try {
            $stmt = $pdo->prepare('INSERT INTO comments (lead_id, user_id, comment) VALUES (?, ?, ?)');
            $stmt->execute([$lead_id, $user_id, $comment]);
        } catch (PDOException $e) {
            // Handle error, e.g., log it or show a generic error message
            // For simplicity, we'll just redirect back with an error flag
            header('Location: public_detail_view.php?id=' . $lead_id . '&error=1');
            exit();
        }
    }

    header('Location: public_detail_view.php?id=' . $lead_id);
    exit();
}
