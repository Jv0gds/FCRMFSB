<?php
// pages/my_applications.php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
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
        <h1>我的申请</h1>
        <p>在这里查看您提交的所有项目申请及其状态。</p>
    </header>

    <div class="lead-list">
        <?php if (empty($applications)): ?>
            <div class="lead-card">
                <p>您还没有提交任何项目。</p>
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
                            <span class="lead-post-time">发布于: <?= date('Y-m-d', strtotime($app['created_at'])) ?></span>
                        </div>
                    </a>
                    <div class="lead-actions">
                        <a href="?page=edit_application&id=<?= $app['id'] ?>" class="btn btn-secondary btn-sm">编辑</a>
                        <a href="?page=delete_application&id=<?= $app['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('您确定要删除此项目吗？');">删除</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
