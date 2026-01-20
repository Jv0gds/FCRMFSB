<?php
// FOR INTERNAL TESTING ONLY
// PURPOSE: To clear all conversation, participant, and message data for a clean test.
// USAGE: Navigate to this script in your browser.
// WARNING: THIS IS A DESTRUCTIVE ACTION. DELETE THIS FILE IMMEDIATELY AFTER USE.

include 'db.php';

echo "<pre>";
echo "Attempting to clear messaging tables...\n\n";

try {
    // Start a transaction
    $pdo->beginTransaction();

    // 1. Delete all messages
    $stmt = $pdo->prepare('DELETE FROM messages');
    $stmt->execute();
    echo "Deleted " . $stmt->rowCount() . " rows from 'messages'.\n";

    // 2. Delete all conversation participants
    $stmt = $pdo->prepare('DELETE FROM conversation_participants');
    $stmt->execute();
    echo "Deleted " . $stmt->rowCount() . " rows from 'conversation_participants'.\n";

    // 3. Delete all conversations
    $stmt = $pdo->prepare('DELETE FROM conversations');
    $stmt->execute();
    echo "Deleted " . $stmt->rowCount() . " rows from 'conversations'.\n";

    // Commit the transaction
    $pdo->commit();
    echo "\nSUCCESS: All messaging tables have been cleared.\n";

} catch (Exception $e) {
    // Roll back the transaction if something failed
    $pdo->rollBack();
    echo "\nERROR: An error occurred. Database has been rolled back.\n";
    echo "Error message: " . $e->getMessage() . "\n";
}

echo "\n\nOperation complete. PLEASE DELETE THIS FILE NOW.";
echo "</pre>";

?>
