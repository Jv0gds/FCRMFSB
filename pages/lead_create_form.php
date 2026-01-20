<?php
// pages/lead_create_form.php
?>
<div class="container public-detail-container">
    <header class="detail-header">
        <h1><?php echo t('publish_new_project'); ?></h1>
        <p><?php echo t('tell_us_what_you_need'); ?></p>
    </header>

    <div class="main-content-detail">
        <form action="pages/lead_create_action.php" method="POST" class="comment-form" style="border-top: none; padding-top: 0;">
            <div class="form-group">
                <label for="company_name" style="font-size: 1.2rem; font-weight: bold;"><?php echo t('give_your_project_a_name'); ?></label>
                <input type="text" id="company_name" name="company_name" class="search-input" style="border-radius: 8px; margin-top: 0.5rem;" placeholder="<?php echo t('e_g_build_a_responsive_website'); ?>" required>
            </div>

            <div class="form-group" style="margin-top: 2rem;">
                <label for="description" style="font-size: 1.2rem; font-weight: bold;"><?php echo t('describe_your_project'); ?></label>
                <textarea id="description" name="description" rows="10" style="margin-top: 0.5rem;" placeholder="<?php echo t('describe_your_project_placeholder'); ?>" required></textarea>
            </div>
            
            <div class="form-actions" style="margin-top: 2rem; text-align: right;">
                <a href="?page=client_dashboard" class="btn btn-secondary"><?php echo t('cancel'); ?></a>
                <button type="submit" class="btn btn-primary"><?php echo t('submit_project'); ?></button>
            </div>
        </form>
    </div>
</div>
