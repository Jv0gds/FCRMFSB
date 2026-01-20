<?php

// pages/my_applications.php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all leads created by the current user
$stmt = $pdo->prepare('
    SELECT id, title, description, status, created_at 
    FROM leads 
    WHERE created_by_user_id = ? 
    ORDER BY created_at DESC
');
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll();
?>

<div class="container public-list-container">
    <header class="list-header">
        <h1><?php echo t("my_applications_title"); ?></h1>
        <p><?php echo t("view_all_applications_status"); ?></p>
    </header>

    <div class="lead-list">
        <?php if (empty($applications)): ?>
            <div class="lead-card">
                <p><?php echo t("no_projects_submitted"); ?></p>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="lead-card">
                    <a href="public_detail_view.php?id=<?= $app['id'] ?>" class="lead-card-link">
                        <h2 class="lead-title"><?= htmlspecialchars($app['title'] ?? '') ?></h2>
                        <p class="lead-description-snippet">
                            <?php
                            $description = $app['description'] ?? '';
                            if (mb_strlen($description) > 150) {
                                echo htmlspecialchars(mb_substr($description, 0, 150)) . '...';
                            } else {
                                echo htmlspecialchars($description);
                            }
                            ?>
                        </p>
                        <div class="lead-meta">
                            <span class="status-badge status-badge--new"><?= htmlspecialchars($app['status'] ?? '') ?></span>
                            <span class="lead-post-time"><?php echo t("posted_on"); ?>: <?= date('Y-m-d', strtotime($app['created_at'])) ?></span>
                        </div>
                    </a>
                    <div class="lead-actions">
                        <a href="?page=edit_application&id=<?= $app['id'] ?>" class="btn btn-secondary btn-sm"><?php echo t("edit"); ?></a>
                        <a href="?page=delete_application&id=<?= $app['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo t("confirm_delete_project"); ?>');"><?php echo t("delete"); ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
