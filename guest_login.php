<?php
// guest_login.php
session_start();

// 设置访客 Session 数据
$_SESSION['user_id'] = 0; // 0 表示非数据库用户
$_SESSION['username'] = '访客用户';
$_SESSION['role'] = 'visitor'; // 关键：设置为 visitor 角色

// 跳转到仪表盘
header("Location: index.php");
exit();
?>