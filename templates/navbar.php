<?php // templates/navbar.php ?>
<header class="top-navbar">
    <div class="navbar-left">
        <a href="/" style="text-decoration: none; color: inherit;"><span class="logo">SCRM</span></a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] != 'registered'): ?>
        <input type="text" placeholder="全局搜索..." class="global-search">
        <?php endif; ?>
    </div>
    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            
            <?php // Role-based navigation switching ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'registered'): ?>
                
                <?php // Customer Portal Navigation ?>
                <a href="index.php?page=client_dashboard" class="nav-button">项目概览</a>
                <a href="index.php?page=my_applications" class="nav-button">我的申请</a>
                <a href="index.php?page=messages" class="nav-button">信箱</a>
                <a href="public_list_view.php" class="nav-button">公开目录</a>
                <a href="index.php?page=support" class="nav-button">联系客服</a>

            <?php else: ?>

                <?php // Internal User Navigation ?>
                <a href="?page=notifications" class="nav-icon" title="通知中心">🔔</a>
                <a href="index.php?page=messages" class="nav-icon" title="信箱">✉️</a>
                <a href="public_list_view.php" class="nav-icon" title="公开目录">📂</a>
                
                <div class="dropdown">
                    <button class="nav-icon create-btn">+</button>
                    <div class="dropdown-content">
                        <a href="?page=lead_create">新建潜在客户</a>
                        <a href="?page=contact_create">新建联系人</a>
                        <a href="?page=company_create">新建公司</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php // Common User Profile Dropdown for all logged-in users ?>
            <div class="dropdown">
                <div class="user-profile" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <img src="https://via.placeholder.com/32" alt="User Avatar" class="user-avatar">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="dropdown-content">
                    <a href="index.php?page=profile_edit">个人资料</a>
                    <a href="index.php?page=company_profile">公司信息</a>
                    <a href="logout.php">登出</a>
                </div>
            </div>

        <?php else: ?>

            <?php // Not logged in ?>
            <a href="login.html" class="nav-button">登录</a>
            <a href="register.html" class="nav-button">注册</a>

        <?php endif; ?>
    </div>
</header>
