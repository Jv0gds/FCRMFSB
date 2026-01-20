<?php
session_start();

include_once 'i18n.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCRMFSB - CRM System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-shell <?php echo (isset($_SESSION["role"]) && $_SESSION["role"] === "registered") ? "no-sidebar" : ""; ?>">
        <?php include 'templates/navbar.php'; ?>

        <?php if (isset($_SESSION["role"]) && $_SESSION["role"] !== "registered"): ?>
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="?page=dashboard"><?php echo t('dashboard'); ?></a></li>
                    <li class="menu-header"><?php echo t('customer_management'); ?></li>
                    <li><a href="?page=leads_list"><?php echo t('potential_customers'); ?></a></li>
                    <li><a href="?page=contacts_list"><?php echo t('contacts'); ?></a></li>
                    <li><a href="?page=companies_list"><?php echo t('companies'); ?></a></li>
                    <li class="menu-header"><?php echo t('sales_process'); ?></li>
                    <li><a href="?page=deals_list"><?php echo t('opportunities'); ?></a></li>
                    <li><a href="?page=activities_list"><?php echo t('activities'); ?></a></li>
                    <li class="menu-header"><?php echo t('management_panel'); ?></li>
                    <li><a href="?page=admin_panel"><?php echo t('system_settings'); ?></a></li>
                </ul>
            </nav>
        </aside>
        <?php endif; ?>

        <main class="main-content">
            <?php
            // 检查用户是否已登录
            if (isset($_SESSION["user_id"])) {
                // 根据用户角色决定显示内容
                if ($_SESSION["role"] === "registered") {
                    // 注册用户看到客户端仪表盘
                    $allowed_pages = ['client_dashboard', 'lead_create_form', 'profile_edit', 'messages', 'conversation', 'send_message', 'start_conversation', 'company_profile', 'my_applications', 'support', 'edit_application', 'delete_application', 'edit_application_action'];
                    $page = $_GET['page'] ?? 'client_dashboard';
                     if (in_array($page, $allowed_pages) && file_exists("pages/{$page}.php")) {
                        include "pages/{$page}.php";
                    } else {
                        include "pages/client_dashboard.php";
                    }
                } else {
                    // 其他员工角色看到CRM仪表盘
                    $allowed_pages = [
                        'dashboard', 'leads_list', 'contacts_list', 'companies_list',
                        'deals_list', 'activities_list', 'notifications', 'admin_panel',
                        'lead_create', 'lead_detail', 'lead_edit',
                        'contact_create', 'contact_detail', 'contact_edit',
                        'company_create', 'company_detail', 'company_edit',
                        'messages', 'conversation', 'send_message', 'start_conversation',
                        'support'
                    ];
                    $page = $_GET['page'] ?? 'dashboard';
                    if (in_array($page, $allowed_pages) && file_exists("pages/{$page}.php")) {
                        include "pages/{$page}.php";
                    } else {
                        include "pages/dashboard.php"; // 默认页面
                    }
                }
            } else {
                // 用户未登录，显示公开内容
                include 'public_page.php';
            }
            ?>
        </main>
    </div>
</body>
</html>
