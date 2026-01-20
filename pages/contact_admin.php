<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../db.php';
include '../templates/navbar.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

// Get recipient ID and subject from URL
$recipient_id = $_GET['recipient_id'] ?? 0;
$subject = $_GET['subject'] ?? '';

// Basic validation
if (empty($recipient_id)) {
    die("错误：未指定收件人。");
}

// Fetch recipient's username to display it
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$recipient_id]);
$recipient = $stmt->fetch();

if (!$recipient) {
    die("错误：收件人不存在。");
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发送站内信</title>
    <base href="/">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>发送站内信</h1>
        <p>发送给: <?= htmlspecialchars($recipient['username']) ?></p>
        <form action="send_message.php" method="post">
            <input type="hidden" name="recipient_id" value="<?= htmlspecialchars($recipient_id) ?>">
            
            <div class="form-group">
                <label for="subject">主题</label>
                <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($subject) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="message">内容</label>
                <textarea id="message" name="message" rows="10" class="form-control" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">发送</button>
        </form>
    </div>
</body>
</html>
