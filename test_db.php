<?php
require 'db.php';

if ($pdo) {
    echo "<h1>🎉 数据库连接成功！</h1>";
    echo "<p>PHP 成功握手 MySQL。</p>";
    
    // 顺便测试读取一下数据
    $stmt = $pdo->query("SELECT count(*) as count FROM users");
    $row = $stmt->fetch();
    echo "<p>当前数据库里有 <strong>" . $row['count'] . "</strong> 个用户。</p>";
}
?>