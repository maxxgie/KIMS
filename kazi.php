<?php
include 'db_connect.php';

$inmate = null;
$search_query = "";
$total_hours = 0;

if (isset($_GET['search_id'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search_id']);
    $sql = "SELECT i.*, 
            (SELECT SUM(hours_logged) FROM training_logs WHERE inmate_id = i.inmate_id) as total_hrs 
            FROM inmates i WHERE i.kims_id = '$search_query'";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $inmate = $result->fetch_assoc();
        $total_hours = $inmate['total_hrs'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Kazi na Masomo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific tweaks to match Screenshot 3's "Dashboard" style within Kazi */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px; }
        .stat-card { background: white; border: 1px solid #ddd; border-top: 4px solid #333; padding: 15px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .stat-card.blue { border-top-color: #1a73e8; }
        .stat-card.green { border-top-color: #2e7d32; }
        
        .section-header-bar { 
            background: #eee; 
            padding: 10px 15px; 
            font-weight: bold; 
            border-left: 5px solid #333; 
            margin: 30px 0 10px 0; 
            font-size: 13px; 
            text-transform: uppercase; 
        }

        .report-table { width: 100%; border-collapse: collapse; border: 1px solid #ddd; margin-top: 10px; }
        .report-table th { background: #f8f8f8; text-align: left; padding: 12px; border: 1px solid #ddd; font-size: 12px; color: #555; }
        .report-table td { padding: 12px; border: 1px solid #ddd; font-size: 13px; }
        
        .placeholder-box { margin-top: 100px; text-align: center; border: 2px dashed #ccc; padding: 80px; color: #888; border-radius: 8px; }
    </style>
</head>
<body>

    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">Records Officer: <?php echo "maxwellnjane@gmail.com"; ?> | <a href="logout.php">[Logout]</a></div>
    </header>

    <nav class="breadcrumb">Home > Kazi na Masomo</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <div class="header-actions">
                <h2 style="font-size: 24px; color: #333;">Vocational Activity Ledger</h2>
                <form action="kazi.php" method="GET" class="search-box">
                    <input type="text" name="search_id" placeholder="Enter Inmate KIMS-ID..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="btn-primary">LOAD</button>
                </form>
            </div>

            <?php if ($inmate): ?>
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <label style="color:#888; font-size:10px;">TRAINEE NAME</label>
                        <div style="font-size: 16px; font-weight: bold;"><?php echo $inmate['full_name']; ?></div>
                    </div>
                    <div class="stat-card">
                        <label style="color:#888; font-size:10px;">KIMS-ID</label>
                        <div style="font-size: 16px; font-weight: bold;"><?php echo $inmate['kims_id']; ?></div>
                    </div>
                    <div class="stat-card">
                        <label style="color:#888; font-size:10px;">TOTAL HOURS</label>
                        <div style="font-size: 22px; font-weight: bold;"><?php echo number_format($total_hours, 2); ?></div>
                    </div>
                    <div class="stat-card green">
                        <label style="color:#888; font-size:10px;">VOCATIONAL STATUS</label>
                        <div style="font-size: 13px; font-weight: bold; color: #2e7d32;">
                            <?php echo ($total_hours >= 100) ? '✓ ELIGIBLE FOR GRADUATION' : 'IN TRAINING'; ?>
                        </div>
                    </div>
                </div>

                <div class="section-header-bar">Post New Workshop Activity</div>
                <div class="judicial-form">
                    <form action="save_kazi_log.php" method="POST" class="form-grid">
                        <input type="hidden" name="inmate_id" value="<?php echo $inmate['inmate_id']; ?>">
                        
                        <div class="form-group">
                            <label>WORKSHOP UNIT</label>
                            <select name="workshop_name">
                                <option>Bakery</option>
                                <option>Carpentry & Joinery</option>
                                <option>Tailoring & Dressmaking</option>
                                <option>Masonry & Building</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>HOURS LOGGED</label>
                            <input type="number" step="0.5" name="hours" placeholder="e.g. 6.0" required>
                        </div>
                        <div class="form-group">
                            <label>ACTIVITY DATE</label>
                            <input type="date" name="log_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>INSTRUCTOR ID (SERVICE NO.)</label>
                            <input type="text" name="instructor_id" placeholder="e.g. 124567" required>
                        </div>
                        
                        <div class="full-width">
                            <button type="submit" class="btn-primary" style="width: 100%;">Update Vocational Ledger</button>
                        </div>
                    </form>
                </div>

                <div class="section-header-bar">Latest Individual Vocational Logs</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>WORKSHOP UNIT</th>
                            <th>HOURS</th>
                            <th>SUPERVISOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $id = $inmate['inmate_id'];
                        $logs = $conn->query("SELECT * FROM training_logs WHERE inmate_id = $id ORDER BY date_logged DESC LIMIT 10");
                        while($row = $logs->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row['date_logged'])); ?></td>
                                <td><?php echo $row['workshop_name']; ?></td>
                                <td><strong><?php echo number_format($row['hours_logged'], 2); ?> hrs</strong></td>
                                <td><?php echo $row['instructor_id']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <div class="placeholder-box">
                    <p>Enter a valid <strong>KIMS-ID</strong> to retrieve trainee records and post new vocational hours.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

</body>
</html>