<?php
session_start();
include 'db_connect.php';

// Logic: Find inmates where EDD is today or has already passed, and they aren't already released
$today = date('Y-m-d');
$sql = "SELECT * FROM inmates 
        WHERE edd <= '$today' AND status = 'In Custody'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Discharge Audit</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Ensuring consistency with the system's report table style */
        .report-table { width: 100%; border-collapse: collapse; border: 1px solid #ddd; background: #fff; margin-top: 20px; }
        .report-table th { background: #f8f8f8; text-align: left; padding: 12px; border: 1px solid #ddd; font-size: 11px; color: #555; text-transform: uppercase; }
        .report-table td { padding: 12px; border: 1px solid #ddd; font-size: 13px; vertical-align: middle; }
        
        .auth-input {
            width: 100%; 
            padding: 8px; 
            margin-bottom: 10px; 
            border: 1px solid #ccc; 
            font-size: 12px;
            font-family: inherit;
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">
            Records Officer: <?php echo htmlspecialchars($_SESSION['username'] ?? 'Warden'); ?> | <a href="logout.php">[Logout]</a>
        </div>
    </header>

    <nav class="breadcrumb">Home > Discharge Management > Discharge Audit</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
        <h2>Inmates Due for Release (As of <?php echo date('d M Y'); ?>)</h2>
        <hr>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'discharged'): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; margin-bottom: 20px; border: 1px solid #c8e6c9; font-size: 13px; font-weight: bold;">
                ✔ Inmate has been successfully discharged from the facility.
            </div>
        <?php endif; ?>

        <table class="report-table">
            <thead>
                <tr>
                    <th>KIMS ID</th>
                    <th>Inmate Name</th>
                    <th>Sentence Finished</th>
                    <th style="width: 250px;">Authorization & Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo $row['kims_id']; ?></strong></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td style="color: green; font-weight: bold;"><?php echo $row['edd']; ?></td>
                            <td>
                                <form action="process_discharge.php" method="POST">
                                    <input type="hidden" name="inmate_id" value="<?php echo $row['inmate_id']; ?>">
                                    <label style="display:block; font-size: 10px; font-weight: bold; margin-bottom: 3px;">AUTHORIZED BY:</label>
                                    <input type="text" name="authorized_by" value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>" required style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; font-size: 12px;">
                                    <button type="submit" class="btn-primary" style="background: #2e7d32;" onclick="return confirm('Are you sure you want to release <?php echo htmlspecialchars($row['full_name']); ?> (KIMS ID: <?php echo htmlspecialchars($row['kims_id']); ?>)? This action will mark the inmate as \'Released\' and cannot be easily undone.');">PROCESS RELEASE</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No inmates are due for release today.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>