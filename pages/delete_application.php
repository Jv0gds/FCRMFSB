<?php
// pages/delete_application.php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    // Should be handled by index.php's auth check, but as a fallback
    header('Location: ../login.html');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: index.php?page=my_applications&error=missing_id');
    exit();
}

$application_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verify the application belongs to the current user before deleting
$stmt = $pdo->prepare('SELECT created_by_user_id FROM leads WHERE id = ?');
$stmt->execute([$application_id]);
$application = $stmt->fetch();

if ($application && $application['created_by_user_id'] == $user_id) {
    // Delete the application
    $delete_stmt = $pdo->prepare('DELETE FROM leads WHERE id = ?');
    $delete_stmt->execute([$application_id]);
    
    header('Location: index.php?page=my_applications&success=deleted');
    exit();
} else {
    // Application not found or does not belong to the user
    header('Location: index.php?page=my_applications&error=not_found_or_unauthorized');
    exit();
}
