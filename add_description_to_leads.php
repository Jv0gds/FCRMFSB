<?php
include 'db.php';

try {
    // Add description column to leads table
    $pdo->exec("ALTER TABLE leads ADD COLUMN description TEXT AFTER company_name");

    // Add some sample descriptions
    $pdo->exec("UPDATE leads SET description = '这是一个关于音乐工作室的业务线索，寻求合作机会。' WHERE company_name = '音乐工作室'");
    $pdo->exec("UPDATE leads SET description = '一家创新的设计公司，需要市场推广服务。我们提供尖端的设计解决方案，帮助品牌脱颖而出。' WHERE company_name = '设计公司'");
    $pdo->exec("UPDATE leads SET description = '这家科技创业公司正在寻找种子轮投资。我们的项目专注于人工智能领域，并已获得初步的市场验证。' WHERE company_name = '科技创业公司'");
    $pdo->exec("UPDATE leads SET description = '一个大型餐饮集团，计划扩展其业务，需要新的供应商。' WHERE company_name = '餐饮集团'");

    echo "Table 'leads' updated successfully. 'description' column added and populated with sample data.";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
