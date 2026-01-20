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
    <title><?php echo t('register_title'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mt-5"><?php echo t('register_title'); ?></h2>
                <form action="register_action.php" method="POST" id="registerForm">
                    <div class="mb-3">
                        <label for="username" class="form-label"><?php echo t('username'); ?></label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                       <label for="first_name" class="form-label"><?php echo t('real_surname'); ?></label>
                       <input type="text" class="form-control" id="first_name" name="first_name" required>
                   </div>
                   <div class="mb-3">
                       <label for="last_name" class="form-label"><?php echo t('real_name'); ?></label>
                       <input type="text" class="form-control" id="last_name" name="last_name" required>
                   </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><?php echo t('email_address'); ?></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><?php echo t('password'); ?></label>
                        <input type="password" class="form-control" id="password" name="password" required title="<?php echo t('password_help'); ?>">
                        <div id="passwordHelp" class="form-text"><?php echo t('password_help'); ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label"><?php echo t('confirm_password'); ?></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div id="passwordError" class="alert alert-danger" style="display: none;"></div>
                    <button type="submit" class="btn btn-primary"><?php echo t('register_button'); ?></button>
                </form>
                <p class="mt-3"><?php echo t('already_have_account'); ?> <a href="login.php"><?php echo t('login_here'); ?></a>.</p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordError = document.getElementById('passwordError');
        const passwordHelp = document.getElementById('passwordHelp');

        function validatePassword() {
            // Reset state
            passwordError.style.display = 'none';
            passwordError.textContent = '';
            password.classList.remove('is-invalid');
            confirmPassword.classList.remove('is-invalid');
            passwordHelp.style.color = '#6c757d'; // Default text color

            const passwordValue = password.value;
            const confirmPasswordValue = confirmPassword.value;

            // Regex for password complexity: at least one lowercase, one uppercase, and one special character
            const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{1,}$/;

            let errors = [];

            if (!complexityRegex.test(passwordValue)) {
                errors.push('<?php echo t('password_complexity_error'); ?>');
                password.classList.add('is-invalid');
                passwordHelp.style.color = 'red';
            } else {
                 passwordHelp.style.color = 'green';
            }

            if (passwordValue !== confirmPasswordValue && confirmPasswordValue !== '') {
                errors.push('<?php echo t('password_mismatch_error'); ?>');
                confirmPassword.classList.add('is-invalid');
            }
            
            if (errors.length > 0) {
                passwordError.style.display = 'block';
                passwordError.innerHTML = errors.join('<br>');
                return false;
            }

            return true;
        }

        // Add event listeners for real-time validation
        password.addEventListener('keyup', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);

        form.addEventListener('submit', function(event) {
            if (!validatePassword()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    </script>
</body>
</html>
