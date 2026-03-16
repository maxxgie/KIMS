<?php
include 'db_connect.php';

// Logic: Find inmates where EDD is today or has already passed, and they aren't already released
$today = date('Y-m-d');
$sql = "SELECT * FROM inmates WHERE edd <= '$today' AND status = 'In Custody'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Discharge Audit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — Discharge Audit</div>
        <div class="user-info"><a href="dashboard.php">Dashboard</a></div>
    </header>

    <main class="content" style="padding:40px;">
        <h2>Inmates Due for Release (As of <?php echo date('d M Y'); ?>)</h2>
        <hr>

        <table class="report-table">
            <thead>
                <tr>
                    <th>KIMS ID</th>
                    <th>Name</th>
                    <th>Sentence Finished</th>
                    <th>Action</th>
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
                                    <button type="submit" class="btn-primary" style="background: #2e7d32;">PROCESS RELEASE</button>
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