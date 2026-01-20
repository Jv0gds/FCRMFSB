<?php
// pages/edit_application.php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: index.php?page=my_applications&error=missing_id');
    exit();
}

$application_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch application details
$stmt = $pdo->prepare('SELECT * FROM leads WHERE id = ? AND created_by_user_id = ?');
$stmt->execute([$application_id, $user_id]);
$application = $stmt->fetch();

if (!$application) {
    // Application not found or does not belong to the user
    header('Location: index.php?page=my_applications&error=not_found_or_unauthorized');
    exit();
}
?>

<div class="container">
    <header>
        <h1>编辑项目</h1>
    </header>
    <form action="index.php?page=edit_application_action" method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($application['id']) ?>">
        <div class="form-group">
            <label for="title">标题</label>
            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($application['title']) ?>" required>
        </div>
        <div class="form-group">
            <label for="description">描述</label>
            <textarea id="description" name="description" class="form-control" rows="5" required><?= htmlspecialchars($application['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
