<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $admission_date = $_POST['admission_date'];
    $offence = mysqli_real_escape_string($conn, $_POST['offence']);
    $sentence = (int)$_POST['sentence_years'];

    // Generate a unique KIMS-ID (e.g., KIMS-2026-XXXX)
    $year = date("Y");
    $random_num = rand(1000, 9999);
    $kims_id = "KIMS-" . $year . "-" . $random_num;

    // Calculate EDD (Earliest Date of Discharge) - Simple logic adding years
    $edd = date('Y-m-d', strtotime($admission_date . " + $sentence years"));

    $sql = "INSERT INTO inmates (kims_id, full_name, id_number, dob, gender, date_admitted, offence_category, sentence_years, edd)
            VALUES ('$kims_id', '$full_name', '$id_number', '$dob', '$gender', '$admission_date', '$offence', $sentence, '$edd')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Inmate Registered Successfully! ID: $kims_id'); window.location.href='dashboard.html';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>