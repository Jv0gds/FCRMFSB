<?php require 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>仪表盘 - CRM</title>
</head>
<body class="app-body">
    
    <aside class="sidebar">
        <div class="sidebar__brand">SimpleCRM</div>
        <nav class="sidebar__nav">
            <ul class="sidebar__list">
                <li class="sidebar__item sidebar__item--active"><a href="dashboard.html">仪表盘</a></li>
                <li class="sidebar__item"><a href="list_view.html?type=leads">潜在客户 (Leads)</a></li>
                <li class="sidebar__item"><a href="list_view.html?type=contacts">联系人</a></li>
                <li class="sidebar__item"><a href="list_view.html?type=companies">公司</a></li>
                <li class="sidebar__item"><a href="list_view.html?type=deals">交易 (Deals)</a></li>
                <li class="sidebar__item"><a href="admin_panel.html">系统管理</a></li>
            </ul>
        </nav>
        <div class="sidebar__user">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <span class="user-role" style="font-size:12px; color:#888;">(<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
            <a href="logout.php" class="btn-logout">退出</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h2 class="page-title">工作台</h2>
            <div class="top-bar__actions">
                <a href="notifications.html" class="icon-btn">通知 <span class="badge">3</span></a>
                <a href="form_view.html" class="btn btn--primary">+ 新建项目</a>
            </div>
        </header>

        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-card__value">12</div>
                <div class="stat-card__label">本周新线索</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value">¥ 50,000</div>
                <div class="stat-card__label">预计成交额</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__value">5</div>
                <div class="stat-card__label">待办活动</div>
            </div>

            <div class="panel panel--wide">
                <div class="panel__header">
                    <h3>销售管道概览</h3>
                </div>
                <div class="pipeline-preview">
                    <div class="stage-column">
                        <h4>初步沟通 (2)</h4>
                        <div class="deal-card-mini">某科技公司采购</div>
                    </div>
                    <div class="stage-column">
                        <h4>方案报价 (1)</h4>
                        <div class="deal-card-mini">零售商扩建项目</div>
                    </div>
                    <div class="stage-column">
                        <h4>谈判 (3)</h4>
                        </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>