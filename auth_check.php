<?php
// auth_check.php
session_start();

// 检查是否已登录
if (!isset($_SESSION['role'])) {
    // 未登录，跳转回登录页
    header("Location: login.html");
    exit();
}

// 可选：你可以在这里添加更多逻辑，比如
// if ($_SESSION['role'] == 'visitor' && basename($_SERVER['PHP_SELF']) == 'admin_panel.php') {
//     die("访客无权访问管理面板");
// }
?>