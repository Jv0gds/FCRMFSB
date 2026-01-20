<?php
session_start();

// Language switching logic
$available_langs = ['en', 'fr', 'zh-CN'];
$default_lang = 'zh-CN';

if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? $default_lang;

$lang_file = "languages/{$lang}.php";
if (file_exists($lang_file)) {
    include_once $lang_file;
} else {
    // Fallback to default language if file not found
    include_once "languages/{$default_lang}.php";
}

// Function to get translation
function t($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

include 'db.php';

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$sort = $_GET['sort'] ?? 'created_at_desc';

// 设置分页参数
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// 构建查询
$params = [];
$whereClauses = [];
$baseSql = 'FROM leads';

if (!empty($search)) {
    $whereClauses[] = '(title LIKE :search1 OR company_name LIKE :search2 OR description LIKE :search3)';
    $params[':search1'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

if (!empty($status)) {
    $whereClauses[] = 'status = :status';
    $params[':status'] = $status;
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = ' WHERE ' . implode(' AND ', $whereClauses);
}

// 获取总记录数
$countSql = 'SELECT COUNT(*) ' . $baseSql . $whereSql;
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalLeads = $countStmt->fetchColumn();
$totalPages = ceil($totalLeads / $perPage);

// 构建排序
$orderBy = 'ORDER BY created_at DESC';
if ($sort === 'created_at_asc') {
    $orderBy = 'ORDER BY created_at ASC';
} elseif ($sort === 'title_asc') {
    $orderBy = 'ORDER BY title ASC';
} elseif ($sort === 'title_desc') {
    $orderBy = 'ORDER BY title DESC';
}

// 获取线索数据
$leadsSql = 'SELECT id, first_name, last_name, company_name, title, description, source, status, created_at ' . $baseSql . $whereSql . ' ' . $orderBy . ' LIMIT :limit OFFSET :offset';
$stmt = $pdo->prepare($leadsSql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val);
}
$stmt->execute();
$leads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('public_leads_list'); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container public-list-container">
        <header class="list-header">
            <h1><?php echo t('find_new_opportunities'); ?></h1>
            <p><?php echo t('browse_public_leads'); ?></p>
        </header>

        <div class="filter-bar">
            <form action="public_list_view.php" method="get" class="filter-form">
                <input type="text" name="search" class="search-input" placeholder="<?php echo t('search_keywords'); ?>" value="<?= htmlspecialchars($search) ?>">
                
                <select name="status" class="filter-select">
                    <option value=""><?php echo t('all_statuses'); ?></option>
                    <option value="new" <?= ($status == 'new') ? 'selected' : '' ?>><?php echo t('status_new'); ?></option>
                    <option value="contacted" <?= ($status == 'contacted') ? 'selected' : '' ?>><?php echo t('status_contacted'); ?></option>
                    <option value="qualified" <?= ($status == 'qualified') ? 'selected' : '' ?>><?php echo t('status_qualified'); ?></option>
                    <option value="lost" <?= ($status == 'lost') ? 'selected' : '' ?>><?php echo t('status_lost'); ?></option>
                </select>

                <select name="sort" class="filter-select">
                    <option value="created_at_desc" <?= ($sort == 'created_at_desc') ? 'selected' : '' ?>><?php echo t('sort_newest'); ?></option>
                    <option value="created_at_asc" <?= ($sort == 'created_at_asc') ? 'selected' : '' ?>><?php echo t('sort_oldest'); ?></option>
                    <option value="title_asc" <?= ($sort == 'title_asc') ? 'selected' : '' ?>><?php echo t('sort_title_az'); ?></option>
                    <option value="title_desc" <?= ($sort == 'title_desc') ? 'selected' : '' ?>><?php echo t('sort_title_za'); ?></option>
                </select>

                <button type="submit" class="btn"><?php echo t('filter_button'); ?></button>
            </form>
        </div>

        <div class="lead-list">
            <?php if (empty($leads)): ?>
                <div class="lead-card">
                    <p><?php echo t('no_matching_leads'); ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                    <a href="public_detail_view.php?id=<?= $lead['id'] ?>" class="lead-card-link">
                        <div class="lead-card">
                            <h2 class="lead-title"><?= htmlspecialchars($lead['title'] ?? '') ?></h2>
                            <p class="lead-name" style="font-weight: bold;"><?= htmlspecialchars($lead['first_name'] ?? '') ?> <?= htmlspecialchars($lead['last_name'] ?? '') ?></p>
                            <p class="lead-company"><?= htmlspecialchars($lead['company_name'] ?? '') ?></p>
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
                                <span class="lead-source"><?php echo t('source'); ?>: <?= htmlspecialchars($lead['source'] ?? '') ?></span>
                                <span class="lead-post-time"><?php echo t('posted_on'); ?>: <?= date('Y-m-d', strtotime($lead['created_at'])) ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search) ?>&status=<?= htmlspecialchars($status) ?>&sort=<?= htmlspecialchars($sort) ?>" class="btn"><?php echo t('previous_page'); ?></a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search) ?>&status=<?= htmlspecialchars($status) ?>&sort=<?= htmlspecialchars($sort) ?>" class="btn"><?php echo t('next_page'); ?></a>
            <?php endif; ?>
        </div>
        <div class="back-link-container">
            <a href="index.php" class="btn btn-secondary"><?php echo t('back_to_home'); ?></a>
        </div>
    </div>
</body>
</html>
