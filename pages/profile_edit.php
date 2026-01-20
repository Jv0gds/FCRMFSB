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
        <h1>编辑个人资料</h1>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">资料更新成功！</div>
    <?php endif; ?>
    
    <form action="pages/profile_edit_action.php" method="POST" enctype="multipart/form-data">
        <div class="detail-content">
            <div class="main-content-detail">
                <div class="form-group">
                    <label><strong>用户名</strong></label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="search-input" style="border-radius: 8px;" required>
                </div>
                <div class="form-row" style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label><strong>名</strong></label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" class="search-input" style="border-radius: 8px;" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label><strong>姓</strong></label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" class="search-input" style="border-radius: 8px;" required>
                    </div>
                </div>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong>电子邮箱</strong></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="search-input" style="border-radius: 8px;" required>
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label><strong>联系电话</strong></label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="search-input" style="border-radius: 8px;">
                </div>
                <div class="form-group" style="margin-top: 1rem;">
                    <label><strong>生日</strong></label>
                    <input type="date" name="birthday" value="<?= htmlspecialchars($user['birthday'] ?? '') ?>" class="search-input" style="border-radius: 8px;">
                </div>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong>性别</strong></label>
                    <select name="gender" class="search-input" style="border-radius: 8px;">
                        <option value="male" <?= ($user['gender'] ?? '') == 'male' ? 'selected' : '' ?>>男</option>
                        <option value="female" <?= ($user['gender'] ?? '') == 'female' ? 'selected' : '' ?>>女</option>
                        <option value="other" <?= ($user['gender'] ?? '') == 'other' ? 'selected' : '' ?>>其他</option>
                    </select>
                </div>

                <hr style="margin-top: 2rem;">
                <h4>修改密码</h4>
                <p>如果您想修改密码，请填写以下字段。否则请留空。</p>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong>新密码</strong></label>
                    <input type="password" name="password" class="search-input" style="border-radius: 8px;">
                </div>
                 <div class="form-group" style="margin-top: 1rem;">
                    <label><strong>确认新密码</strong></label>
                    <input type="password" name="confirm_password" class="search-input" style="border-radius: 8px;">
                </div>
            </div>
            <aside class="sidebar-detail">
                <div class="info-box">
                    <h4>头像</h4>
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="User Avatar" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-bottom: 1rem;">
                    <input type="file" name="avatar">
                </div>
            </aside>
        </div>
        <div class="form-actions" style="margin-top: 2rem; text-align: right;">
            <a href="?page=client_dashboard" class="btn btn-secondary">返回</a>
            <button type="submit" class="btn btn-primary">保存更改</button>
        </div>
    </form>
</div>
