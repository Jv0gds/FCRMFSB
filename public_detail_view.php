<?php
session_start();
include 'db.php';

$id = $_GET['id'] ?? 0;

if (!$id) {
    die('无效的ID');
}

$stmt = $pdo->prepare('SELECT *, assigned_to as owner_id FROM leads WHERE id = ?');
$stmt->execute([$id]);
$lead = $stmt->fetch();

if (!$lead) {
    die('未找到指定的线索');
}

// Fetch comments for this lead
$stmt_comments = $pdo->prepare('
    SELECT c.comment, c.created_at, u.username
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.lead_id = ?
    ORDER BY c.created_at DESC
');
$stmt_comments->execute([$id]);
$comments = $stmt_comments->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>线索详情</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container public-detail-container">
        <header class="detail-header">
            <h1><?= htmlspecialchars(($lead['first_name'] ?? '') . ' ' . ($lead['last_name'] ?? '')) ?></h1>
            <h2><?= htmlspecialchars($lead['company_name'] ?? '') ?></h2>
        </header>

        <div class="detail-content">
            <div class="main-content-detail">
                <h3>项目描述</h3>
                <p class="lead-description">
                    <?= nl2br(htmlspecialchars($lead['description'] ?? '没有提供描述。')) ?>
                </p>

                <div class="comments-section">
                    <h3>评论</h3>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="comment_action.php" method="post" class="comment-form">
                            <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                            <textarea name="comment" placeholder="添加评论..." required></textarea>
                            <button type="submit" class="btn btn-primary">提交</button>
                        </form>
                    <?php else: ?>
                        <p><a href="login.html">登录</a>后才能发表评论。</p>
                    <?php endif; ?>

                    <div class="comments-list">
                        <?php if (empty($comments)): ?>
                            <p>还没有评论。</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment">
                                    <p class="comment-body"><?= htmlspecialchars($comment['comment']) ?></p>
                                    <div class="comment-meta">
                                        <span><strong><?= htmlspecialchars($comment['username']) ?></strong></span>
                                        <span><?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            <aside class="sidebar-detail">
                <div class="actions-box">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['user_id'] != $lead['owner_id']): ?>
                            <a href="index.php?page=start_conversation&lead_id=<?= $lead['id'] ?>&user_id=<?= $lead['owner_id'] ?>" class="btn btn-primary btn-block">发送私信</a>
                        <?php endif; ?>
                        <button class="btn btn-primary btn-block">请求访问</button>
                    <?php else: ?>
                        <a href="login.html" class="btn btn-primary btn-block">登录以请求访问</a>
                    <?php endif; ?>
                    <a href="mailto:report@example.com?subject=内容举报：线索 ID <?= $lead['id'] ?>" class="btn btn-secondary btn-block">举报内容</a>
                </div>
                <div class="info-box">
                    <h4>关于客户</h4>
                    <dl>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <dt>电话:</dt> <dd><?= htmlspecialchars($lead['phone'] ?? '未提供') ?></dd>
                            <dt>邮箱:</dt> <dd><?= htmlspecialchars($lead['email'] ?? '未提供') ?></dd>
                        <?php else: ?>
                            <dt>电话:</dt> <dd><a href="login.html">登录后可见</a></dd>
                            <dt>邮箱:</dt> <dd><a href="login.html">登录后可见</a></dd>
                        <?php endif; ?>
                        <dt>来源:</dt> <dd><?= htmlspecialchars($lead['source'] ?? '未提供') ?></dd>
                        <dt>状态:</dt> <dd><span class="status-badge status-badge--new"><?= htmlspecialchars($lead['status'] ?? '') ?></span></dd>
                    </dl>
                </div>
            </aside>
        </div>
         <div class="back-link-container">
            <a href="public_list_view.php" class="btn btn-secondary">返回列表</a>
        </div>
    </div>
</body>
</html>
