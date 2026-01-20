<?php
session_start();
include '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];

    // Fetch user info, including company_id
    $stmt_user = $pdo->prepare("SELECT first_name, last_name, email, phone, company_id FROM users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $user = $stmt_user->fetch();

    if (!$user) {
        die('Error: User not found.');
    }

    // Fetch company info if user is associated with a company
    $company_name = 'N/A';
    $company_phone = $user['phone']; // Default to user's phone
    if ($user['company_id']) {
        $stmt_company = $pdo->prepare("SELECT name, phone FROM companies WHERE id = ?");
        $stmt_company->execute([$user['company_id']]);
        $company = $stmt_company->fetch();
        if ($company) {
            $company_name = $company['name'];
            $company_phone = $company['phone'] ?? $user['phone']; // Use company phone if available
        }
    }
    
    // Get form data
    $title = $_POST['company_name'] ?? ''; // This is the project title from the form
    $description = $_POST['description'] ?? '';
    $status = 'new';
    $source = 'website';

    // Validate required fields
    if (empty($title) || empty($description)) {
        die('Error: Project name and description are required.');
    }

    // Prepare SQL insert statement with the new title column
    $sql = "INSERT INTO leads (first_name, last_name, email, phone, company_name, title, description, source, status, created_by_user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    // Execute insert
    try {
        $stmt->execute([
            $user['first_name'] ?? 'N/A',
            $user['last_name'] ?? 'N/A',
            $user['email'] ?? null,
            $company_phone,
            $company_name,
            $title,
            $description,
            $source,
            $status,
            $user_id
        ]);
        
        header("Location: ../index.php?page=client_dashboard&success=1");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php?page=lead_create_form");
    exit();
}
