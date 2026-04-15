<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $inmate_id = (int)$_POST['inmate_id'];
    $kims_id = mysqli_real_escape_string($conn, $_POST['kims_id']);

    // 1. Fetch current sentence and admission date
    $query = "SELECT date_admitted, sentence_years FROM inmates WHERE inmate_id = $inmate_id";
    $result = $conn->query($query);

    if ($result && $row = $result->fetch_assoc()) {
        $admission_date = $row['date_admitted'];
        $old_sentence = (float)$row['sentence_years'];

        // 2. Apply 1/3 Remission (Inmate serves 2/3 of the sentence)
        $new_sentence = $old_sentence * (2 / 3);

        // 3. Recalculate EDD
        // We use days for better precision when dealing with fractions of years
        $days_to_serve = round($new_sentence * 365.25);
        $new_edd = date('Y-m-d', strtotime($admission_date . " + $days_to_serve days"));

        // 4. Update the record
        $update_sql = "UPDATE inmates SET sentence_years = $new_sentence, edd = '$new_edd' WHERE inmate_id = $inmate_id";

        if ($conn->query($update_sql) === TRUE) {
            header("Location: judicial.php?search_id=$kims_id&status=success");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
} else {
    header("Location: login.php");
}
?>