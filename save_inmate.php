<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Capture Form Data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $id_number = mysqli_real_escape_string($conn, $_POST['id_number']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $offence = mysqli_real_escape_string($conn, $_POST['offence']);
    $court = mysqli_real_escape_string($conn, $_POST['court_name']);
    $admission_date = mysqli_real_escape_string($conn, $_POST['admission_date']);

    // --- NEW VALIDATION: 9-Digit ID Restriction ---
    // Remove any accidental spaces
    $id_number = trim($id_number);
    
    if (!ctype_digit($id_number)) {
        die("Error: National ID must contain only numbers.");
    }
    if (strlen($id_number) > 9) {
        die("Error: National ID cannot exceed 9 digits. You entered " . strlen($id_number) . " digits.");
    }
    // ----------------------------------------------

    // Capture Duration and Unit
    $sentence_value = (float)$_POST['sentence_value'];
    $sentence_unit = $_POST['sentence_unit'];

    // Convert to years for the 'sentence_years' column and prepare date math
    if ($sentence_unit == 'months') {
        $sentence_years = $sentence_value / 12;
        $time_diff = "+ " . (int)$sentence_value . " months";
    } elseif ($sentence_unit == 'days') {
        $sentence_years = $sentence_value / 365;
        $time_diff = "+ " . (int)$sentence_value . " days";
    } else {
        $sentence_years = $sentence_value;
        $time_diff = "+ " . (int)$sentence_value . " years";
    }

    // Use NULL if block_id is 0 or empty to prevent Foreign Key errors
    $raw_block_id = isset($_POST['block_id']) ? (int)$_POST['block_id'] : 0;
    $block_id = ($raw_block_id > 0) ? $raw_block_id : "NULL";

    // 2. Generate Unique KIMS ID Automatically
    $year = date("Y");
    $is_unique = false;
    $kims_id = "";

    while (!$is_unique) {
        $random = rand(1000, 9999);
        $kims_id = "KIMS-" . $year . "-" . $random;
        
        // Verify uniqueness in the database
        $check_stmt = $conn->prepare("SELECT kims_id FROM inmates WHERE kims_id = ?");
        $check_stmt->bind_param("s", $kims_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows === 0) {
            $is_unique = true;
        }
    }

    // 3. AUTO-CALCULATE EDD (Expected Date of Discharge)
    $edd = date('Y-m-d', strtotime($admission_date . " " . $time_diff));

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

    // 5. Insert into Database
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
                '$sentence_years', 
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