<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }
include 'db_connect.php';

// Data Fetching
$total_inmates = $conn->query("SELECT COUNT(*) as total FROM inmates")->fetch_assoc()['total'] ?? 0;
$today = date('Y-m-d');
$court_today = $conn->query("SELECT COUNT(*) as total FROM court_records WHERE next_hearing_date = '$today'")->fetch_assoc()['total'] ?? 0;
$capacity_res = $conn->query("SELECT SUM(capacity) as cap, (SELECT COUNT(*) FROM inmates WHERE status = 'In Custody') as occupied FROM cell_blocks")->fetch_assoc();
$occupancy_rate = ($capacity_res['cap'] > 0) ? round(($capacity_res['occupied'] / $capacity_res['cap']) * 100) : 0;
$kazi_count = $conn->query("SELECT COUNT(DISTINCT inmate_id) as total FROM training_logs")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Command Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Neutral Wireframe Theme */
        body { background-color: #f4f4f4; color: #333; font-family: 'Segoe UI', Arial, sans-serif; }
        .content { padding: 20px; background: #fff; min-height: 90vh; border-left: 1px solid #ccc; }
        
        /* Stats Grid - Neutral Grayscale */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
        .stat-card { 
            background: #fff; 
            border: 1px solid #ccc; 
            padding: 15px; 
            text-align: left; 
        }
        .stat-card h3 { 
            font-size: 13px; 
            color: #555; 
            text-transform: uppercase; 
            margin: 0; 
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }
        .stat-number { font-size: 28px; font-weight: bold; margin: 10px 0 5px 0; color: #000; }
        .stat-card span { font-size: 11px; color: #888; }

        /* Section Styling */
        .section-title { 
            background: #eee; 
            padding: 8px 15px; 
            font-weight: bold; 
            border-left: 4px solid #333; 
            margin: 30px 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
        }

        /* Table Design */
        .report-table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #ccc; }
        .report-table th { 
            background: #f9f9f9; 
            text-align: left; 
            padding: 10px; 
            border: 1px solid #ccc; 
            font-size: 12px; 
            text-transform: uppercase;
        }
        .report-table td { 
            padding: 10px; 
            border: 1px solid #eee; 
            font-size: 13px; 
        }
        .view-link { color: #0066cc; text-decoration: none; font-weight: bold; }
        .view-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">
            Records Officer: <?php echo $_SESSION['username']; ?> | <a href="logout.php">[Logout]</a>
        </div>
    </header>

    <nav class="breadcrumb">Home > Dashboard</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <h2 style="margin-top:0;">Command Dashboard</h2>
            <hr>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Population</h3>
                    <p class="stat-number"><?php echo $total_inmates; ?></p>
                    <span>Registered Inmates</span>
                </div>
                <div class="stat-card">
                    <h3>Court Today</h3>
                    <p class="stat-number"><?php echo $court_today; ?></p>
                    <span>Escort Required</span>
                </div>
                <div class="stat-card">
                    <h3>Capacity Use</h3>
                    <p class="stat-number"><?php echo $occupancy_rate; ?>%</p>
                    <span>Overall Occupancy</span>
                </div>
                <div class="stat-card">
                    <h3>Vocational</h3>
                    <p class="stat-number"><?php echo $kazi_count; ?></p>
                    <span>Active Trainees</span>
                </div>
            </div>

            <div class="section-title">Recent Admissions Overview</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>KIMS-ID</th>
                        <th>Name</th>
                        <th>Offence</th>
                        <th>Date Admitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recent_res = $conn->query("SELECT kims_id, full_name, offence_category, date_admitted FROM inmates ORDER BY date_admitted DESC LIMIT 5");
                    if ($recent_res && $recent_res->num_rows > 0) {
                        while($row = $recent_res->fetch_assoc()) {
                            echo "<tr>
                                    <td><strong>{$row['kims_id']}</strong></td>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['offence_category']}</td>
                                    <td>" . date('d-m-Y', strtotime($row['date_admitted'])) . "</td>
                                    <td><a href='search.php?kims_id={$row['kims_id']}' class='view-link'>[ View Profile ]</a></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>No recent records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="section-title">Latest Global Vocational Logs</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Trainee Name</th>
                        <th>Workshop Unit</th>
                        <th>Hours</th>
                        <th>Supervisor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // JOIN used to pull trainee names from the inmates table
                    $vocal_sql = "SELECT t.*, i.full_name, i.kims_id 
                                 FROM training_logs t 
                                 JOIN inmates i ON t.inmate_id = i.inmate_id 
                                 ORDER BY t.date_logged DESC LIMIT 10";
                    $vocal_res = $conn->query($vocal_sql);

                    if ($vocal_res && $vocal_res->num_rows > 0) {
                        while($vlog = $vocal_res->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . date('d-m-Y', strtotime($vlog['date_logged'])) . "</td>
                                    <td><strong>{$vlog['full_name']}</strong> ({$vlog['kims_id']})</td>
                                    <td>{$vlog['workshop_name']}</td>
                                    <td><strong>{$vlog['hours_logged']} hrs</strong></td>
                                    <td style='color: #666;'>{$vlog['instructor_id']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>No vocational activity recorded.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>