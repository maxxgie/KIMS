<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the inmate ID from the audit form
    $inmate_id = (int)$_POST['inmate_id'];
    $authorized_by = $_POST['authorized_by'];

    // Update the status to 'Released'
    $stmt = $conn->prepare("UPDATE inmates SET status = 'Released', discharged_by = ? WHERE inmate_id = ?");
    $stmt->bind_param("si", $authorized_by, $inmate_id);

    if ($stmt->execute()) {
        // Redirect back with a success flag
        header("Location: discharge_audit.php?status=discharged");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>