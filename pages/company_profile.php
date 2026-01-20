<?php
// pages/company_profile.php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user to get their company_id
$stmt_user = $pdo->prepare("SELECT company_id FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

$company = null;
if ($user && $user['company_id']) {
    $stmt_company = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt_company->execute([$user['company_id']]);
    $company = $stmt_company->fetch();
}
?>

<div class="container public-detail-container">
    <header class="detail-header">
        <h1><?php echo t('company_information'); ?></h1>
        <p><?= $company ? t('update_your_company_profile') : t('fill_in_your_company_information') ?></p>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;"><?php echo t('company_information_updated_successfully'); ?></div>
    <?php endif; ?>
    
    <div class="main-content-detail">
        <form action="pages/company_profile_action.php" method="POST" class="comment-form" style="border-top: none; padding-top: 0;">
            <div class="form-group">
                <label><strong><?php echo t('company_name'); ?></strong></label>
                <input type="text" name="name" value="<?= htmlspecialchars($company['name'] ?? '') ?>" class="search-input" style="border-radius: 8px;" required>
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label><strong><?php echo t('industry'); ?></strong></label>
                <input type="text" name="industry" value="<?= htmlspecialchars($company['industry'] ?? '') ?>" class="search-input" style="border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label><strong><?php echo t('company_phone'); ?></strong></label>
                <input type="text" name="phone" value="<?= htmlspecialchars($company['phone'] ?? '') ?>" class="search-input" style="border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-top: 1rem;">
                <label><strong><?php echo t('company_website'); ?></strong></label>
                <input type="text" name="website" value="<?= htmlspecialchars($company['website'] ?? '') ?>" class="search-input" style="border-radius: 8px;">
            </div>
            <div class="form-actions" style="margin-top: 2rem; text-align: right;">
                <a href="?page=client_dashboard" class="btn btn-secondary"><?php echo t('back'); ?></a>
                <button type="submit" class="btn btn-primary"><?php echo t('save'); ?></button>
            </div>
        </form>
    </div>
</div>
