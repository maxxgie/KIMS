<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture and Sanitize Data
    $inmate_id = (int)$_POST['inmate_id'];
    $workshop = mysqli_real_escape_string($conn, $_POST['workshop_name']);
    $hours = (float)$_POST['hours'];
    $log_date = mysqli_real_escape_string($conn, $_POST['log_date']);
    $instructor = mysqli_real_escape_string($conn, $_POST['instructor_id']);

    // 2. Insert into Database
    // Make sure 'instructor_id' is added to the table via SQL first!
    $sql = "INSERT INTO training_logs (inmate_id, instructor_id, workshop_name, hours_logged, date_logged) 
            VALUES ($inmate_id, '$instructor', '$workshop', $hours, '$log_date')";

    if ($conn->query($sql) === TRUE) {
        // 3. Redirect back to the Kazi profile
        $res = $conn->query("SELECT kims_id FROM inmates WHERE inmate_id = $inmate_id");
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            header("Location: kazi.php?search_id=" . $row['kims_id'] . "&status=logged");
            exit();
        } else {
            header("Location: kazi.php?status=success");
            exit();
        }
    } else {
        // Display clear error for debugging
        echo "<div style='color:red; font-family:sans-serif; padding:20px; border:1px solid red;'>
                <strong>Database Error:</strong> " . $conn->error . "<br>
                <em>Check if instructor_id column exists in training_logs table.</em>
              </div>";
    }
}
$conn->close();
?>