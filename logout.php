<?php
// logout.php

// 1. 启动会话
session_start();

// 2. 清空所有会话变量
$_SESSION = array();

// 3. 销毁会话
session_destroy();

// 4. 重定向到主页，用户将看到游客视图
header("Location: index.php");
exit();
?>
