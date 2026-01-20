<?php
// login_action.php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 简单验证
    if (empty($email) || empty($password)) {
        header("Location: login.html?error=请填写所有字段");
        exit();
    }

    // 查询用户
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 验证密码
    if ($user && password_verify($password, $user['password'])) {
        // 登录成功，设置 Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // 保存角色用于权限控制

        // 根据角色跳转到不同页面 (可选)
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'registered') {
            header("Location: index.php?page=client_dashboard");
        } else {
            header("Location: index.php?page=dashboard");
        }
        exit();
    } else {
        // 登录失败
        header("Location: login.html?error=邮箱或密码错误");
        exit();
    }
} else {
    header("Location: login.html");
    exit();
}
?>