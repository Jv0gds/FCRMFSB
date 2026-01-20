<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $industry = $_POST['industry'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $website = $_POST['website'] ?? null;

    if (empty($name)) {
        die('Company name is required.');
    }

    // Check if user already has a company
    $stmt_user = $pdo->prepare("SELECT company_id FROM users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch();
    $company_id = $user ? $user['company_id'] : null;

    if ($company_id) {
        // Update existing company
        $sql = "UPDATE companies SET name = ?, industry = ?, phone = ?, website = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $industry, $phone, $website, $company_id]);
    } else {
        // Create new company
        $sql = "INSERT INTO companies (name, industry, phone, website) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $industry, $phone, $website]);
        $new_company_id = $pdo->lastInsertId();

        // Link company to user
        $sql_user = "UPDATE users SET company_id = ? WHERE id = ?";
        $stmt_user_update = $pdo->prepare($sql_user);
        $stmt_user_update->execute([$new_company_id, $user_id]);
    }

    header("Location: ../index.php?page=company_profile&success=1");
    exit();
}
