<?php
$servername = "127.0.0.1";
$username = "root";
$password = ""; 
$dbname = "kims";
$port = 3307; // This matches your XAMPP Control Panel exactly

// Create connection with the specific port
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>