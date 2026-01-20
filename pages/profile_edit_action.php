<?php
session_start();
include '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic info
    $username = $_POST['username'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? null;
    $birthday = $_POST['birthday'] ?? null;
    $gender = $_POST['gender'] ?? null;
    
    // Password info
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($first_name) || empty($last_name)) {
        die('Username, email, first name, and last name are required.');
    }

    // Handle avatar upload
    $avatar_path = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "../uploads/avatars/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $filename = uniqid() . '_' . basename($_FILES["avatar"]["name"]);
        $target_file = $target_dir . $filename;
        
        // Basic validation for image upload
        $check = getimagesize($_FILES["avatar"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $avatar_path = "uploads/avatars/" . $filename;
            }
        }
    }

    // Build the SQL query
    $sql_parts = [
        "username = ?", "first_name = ?", "last_name = ?",
        "email = ?", "phone = ?", "birthday = ?", "gender = ?"
    ];
    $params = [$username, $first_name, $last_name, $email, $phone, $birthday, $gender];

    if ($avatar_path) {
        $sql_parts[] = "avatar = ?";
        $params[] = $avatar_path;
    }
    
    $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    $params[] = $user_id;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Update session username
    $_SESSION['username'] = $username;

    // Update password if provided
    if (!empty($password)) {
        if ($password !== $confirm_password) {
            die('Passwords do not match.');
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_pass = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_pass = $pdo->prepare($sql_pass);
        $stmt_pass->execute([$hashed_password, $user_id]);
    }

    // Redirect back to the edit page with a success message
    header("Location: ../index.php?page=profile_edit&success=1");
    exit();

} else {
    header("Location: ../index.php?page=profile_edit");
    exit();
}
