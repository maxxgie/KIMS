<?php
session_start();
// Ensure only authenticated users can access
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }

include 'db_connect.php';

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = (int)$_GET['id']; // Cast to integer for security

    if ($type === 'inmate') {
        // 1. Delete associated Court Records
        $conn->query("DELETE FROM court_records WHERE inmate_id = $id");
        // 2. Delete associated Training Logs
        $conn->query("DELETE FROM training_logs WHERE inmate_id = $id");
        // 3. Delete the Inmate
        $sql = "DELETE FROM inmates WHERE inmate_id = $id";
        $redirect = "dashboard.php?status=deleted";

    } elseif ($type === 'court') {
        // Delete specific court record. Assumes Primary Key is 'id'.
        $sql = "DELETE FROM court_records WHERE id = $id"; 
        $redirect = "reports.php?type=court&status=deleted";

    } elseif ($type === 'log') {
        // Delete specific training log. Assumes Primary Key is 'id'.
        $sql = "DELETE FROM training_logs WHERE id = $id";
        $redirect = "dashboard.php?status=deleted"; // A safe default
    }

    // Execute Deletion
    if (isset($sql) && $conn->query($sql) === TRUE) {
        header("Location: " . $redirect);
        exit();
    } else {
        die("Critical Error: Unable to purge record. " . $conn->error);
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>