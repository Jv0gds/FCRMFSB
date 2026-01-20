<?php
// pages/dashboard.php
// This is the default content for the main area.
?>

<h2><?php echo t('dashboard'); ?></h2>
<p><?php echo t('welcome_back'); ?></p>
<p><?php echo t('dashboard_info'); ?></p>

<div style="margin-top: 20px; display: flex; gap: 20px;">
    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1; background: #fff;">
        <h3><?php echo t('new_leads'); ?></h3>
        <p style="font-size: 2em; font-weight: bold;">12</p>
    </div>
    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1; background: #fff;">
        <h3><?php echo t('this_month_sales'); ?></h3>
        <p style="font-size: 2em; font-weight: bold;">$8,520</p>
    </div>
    <div style="border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1; background: #fff;">
        <h3><?php echo t('todo_activities'); ?></h3>
        <p style="font-size: 2em; font-weight: bold;">5</p>
    </div>
</div>
