<?php
session_start();
include 'db_connect.php';

// Security Check
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inmate_id = (int)$_POST['inmate_id'];
    $kims_id = mysqli_real_escape_string($conn, $_POST['kims_id']);
    $today = date('Y-m-d');
    
    // Create a formal legal record of the review initiation
    $remarks = "FORMAL PAROLE ELIGIBILITY REVIEW INITIATED. Subject has served >50% of sentence. Authorized by: " . $_SESSION['username'];
    
    $sql = "INSERT INTO court_records (inmate_id, court_name, next_hearing_date, remarks) 
            VALUES ($inmate_id, 'Internal Parole Board', '$today', '$remarks')";

    if ($conn->query($sql) === TRUE) {
        header("Location: alerts.php?status=review_started&id=" . $kims_id);
        exit();
    } else {
        die("Critical System Error: Unable to initiate legal review. " . $conn->error);
    }
}
?>