<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inmate_id = (int)$_POST['inmate_id'];
    $block_id = (int)$_POST['block_id'];

    $sql = "UPDATE inmates SET block_id = $block_id WHERE inmate_id = $inmate_id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Housing updated successfully!'); window.location.href='housing.php';</script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
$conn->close();
?>