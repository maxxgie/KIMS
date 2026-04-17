<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inmate_id = (int)$_POST['inmate_id'];
    $kims_id = mysqli_real_escape_string($conn, $_POST['kims_id']);

    // Capture Duration and Unit
    $sentence_value = (float)$_POST['sentence_value'];
    $sentence_unit = $_POST['sentence_unit'];

    // 1. Fetch admission date to recalculate EDD accurately
    $query = "SELECT date_admitted, sentence_years FROM inmates WHERE inmate_id = $inmate_id";
    $result = $conn->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        $admission_date = $row['date_admitted'];
        $old_years = (float)$row['sentence_years'];
        
        // Convert to years for storage and prepare date math
        if ($sentence_unit == 'months') {
            $sentence_years = $sentence_value / 12;
            $time_diff = "+ " . (int)$sentence_value . " months";
        } elseif ($sentence_unit == 'days') {
            $sentence_years = $sentence_value / 365;
            $time_diff = "+ " . (int)$sentence_value . " days";
        } else {
            $sentence_years = $sentence_value;
            $time_diff = "+ " . (int)$sentence_value . " years";
        }

        // 2. Recalculate EDD based on the new sentence length
        $new_edd = date('Y-m-d', strtotime($admission_date . " " . $time_diff));

        // 3. Update the database and log the revision
        $conn->begin_transaction();
        try {
            $update_sql = "UPDATE inmates SET sentence_years = $sentence_years, edd = '$new_edd' WHERE inmate_id = $inmate_id";
            $conn->query($update_sql);

            $user = $_SESSION['username'] ?? 'Admin';
            $log_sql = "INSERT INTO sentence_updates (inmate_id, old_years, new_years, updated_by) VALUES ($inmate_id, $old_years, $sentence_years, '$user')";
            $conn->query($log_sql);

            $conn->commit();
            echo "<script>alert('Sentence Updated Successfully!'); window.location.href='judicial.php?search_id=$kims_id';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error updating record: " . $e->getMessage();
        }
    }
}
$conn->close();
?>