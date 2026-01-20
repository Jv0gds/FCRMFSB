<?php
// install.php
// 警告：这个脚本会重置数据库，生产环境请删除！

$host = 'localhost';
$user = 'root';
$pass = ''; // 你的数据库密码

try {
    // 1. 连接到 MySQL 服务 (不指定数据库名)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. 读取 sql 文件内容
    $sql = file_get_contents('db_setup.sql');

    // 3. 执行 SQL
    $pdo->exec($sql);

    echo "<h1>数据库安装成功！</h1>";
    echo "<p>所有表和数据已重置。</p>";
    echo "<a href='login.php'>去登录</a>";

} catch (PDOException $e) {
    echo "安装失败: " . $e->getMessage();
}
?>