<?php
// pages/profile_edit.php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die('User not found');
}
?>

<div class="container public-detail-container">
    <header class="detail-header">
        <h1><?php echo t('edit_profile'); ?></h1>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;"><?php echo t('profile_updated_successfully'); ?></div>
    <?php endif; ?>
    
    <form action="pages/profile_edit_action.php" method="POST" enctype="multipart/form-data">
        <div class="detail-content">
            <div class="main-content-detail">
                <div class="form-group">
                    <label><strong><?php echo t('username'); ?></strong></label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="search-input" style="border-radius: 8px;" required>
                </div>
                <div class="form-row" style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label><strong><?php echo t('first_name'); ?></strong></label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" class="search-input" style="border-radius: 8px;" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label><strong><?php echo t('last_name'); ?></strong></label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" class="search-input" style="border-radius: 8px;" required>
                    </div>
                </div>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong><?php echo t('email_address'); ?></strong></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="search-input" style="border-radius: 8px;" required>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label><strong><?php echo t('phone_number'); ?></strong></label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="search-input" style="border-radius: 8px;">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label><strong><?php echo t('birthday'); ?></strong></label>
                    <input type="date" name="birthday" value="<?= htmlspecialchars($user['birthday'] ?? '') ?>" class="search-input" style="border-radius: 8px;">
                </div>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong><?php echo t('gender'); ?></strong></label>
                    <select name="gender" class="search-input" style="border-radius: 8px;">
                        <option value="male" <?= ($user['gender'] ?? '') == 'male' ? 'selected' : '' ?>><?php echo t('male'); ?></option>
                        <option value="female" <?= ($user['gender'] ?? '') == 'female' ? 'selected' : '' ?>><?php echo t('female'); ?></option>
                        <option value="other" <?= ($user['gender'] ?? '') == 'other' ? 'selected' : '' ?>><?php echo t('other'); ?></option>
                    </select>
                </div>

                <hr style="margin-top: 2rem;">
                <h4><?php echo t('change_password'); ?></h4>
                <p><?php echo t('change_password_prompt'); ?></p>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong><?php echo t('new_password'); ?></strong></label>
                    <input type="password" name="password" class="search-input" style="border-radius: 8px;">
                </div>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong><?php echo t('confirm_new_password'); ?></strong></label>
                    <input type="password" name="confirm_password" class="search-input" style="border-radius: 8px;">
                </div>
            </div>
            <aside class="sidebar-detail">
                <div class="info-box">
                    <h4><?php echo t('avatar'); ?></h4>
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="User Avatar" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;">
                    <input type="file" name="avatar">
                </div>
            </aside>
        </div>
        <div class="form-actions" style="margin-top: 2rem; text-align: right;">
            <a href="?page=client_dashboard" class="btn btn-secondary"><?php echo t('back'); ?></a>
            <button type="submit" class="btn btn-primary"><?php echo t('save_changes'); ?></button>
        </div>
    </form>
</div>
