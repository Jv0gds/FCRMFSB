-- 1. 创建数据库 (如果不存在)
CREATE DATABASE IF NOT EXISTS simple_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. 选中该数据库
USE simple_crm;

-- ==========================================
-- 3. 清理旧表 (可选，开发阶段为了重置数据很有用)
-- 注意：上线后请删除这部分，否则数据会丢失！
-- ==========================================
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS companies;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS leads;
DROP TABLE IF EXISTS deals;
DROP TABLE IF EXISTS activities;
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- 4. 创建数据表结构
-- ==========================================

-- 用户表 (包含5种角色)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('visitor', 'registered', 'sales_rep', 'sales_manager', 'admin') DEFAULT 'registered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 公司表 (客户主体)
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    industry VARCHAR(50),
    phone VARCHAR(20),
    website VARCHAR(100),
    owner_id INT, -- 负责的销售ID
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 联系人表 (公司下的具体人员)
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    position VARCHAR(50), -- 职位
    company_id INT, -- 关联公司
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
);

-- 潜在客户/线索表 (Leads)
CREATE TABLE leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    company_name VARCHAR(100), -- 线索阶段可能还没建立公司记录
    source VARCHAR(50), -- 来源：网站、介绍、广告
    status ENUM('new', 'contacted', 'qualified', 'lost') DEFAULT 'new',
    assigned_to INT, -- 分配给谁
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 交易/商机表 (Deals - 包含管道阶段)
CREATE TABLE deals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL, -- 如 "ABC公司采购案"
    value DECIMAL(10, 2) DEFAULT 0.00, -- 预计金额
    stage ENUM('discovery', 'proposal', 'negotiation', 'closed_won', 'closed_lost') DEFAULT 'discovery',
    company_id INT,
    contact_id INT,
    assigned_to INT,
    expected_close_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 活动/跟进记录表 (Activities)
CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('call', 'email', 'meeting', 'note') NOT NULL,
    description TEXT,
    activity_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id INT, -- 谁执行的操作
    related_to_type ENUM('lead', 'contact', 'deal'), -- 关联对象类型
    related_to_id INT, -- 关联对象ID
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 5. 插入预置数据 (Seeds)
-- ==========================================

-- 插入用户
-- 密码均为 "123456" (哈希值: $2y$10$tH6F... 实际开发请用 password_hash 生成)
INSERT INTO users (username, email, password, role) VALUES 
('管理员', 'admin@crm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('销售经理张', 'manager@crm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales_manager'),
('销售小王', 'wang@crm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sales_rep');

-- 插入公司
INSERT INTO companies (name, industry, owner_id) VALUES 
('科技先锋有限公司', '互联网', 3),
('环球贸易集团', '零售', 3);

-- 插入联系人
INSERT INTO contacts (first_name, last_name, email, company_id) VALUES 
('雷', '李', 'lilei@tech.com', 1),
('梅梅', '韩', 'han@global.com', 2);

-- 插入线索
INSERT INTO leads (first_name, last_name, company_name, status, assigned_to) VALUES 
('强', '光头', '森林伐木场', 'new', 3);

-- 插入交易
INSERT INTO deals (title, value, stage, company_id, assigned_to) VALUES 
('服务器集群采购', 50000.00, 'proposal', 1, 3);