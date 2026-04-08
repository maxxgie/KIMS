<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inmate_id = (int)$_POST['inmate_id'];
    $new_sentence = (int)$_POST['sentence_years'];
    $kims_id = mysqli_real_escape_string($conn, $_POST['kims_id']);

    // 1. Fetch admission date to recalculate EDD accurately
    $query = "SELECT date_admitted FROM inmates WHERE inmate_id = $inmate_id";
    $result = $conn->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        $admission_date = $row['date_admitted'];
        
        // 2. Recalculate EDD based on the new sentence length
        $new_edd = date('Y-m-d', strtotime($admission_date . " + $new_sentence years"));

        // 3. Update the database
        $update_sql = "UPDATE inmates SET sentence_years = $new_sentence, edd = '$new_edd' WHERE inmate_id = $inmate_id";

        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('Sentence Updated Successfully!'); window.location.href='judicial.php?search_id=$kims_id';</script>";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}
$conn->close();
?>