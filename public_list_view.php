<?php
include 'db.php';

$search = $_GET['search'] ?? '';

// 设置分页参数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// 获取总记录数
$countStmt = $pdo->prepare('SELECT COUNT(*) FROM leads WHERE title LIKE :search1 OR company_name LIKE :search2 OR description LIKE :search3');
$search_param = "%$search%";
$countStmt->execute([':search1' => $search_param, ':search2' => $search_param, ':search3' => $search_param]);
$totalLeads = $countStmt->fetchColumn();
$totalPages = ceil($totalLeads / $perPage);

// Fetch description and created_at as well.
$stmt = $pdo->prepare('SELECT id, first_name, last_name, company_name, title, description, source, status, created_at FROM leads WHERE title LIKE :search1 OR company_name LIKE :search2 OR description LIKE :search3 ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$search_param = "%$search%";
$stmt->bindValue(':search1', $search_param);
$stmt->bindValue(':search2', $search_param);
$stmt->bindValue(':search3', $search_param);
$stmt->execute();
$leads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>公开线索列表</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container public-list-container">
        <header class="list-header">
            <h1>寻找新的机会</h1>
            <p>浏览公开的业务线索，寻找下一个合作伙伴。</p>
        </header>

        <div class="filter-bar">
            <form action="public_list_view.php" method="get">
                <input type="text" name="search" class="search-input" placeholder="搜索关键词..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn">搜索</button>
            </form>
        </div>

        <div class="lead-list">
            <?php if (empty($leads)): ?>
                <div class="lead-card">
                    <p>没有找到匹配的线索。</p>
                </div>
            <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                    <a href="public_detail_view.php?id=<?= $lead['id'] ?>" class="lead-card-link">
                        <div class="lead-card">
                            <h2 class="lead-title"><?= htmlspecialchars($lead['title'] ?? '') ?></h2>
                            <p class="lead-description-snippet">
                                <?php
                                $description = $lead['description'] ?? '';
                                if (mb_strlen($description) > 150) {
                                    echo htmlspecialchars(mb_substr($description, 0, 150)) . '...';
                                } else {
                                    echo htmlspecialchars($description);
                                }
                                ?>
                            </p>
                            <div class="lead-meta">
                                <span class="status-badge status-badge--new"><?= htmlspecialchars($lead['status'] ?? '') ?></span>
                                <span class="lead-source">来源: <?= htmlspecialchars($lead['source'] ?? '') ?></span>
                                <span class="lead-post-time">发布于: <?= date('Y-m-d', strtotime($lead['created_at'])) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>" class="btn">上一页</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>" class="btn">下一页</a>
            <?php endif; ?>
        </div>
        <div class="back-link-container">
            <a href="index.php" class="btn btn-secondary">返回首页</a>
        </div>
    </div>
</body>
</html>
