<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize basic inputs
    $inmate_id = intval($_POST['inmate_id']);
    $kims_id = mysqli_real_escape_string($conn, $_POST['kims_id']);
    $new_offence = mysqli_real_escape_string($conn, $_POST['new_offence']);
    $user = $_SESSION['username'];

    // 1. Fetch the current offence using a Prepared Statement for security
    $stmt = $conn->prepare("SELECT offence_category FROM inmates WHERE inmate_id = ?");
    $stmt->bind_param("i", $inmate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_data = $result->fetch_assoc();
    
    if ($current_data) {
        $old_offence = $current_data['offence_category'];

        // 2. Only proceed if the offence has actually changed
        if ($old_offence !== $new_offence) {
            
            // Start a transaction to ensure both queries succeed together
            $conn->begin_transaction();

            try {
                // A. Record the transition in the audit table
                $log_stmt = $conn->prepare("INSERT INTO offence_updates (inmate_id, old_offence, new_offence, updated_by) VALUES (?, ?, ?, ?)");
                $log_stmt->bind_param("isss", $inmate_id, $old_offence, $new_offence, $user);
                $log_stmt->execute();

                // B. Update the primary inmate record
                $update_stmt = $conn->prepare("UPDATE inmates SET offence_category = ? WHERE inmate_id = ?");
                $update_stmt->bind_param("si", $new_offence, $inmate_id);
                $update_stmt->execute();

                // Commit changes
                $conn->commit();
                
                // Redirect back to judicial page with success
                header("Location: judicial.php?search_id=" . $kims_id . "&status=success");
                exit();

            } catch (Exception $e) {
                // If anything fails, rollback the database changes
                $conn->rollback();
                echo "Error updating legal records: " . $e->getMessage();
            }
        } else {
            // No change detected, just redirect back
            header("Location: judicial.php?search_id=" . $kims_id);
            exit();
        }
    } else {
        echo "Inmate record not found.";
    }
} else {
    header("Location: judicial.php");
    exit();
}
?>