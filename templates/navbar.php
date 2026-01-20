<?php // templates/navbar.php ?>
<header class="top-navbar">
    <div class="navbar-left">
        <span class="logo">SCRM</span>
        <input type="text" placeholder="全局搜索..." class="global-search">
    </div>
    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            
            <a href="?page=notifications" class="nav-icon" title="通知中心">🔔</a>
            
            <div class="dropdown">
                <button class="nav-icon create-btn">+</button>
                <div class="dropdown-content">
                    <a href="?page=lead_create">新建潜在客户</a>
                    <a href="?page=contact_create">新建联系人</a>
                    <a href="?page=company_create">新建公司</a>
                </div>
            </div>

            <div class="dropdown">
                <div class="user-profile" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <img src="https://via.placeholder.com/32" alt="User Avatar" class="user-avatar">
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="dropdown-content">
                    <a href="?page=profile">个人资料</a>
                    <a href="logout.php">登出</a>
                </div>
            </div>

        <?php else: ?>

            <a href="login.html" class="nav-button">登录</a>
            <a href="register.html" class="nav-button">注册</a>

        <?php endif; ?>
    </div>
</header>