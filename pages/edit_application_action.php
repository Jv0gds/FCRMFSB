<?php
// pages/edit_application_action.php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Verify the application belongs to the current user before updating
    $stmt = $pdo->prepare('SELECT created_by_user_id FROM leads WHERE id = ?');
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();

    if ($application && $application['created_by_user_id'] == $user_id) {
        // Update the application
        $update_stmt = $pdo->prepare('UPDATE leads SET title = ?, description = ? WHERE id = ?');
        $update_stmt->execute([$title, $description, $application_id]);
        
        header('Location: index.php?page=my_applications&success=updated');
        exit();
    } else {
        // Application not found or does not belong to the user
        header('Location: index.php?page=my_applications&error=not_found_or_unauthorized');
        exit();
    }
} else {
    header('Location: index.php?page=my_applications');
    exit();
}
