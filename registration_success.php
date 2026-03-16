<?php
include 'db_connect.php';
$id = $_GET['id'];
$sql = "SELECT * FROM inmates WHERE kims_id = '$id'";
$res = $conn->query($sql);
$inmate = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Successful</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .gate-pass { background: white; border: 2px dashed #333; padding: 30px; width: 600px; margin: 50px auto; }
        .pass-header { text-align: center; border-bottom: 2px solid #333; margin-bottom: 20px; }
        .pass-body { display: flex; gap: 20px; }
        @media print { .no-print { display: none; } .gate-pass { border: 2px solid #000; } }
    </style>
</head>
<body style="background: #f4f4f4;">
    <div class="gate-pass">
        <div class="pass-header">
            <h2>KING'ONG'O MAXIMUM PRISON</h2>
            <h3>ADMISSION GATE PASS</h3>
        </div>
        <div class="pass-body">
            <img src="uploads/<?php echo $inmate['photo_url']; ?>" width="150" height="150" style="border:1px solid #000;">
            <div>
                <p><strong>KIMS ID:</strong> <?php echo $inmate['kims_id']; ?></p>
                <p><strong>Name:</strong> <?php echo $inmate['full_name']; ?></p>
                <p><strong>Offence:</strong> <?php echo $inmate['offence_category']; ?></p>
                <p><strong>Admitted:</strong> <?php echo $inmate['date_admitted']; ?></p>
                <p><strong>Expected Discharge:</strong> <?php echo $inmate['edd']; ?></p>
            </div>
        </div>
        <div style="margin-top:30px; font-style: italic; font-size: 12px;">
            Authorized by: Warden Mwangi ____________________
        </div>
    </div>
    <div class="no-print" style="text-align:center;">
        <button onclick="window.print()" class="btn-primary">Print Pass</button>
        <button onclick="location.href='dashboard.php'" class="btn-secondary">Back to Dashboard</button>
    </div>
</body>
</html>