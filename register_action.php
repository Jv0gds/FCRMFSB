<?php
session_start();
// 包含数据库连接文件
include 'db.php';

// 检查是否为 POST 请求
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
   $first_name = $_POST['first_name'];
   $last_name = $_POST['last_name'];

    // 1. 验证密码
    if ($password !== $confirm_password) {
        echo "错误：两次输入的密码不匹配。";
        exit();
    }

    $complexityRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{1,}$/';
    if (!preg_match($complexityRegex, $password)) {
        echo "错误：密码需要至少包含大写、小写字母及一个特殊符号。";
        exit();
    }

    // 2. 检查邮箱是否已存在
    // 准备 SQL 语句，使用占位符 :email 防止 SQL 注入
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    // 执行查询，并传入邮箱地址
    $stmt->execute(['email' => $email]);

    // 如果查询结果的行数大于 0，说明邮箱已存在
    if ($stmt->rowCount() > 0) {
        // 输出错误信息并终止脚本
        echo "错误：该邮箱已被注册。";
        exit();
    } else {
        // 2. 对密码进行哈希处理
        // 使用 password_hash 函数，这是 PHP 推荐的安全做法
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. 插入新用户数据
        // 准备插入数据的 SQL 语句
        $stmt_insert = $pdo->prepare("INSERT INTO users (username, email, password, role, first_name, last_name) VALUES (:username, :email, :password, 'registered', :first_name, :last_name)");

        // 执行插入操作，并绑定参数
        if ($stmt_insert->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password,
           'first_name' => $first_name,
           'last_name' => $last_name
        ])) {
            // 4. 注册成功，显示成功信息并倒计时跳转
            echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册成功</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 100px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">注册成功!</h4>
            <p>您已成功注册，页面将在 <span id="countdown">3</span> 秒后自动跳转到登录页面。</p>
            <hr>
            <p class="mb-0">如果页面没有自动跳转，请 <a href="login.html">点击这里</a>。</p>
        </div>
    </div>
    <script>
        let countdownElement = document.getElementById('countdown');
        let seconds = 3;

        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                window.location.href = 'login.html';
            }
        }, 1000);
    </script>
</body>
</html>
HTML;
            exit(); // 确保脚本停止执行
        } else {
            // 如果插入失败，输出错误信息
            echo "错误：注册失败，请稍后再试。";
        }
    }
}
?>
