<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture Form Data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $offence = mysqli_real_escape_string($conn, $_POST['offence']);
    $sentence = mysqli_real_escape_string($conn, $_POST['sentence_years']); // This is the years
    $court = mysqli_real_escape_string($conn, $_POST['court_name']);
    $admission_date = mysqli_real_escape_string($conn, $_POST['admission_date']);

    // Use NULL if block_id is 0 or empty to prevent Foreign Key errors
    $raw_block_id = isset($_POST['block_id']) ? (int)$_POST['block_id'] : 0;
    $block_id = ($raw_block_id > 0) ? $raw_block_id : "NULL";

    // 2. Generate KIMS ID Automatically
    $year = date("Y");
    $random = rand(1000, 9999);
    $kims_id = "KIMS-" . $year . "-" . $random;

    // 3. AUTO-CALCULATE EDD (Earliest Discharge Date)
    // We add the sentence years to the admission date
    $edd = date('Y-m-d', strtotime($admission_date . " + $sentence years"));

    // 4. Handle Mugshot Upload
    $photo_name = "default.png"; 
    if (isset($_FILES['inmate_photo']) && $_FILES['inmate_photo']['error'] == 0) {
        $extension = pathinfo($_FILES['inmate_photo']['name'], PATHINFO_EXTENSION);
        $photo_name = $kims_id . "." . $extension; 
        $target = "uploads/" . $photo_name;
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        move_uploaded_file($_FILES['inmate_photo']['tmp_name'], $target);
    }

    // 5. Insert into Database (NOW INCLUDING sentence_years and edd)
    $sql = "INSERT INTO inmates (
                kims_id, 
                full_name, 
                id_number, 
                dob, 
                gender, 
                offence_category, 
                sentence_years, 
                block_id,
                edd, 
                date_admitted, 
                photo_url, 
                status
            ) VALUES (
                '$kims_id', 
                '$full_name', 
                '$id_number', 
                '$dob', 
                '$gender', 
                '$offence', 
                '$sentence', 
                $block_id,
                '$edd', 
                '$admission_date', 
                '$photo_name', 
                'In Custody'
            )";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php?status=success&new_id=$kims_id");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
}
?>