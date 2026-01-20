<?php
// db.php
// 数据库配置信息
$host = 'localhost';
$db   = 'simple_crm';      // 对应我们刚才SQL文件里的数据库名
$user = 'root';            // 本地开发通常是 root
$pass = '';                // 本地开发通常为空，如果是MAMP/XAMPP可能是 'root'
$charset = 'utf8mb4';      // 支持中文的关键

// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// PDO 配置选项
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // 报错模式：抛出异常
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // 默认返回关联数组 ['name' => '张三']
    PDO::ATTR_EMULATE_PREPARES   => false,                // 禁用模拟预处理，增加安全性
];

try {
    // 创建 PDO 实例，建立连接
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 如果连接失败，抛出错误（生产环境不要直接输出错误详情）
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>