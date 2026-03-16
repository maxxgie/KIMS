<?php
include 'db_connect.php';

// SQL to fetch all inmates
$sql = "SELECT kims_id, full_name, offence_category, date_admitted, edd FROM inmates ORDER BY date_admitted DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Inmate List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Internal styles for quick viewing */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        .data-table th, .data-table td { padding: 12px; border: 1px solid #ddd; text-align: left; font-size: 13px; }
        .data-table th { background-color: #333; color: white; }
        .status-tag { background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 3px; font-weight: bold; }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — Database View</div>
        <div class="user-info"><a href="dashboard.html">Return to Dashboard</a></div>
    </header>

    <main class="content" style="padding: 40px;">
        <h2>Registered Inmates</h2>
        <p>This table shows "Live" data pulled directly from your <strong>kims_db</strong>.</p>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>KIMS ID</th>
                    <th>Full Name</th>
                    <th>Offence</th>
                    <th>Admission Date</th>
                    <th>EDD (Discharge)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><strong>" . $row["kims_id"] . "</strong></td>";
                        echo "<td>" . $row["full_name"] . "</td>";
                        echo "<td>" . $row["offence_category"] . "</td>";
                        echo "<td>" . $row["date_admitted"] . "</td>";
                        echo "<td>" . $row["edd"] . "</td>";
                        echo "<td><span class='status-tag'>Custody</span></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No records found in database.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>

<?php $conn->close(); ?>