<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inmate_id = (int)$_POST['inmate_id'];
    $court_name = mysqli_real_escape_string($conn, $_POST['court_name']);
    $next_date = $_POST['next_date'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    $sql = "INSERT INTO court_records (inmate_id, court_name, next_hearing_date, remarks) 
            VALUES ($inmate_id, '$court_name', '$next_date', '$remarks')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Court Outcome Saved Successfully!'); window.location.href='judicial.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>