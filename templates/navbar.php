<?php
    // templates/navbar.php
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    $current_page_param = $_GET['page'] ?? '';
    $current_lang = $_GET['lang'] ?? 'en'; // Assuming 'en' is the default language
?>
<header class="top-navbar">
    <link rel="stylesheet" href="css/components/language_switcher.css">
    <div class="navbar-left">
        <a href="/" style="text-decoration: none; color: inherit;"><span class="logo">SCRM</span></a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] != 'registered'): ?>
        <input type="text" placeholder="<?php echo t('global_search'); ?>" class="global-search">
        <?php endif; ?>
    </div>
    <div class="navbar-right">
        <div class="language-carousel-container">
            <div class="language-carousel" id="languageCarousel">
                <div class="language-item <?php echo ($current_lang == 'en' ? 'active' : ''); ?>" data-lang="en">EN</div>
                <div class="language-item <?php echo ($current_lang == 'fr' ? 'active' : ''); ?>" data-lang="fr">FR</div>
                <div class="language-item <?php echo ($current_lang == 'zh-CN' ? 'active' : ''); ?>" data-lang="zh-CN">CN</div>
            </div>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            
            <?php // Role-based navigation switching ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'registered'): ?>
                
                <?php // Customer Portal Navigation ?>
                <a href="index.php?page=client_dashboard" class="nav-button <?php echo ($current_script == 'index.php' && ($current_page_param == 'client_dashboard' || $current_page_param == '')) ? 'active' : ''; ?>"><?php echo t('projects_overview'); ?></a>
                <a href="index.php?page=my_applications" class="nav-button <?php echo ($current_script == 'index.php' && $current_page_param == 'my_applications') ? 'active' : ''; ?>"><?php echo t('my_applications'); ?></a>
                <a href="pages/notifications.php" class="nav-button <?php echo ($current_script == 'notifications.php') ? 'active' : ''; ?>"><?php echo t('notifications'); ?></a>
                <a href="index.php?page=messages" class="nav-button <?php echo ($current_script == 'index.php' && $current_page_param == 'messages') ? 'active' : ''; ?>"><?php echo t('messages'); ?></a>
                <a href="public_list_view.php" class="nav-button <?php echo ($current_script == 'public_list_view.php') ? 'active' : ''; ?>"><?php echo t('public_directory'); ?></a>
                <a href="pages/contact_admin.php?recipient_id=1&subject=联系客服" class="nav-button <?php echo ($current_script == 'contact_admin.php') ? 'active' : ''; ?>"><?php echo t('contact_support'); ?></a>

            <?php else: ?>

                <?php // Internal User Navigation ?>
                <a href="pages/notifications.php" class="nav-icon" title="<?php echo t('notifications'); ?>">🔔</a>
                <a href="index.php?page=messages" class="nav-icon" title="<?php echo t('messages'); ?>">✉️</a>
                <a href="public_list_view.php" class="nav-icon" title="<?php echo t('public_directory'); ?>">📂</a>
                
                <div class="dropdown">
                    <button class="nav-icon create-btn">+</button>
                    <div class="dropdown-content">
                        <a href="?page=lead_create"><?php echo t('new_lead'); ?></a>
                        <a href="?page=contact_create"><?php echo t('new_contact'); ?></a>
                        <a href="?page=company_create"><?php echo t('new_company'); ?></a>
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
                    <a href="index.php?page=profile_edit"><?php echo t('personal_profile'); ?></a>
                    <a href="index.php?page=company_profile"><?php echo t('company_information'); ?></a>
                    <a href="logout.php"><?php echo t('logout'); ?></a>
                </div>
            </div>

        <?php else: ?>

            <?php // Not logged in ?>
            <a href="login.php" class="nav-button"><?php echo t('login'); ?></a>
            <a href="register.php" class="nav-button"><?php echo t('register'); ?></a>

        <?php endif; ?>
    </div>
    <script src="js/components/language_switcher.js"></script>
</header>
