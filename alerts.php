<?php
session_start();
if (!isset($_SESSION['username'])) { header("Location: login.php"); exit(); }
include 'db_connect.php';

$today = date('Y-m-d');

// Alerts Logic: Court (Next 48 Hours)
$court_alert_date = date('Y-m-d', strtotime('+2 days'));
$court_alerts = $conn->query("SELECT c.*, i.full_name, i.kims_id FROM court_records c JOIN inmates i ON c.inmate_id = i.inmate_id WHERE c.next_hearing_date BETWEEN '$today' AND '$court_alert_date' ORDER BY c.next_hearing_date ASC");

// Alerts Logic: Releases (Next 30 Days)
$release_alert_date = date('Y-m-d', strtotime('+30 days'));
$release_alerts = $conn->query("SELECT inmate_id, full_name, kims_id, edd FROM inmates WHERE edd BETWEEN '$today' AND '$release_alert_date' ORDER BY edd ASC");

// Alerts Logic: Parole Eligibility
$parole_alerts_query = "SELECT inmate_id, full_name, kims_id, date_admitted, sentence_years, edd
                        FROM inmates 
                        WHERE status = 'In Custody' 
                        AND DATEDIFF('$today', date_admitted) >= (DATEDIFF(edd, date_admitted) / 2)";
$parole_alerts = $conn->query($parole_alerts_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — System Alerts</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific Alert Styling */
        .alert-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-urgent { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .badge-info { background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
        
        .report-table { width: 100%; border-collapse: collapse; border: 1px solid #ddd; background: #fff; }
        .report-table th { background: #f8f8f8; text-align: left; padding: 12px; border: 1px solid #ddd; font-size: 11px; color: #555; }
        .report-table td { padding: 12px; border: 1px solid #ddd; font-size: 13px; }
        
        .section-header-bar { 
            background: #eee; padding: 10px 15px; font-weight: bold; 
            border-left: 5px solid #333; margin: 30px 0 10px 0; 
            font-size: 13px; text-transform: uppercase; 
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">
            Records Officer: <?php echo $_SESSION['username']; ?> | <a href="logout.php">[Logout]</a>
        </div>
    </header>

    <nav class="breadcrumb">Home > System Notifications > Critical Alerts</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <div class="header-actions">
                <h2 style="margin:0;">System Notifications & Alerts</h2>
                <div style="font-size: 12px; color: #666;">Server Date: <strong><?php echo date('d M Y'); ?></strong></div>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'review_started'): ?>
                <div style="background: #fff3e0; color: #e65100; padding: 15px; margin-top: 20px; border: 1px solid #ffe0b2; font-size: 13px; font-weight: bold;">
                    ➔ Parole Review Process has been formally initiated for KIMS-ID: <?php echo htmlspecialchars($_GET['id']); ?>. A record has been added to the Judicial Transcript.
                </div>
            <?php endif; ?>

            <div class="section-header-bar" style="border-left-color: #d32f2f;">
                <span style="color: #d32f2f;">⚠</span> Judicial Alerts (Next 48 Hours)
            </div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>HEARING DATE</th>
                        <th>INMATE IDENTIFICATION</th>
                        <th>COURT LOCATION</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($court_alerts && $court_alerts->num_rows > 0): ?>
                        <?php while($ca = $court_alerts->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo date('d-m-Y', strtotime($ca['next_hearing_date'])); ?></strong></td>
                                <td><?php echo strtoupper($ca['full_name']); ?><br><small style="color:#888;"><?php echo $ca['kims_id']; ?></small></td>
                                <td><?php echo $ca['court_name']; ?></td>
                                <td><span class="alert-badge badge-urgent">Immediate Escort</span></td>
                                <td><a href="judicial.php?search_id=<?php echo $ca['kims_id']; ?>" class="btn-action btn-view">LOGISTICS</a></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 30px; color: #999;">No upcoming court dates in the immediate 48-hour window.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="section-header-bar" style="border-left-color: #2e7d32;">
                Scheduled Releases (Next 30 Days)
            </div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>EXPECTED DISCHARGE</th>
                        <th>INMATE NAME</th>
                        <th>KIMS ID</th>
                        <th>CLEARANCE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($release_alerts && $release_alerts->num_rows > 0): ?>
                        <?php while($ra = $release_alerts->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo date('d-m-Y', strtotime($ra['edd'])); ?></strong></td>
                                <td><?php echo strtoupper($ra['full_name']); ?></td>
                                <td><?php echo $ra['kims_id']; ?></td>
                                <td><span class="alert-badge badge-info">Pending Final Review</span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 30px; color: #999;">No releases scheduled for the current month.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="section-header-bar" style="border-left-color: #f57c00;">
                Parole Eligibility Review (Time Served > 50%)
            </div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>TRAINEE / INMATE NAME</th>
                        <th>KIMS ID</th>
                        <th>SENTENCE RATIO</th>
                        <th>LOGS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($parole_alerts && $parole_alerts->num_rows > 0): ?>
                        <?php while($pa = $parole_alerts->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo strtoupper($pa['full_name']); ?></td>
                                <td><?php echo $pa['kims_id']; ?></td>
                                <td><strong style="color: #f57c00;">ELIGIBLE</strong></td>
                                <td><a href="kazi.php?search_id=<?php echo $pa['kims_id']; ?>" class="btn-action" style="text-decoration:none;">VIEW LOGS</a></td>
                                <td>
                                    <form action="initiate_parole_review.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="inmate_id" value="<?php echo $pa['inmate_id']; ?>">
                                        <input type="hidden" name="kims_id" value="<?php echo $pa['kims_id']; ?>">
                                        <button type="submit" class="btn-action btn-view" style="cursor:pointer; border:none;" onclick="return confirm('OFFICIAL ACTION: Are you sure you want to formally initiate a Parole Board Review for <?php echo htmlspecialchars($pa['full_name']); ?>?');">INITIATE REVIEW</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 30px; color: #999;">No inmates currently meet the 50% sentence threshold for review.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
        </main>
    </div>
</body>
</html>