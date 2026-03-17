<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inmate_id = mysqli_real_escape_string($conn, $_POST['inmate_id']);
    $new_block_id = mysqli_real_escape_string($conn, $_POST['block_id']);
    $kims_id = mysqli_real_escape_string($conn, $_POST['kims_id']);

    // 1. Capacity Check: Get current occupancy vs capacity
    $capacity_sql = "SELECT capacity, 
                    (SELECT COUNT(*) FROM inmates WHERE block_id = '$new_block_id') as current_occupancy 
                    FROM cell_blocks WHERE block_id = '$new_block_id'";
    
    $res = $conn->query($capacity_sql);
    $block_data = $res->fetch_assoc();

    if ($block_data['current_occupancy'] >= $block_data['capacity']) {
        echo "<script>alert('Transfer Denied: Destination block is at full capacity.'); window.history.back();</script>";
        exit();
    }

    // 2. Perform the Update
    $update_sql = "UPDATE inmates SET block_id = '$new_block_id' WHERE inmate_id = '$inmate_id'";

    if ($conn->query($update_sql) === TRUE) {
        // Optional: Log the transfer for audit purposes
        // $log_sql = "INSERT INTO transfer_logs (inmate_id, block_id, transfer_date) VALUES ('$inmate_id', '$new_block_id', NOW())";
        // $conn->query($log_sql);

        header("Location: housing.php?search_id=" . $kims_id . "&status=updated");
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>