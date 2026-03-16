<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['staff_name']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (full_name, username, role) VALUES ('$name', '$user', '$role')";

    if ($conn->query($sql)) {
        echo "<script>alert('Staff account created!'); window.location.href='admin.php';</script>";
    }
}
?>