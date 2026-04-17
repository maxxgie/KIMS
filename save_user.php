<?php
session_start();
include 'db_connect.php';

// Role Authorization Check: Only Admin or Super Admin allowed to create accounts
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Super Admin')) {
    die("Critical Security Violation: Unauthorized account creation attempt has been blocked and logged.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['staff_name'];
    $user = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Securely hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Security Best Practice: Use prepared statements to prevent SQL injection.
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, role, password) VALUES (?, ?, ?, ?)");

    // This check helps diagnose schema errors like a missing column.
    if ($stmt === false) {
        die("Database Prepare Error: " . $conn->error . ". Please check if the 'role' column exists in the 'users' table.");
    }

    $stmt->bind_param("ssss", $name, $user, $role, $hashed_password);

    if ($stmt->execute()) {
        header("Location: admin.php?status=user_created");
    } else {
        die("Database Execute Error: " . $stmt->error);
    }
    $stmt->close();
}
$conn->close();
exit();
?>