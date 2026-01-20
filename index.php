<?php session_start(); ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCRMFSB - CRM System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-shell">
        <?php include 'templates/navbar.php'; ?>

        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="?page=dashboard">仪表盘</a></li>
                    <li class="menu-header">客户管理</li>
                    <li><a href="?page=leads_list">潜在客户</a></li>
                    <li><a href="?page=contacts_list">联系人</a></li>
                    <li><a href="?page=companies_list">公司</a></li>
                    <li class="menu-header">销售流程</li>
                    <li><a href="?page=deals_list">商机</a></li>
                    <li><a href="?page=activities_list">活动</a></li>
                    <li class="menu-header">管理面板</li>
                    <li><a href="?page=admin_panel">系统设置</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <?php
            // 定义一个允许页面访问的白名单，以增强安全性
            $allowed_pages = [
                'dashboard', 'leads_list', 'contacts_list', 'companies_list',
                'deals_list', 'activities_list', 'notifications', 'admin_panel',
                'lead_create', 'lead_detail', 'lead_edit',
                'contact_create', 'contact_detail', 'contact_edit',
                'company_create', 'company_detail', 'company_edit'
            ];

            // 获取请求的页面，如果没有指定，则默认为'dashboard'
            $page = $_GET['page'] ?? 'dashboard';

            // 检查请求的页面是否在白名单中
            if (in_array($page, $allowed_pages)) {
                $file_path = "pages/{$page}.php";

                // 检查对应的文件是否存在
                if (file_exists($file_path)) {
                    include $file_path;
                } else {
                    // 文件不存在，显示一个错误页面（或包含一个通用的404页面）
                    echo "<h2>错误 404 - 页面未找到</h2>";
                    echo "<p>我们找不到 '{$page}' 对应的页面文件。</p>";
                }
            } else {
                // 如果页面不在白名单中，为了安全起见，也显示403错误
                echo "<h2>错误 403 - 禁止访问</h2>";
                echo "<p>您无权访问此页面。</p>";
            }
            ?>
        </main>
    </div>
</body>
</html>
