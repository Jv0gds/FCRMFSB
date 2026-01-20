<?php
// login_action.php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 简单验证
    if (empty($email) || empty($password)) {
        // 修改 1: 英文提示 "Please fill in all fields"
        header("Location: login.php?error=Please fill in all fields"); 
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
        $_SESSION['role'] = $user['role']; 

        if (isset($_SESSION['role']) && $_SESSION['role'] == 'registered') {
            header("Location: index.php?page=client_dashboard");
        } else {
            header("Location: index.php?page=dashboard");
        }
        exit();
    } else {
        // 修改 2: 英文提示 "Incorrect email or password"
        // 注意：我把 login.html 改为了 login.php (见下文解释)
        header("Location: login.php?error=Incorrect email or password"); 
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>