<?php
$c = mysqli_connect("127.0.0.1", "root", "");
if (!$c) {
    echo "Direct Connect Failed: " . mysqli_connect_error();
} else {
    echo "Success! The connection is working. The issue is likely the DB name or a typo in db_connect.php.";
}
?>