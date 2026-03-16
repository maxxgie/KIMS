<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inmate_id = (int)$_POST['inmate_id'];
    $court_name = mysqli_real_escape_string($conn, $_POST['court_name']);
    $hearing_date = $_POST['hearing_date'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);

    $sql = "INSERT INTO court_records (inmate_id, court_name, next_hearing_date, remarks) 
            VALUES ($inmate_id, '$court_name', '$hearing_date', '$remarks')";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to judicial page with the inmate still selected
        // We fetch the KIMS ID again to refresh the search
        $res = $conn->query("SELECT kims_id FROM inmates WHERE inmate_id = $inmate_id");
        $row = $res->fetch_assoc();
        header("Location: judicial.php?search_id=" . $row['kims_id']);
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>