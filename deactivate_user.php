<?php
session_start();
// Security check: Ensure only an authenticated user can perform this action.
if (!isset($_SESSION['username'])) { 
    header("Location: login.php"); 
    exit(); 
}

// A robust system would also check if $_SESSION['role'] === 'Admin'
if ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Super Admin') {
    header("Location: admin.php?error=unauthorized");
    exit();
}

include 'db_connect.php';

// Check if a user ID is provided in the URL
if (isset($_GET['id'])) {
    $user_to_delete_id = (int)$_GET['id']; // Cast to integer for security

    // To prevent self-deactivation, we need the current user's ID.
    // It's not in the session, so we must query for it.
    $current_username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $current_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_user_id = ($result->num_rows > 0) ? $result->fetch_assoc()['user_id'] : 0;
    $stmt->close();

    if ($user_to_delete_id === $current_user_id) {
        // Redirect back with an error message if a user tries to delete themselves.
        header("Location: admin.php?error=self_delete");
        exit();
    }

    // Prepare and execute the deletion query using prepared statements for security
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $delete_stmt->bind_param("i", $user_to_delete_id);

    if ($delete_stmt->execute()) {
        header("Location: admin.php?status=deactivated");
    } else {
        die("Error deactivating user: " . $delete_stmt->error);
    }
    $delete_stmt->close();
} else {
    header("Location: admin.php");
}
$conn->close();
exit();
?>