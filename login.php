<?php
session_start();

// Language switching logic
$available_langs = ['en', 'fr', 'zh-CN'];
$default_lang = 'zh-CN';

if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? $default_lang;

$lang_file = "languages/{$lang}.php";
if (file_exists($lang_file)) {
    include_once $lang_file;
} else {
    // Fallback to default language if file not found
    include_once "languages/{$default_lang}.php";
}

// Function to get translation
function t($key) {
    global $translations;
    return $translations[$key] ?? $key;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('login_title'); ?> - SCRMFSB</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .auth-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-title {
            font-size: 24px;
            font-weight: 600;
            color: #1d2129;
        }
        .auth-form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4e5969;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e5e6eb;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            border-color: #1677ff;
            outline: none;
        }
        .form-actions {
            margin-top: 8px;
        }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            box-sizing: border-box;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
        }
        .btn--primary {
            background-color: #1677ff;
            color: #ffffff;
            border-color: #1677ff;
        }
        .btn--primary:hover {
            background-color: #4096ff;
        }
        .btn--outline {
            background-color: #ffffff;
            color: #4e5969;
            border-color: #e5e6eb;
        }
        .btn--outline:hover {
            background-color: #f7f8fa;
            color: #1677ff;
        }
        .auth-divider {
            margin: 24px 0;
            text-align: center;
            color: #c9cdd4;
            font-size: 12px;
            display: flex;
            align-items: center;
        }
        .auth-divider span {
            padding: 0 10px;
        }
        .auth-divider::before, .auth-divider::after {
            content: '';
            flex-grow: 1;
            height: 1px;
            background-color: #e5e6eb;
        }
        .form-footer-link {
            margin-top: 16px;
            text-align: center;
        }
        .auth-link {
            color: #1677ff;
            text-decoration: none;
            font-size: 14px;
        }
        .auth-link:hover {
            text-decoration: underline;
        }
        .register-link {
             margin-top: 24px;
             text-align: center;
             font-size: 14px;
             color: #86909c;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1 class="auth-title"><?php echo t('login_to_your_account'); ?></h1>
        </div>
        <form class="auth-form auth-form--login" action="login_action.php" method="POST">
            
            <?php if (isset($_GET['error'])): ?>
                <div style="color: red; margin-bottom: 10px; text-align: center;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label" for="login-email"><?php echo t('email_address'); ?></label>
                <input class="form-input" type="email" id="login-email" name="email" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="login-password"><?php echo t('password'); ?></label>
                <input class="form-input" type="password" id="login-password" name="password" required>
            </div>
            
            <div class="form-actions">
                <button class="btn btn--primary" type="submit" name="login_submit"><?php echo t('enter_system'); ?></button>
            </div>

            <div class="auth-divider">
                <span><?php echo t('or'); ?></span>
            </div>

            <div class="form-actions">
                <a href="guest_login.php" class="btn btn--outline"><?php echo t('try_as_guest'); ?></a>
            </div>

            <div class="form-footer-link">
                <a href="#" class="auth-link"><?php echo t('forgot_password'); ?></a>
            </div>
        </form>
        <div class="register-link">
            <?php echo t('no_account_yet'); ?> <a href="register.php" class="auth-link"><?php echo t('register_now'); ?></a>
        </div>
    </div>
</body>
</html>
