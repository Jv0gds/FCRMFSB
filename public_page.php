<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('welcome_to_scrmfsb'); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo t('welcome_header'); ?></h1>
            <p><?php echo t('welcome_subheader'); ?></p>
        </header>

        <section class="features">
            <h2><?php echo t('system_features'); ?></h2>
            <ul>
                <li><?php echo t('feature_leads_contacts'); ?></li>
                <li><?php echo t('feature_sales_tracking'); ?></li>
                <li><?php echo t('feature_activity_scheduling'); ?></li>
                <li><?php echo t('feature_reporting'); ?></li>
            </ul>
        </section>

        <section class="call-to-action">
            <h2><?php echo t('get_started'); ?></h2>
            <p><?php echo t('login_or_register_prompt'); ?></p>
            <a href="login.php" class="button"><?php echo t('login'); ?></a>
            <a href="register.php" class="button"><?php echo t('register'); ?></a>
            <a href="public_list_view.php" class="button"><?php echo t('browse_public_directory'); ?></a>
        </section>

        <footer>
            <p><?php echo t('copyright_notice'); ?></p>
        </footer>
    </div>
</body>
</html>
