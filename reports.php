<?php
include 'db_connect.php';

// Get the report type from URL, default to 'court'
$type = isset($_GET['type']) ? $_GET['type'] : 'court';
$reportTitle = "";
$resultData = null;

// Determine SQL Query and Title based on selection
switch ($type) {
    case 'revisions':
        $reportTitle = "Legal Charge Revision Audit";
        // Logic: Joins the new tracking table with inmates to show the 'Before and After'
        $sql = "SELECT i.kims_id, i.full_name, u.old_offence, u.new_offence, u.update_date, u.updated_by 
                FROM offence_updates u 
                INNER JOIN inmates i ON u.inmate_id = i.inmate_id 
                ORDER BY u.update_date DESC";
        break;

    case 'sentence_revisions':
        $reportTitle = "Sentence Adjustment Audit";
        // Logic: Track manual changes to inmate sentence lengths
        $sql = "SELECT i.kims_id, i.full_name, s.old_years, s.new_years, s.update_date, s.updated_by 
                FROM sentence_updates s 
                INNER JOIN inmates i ON s.inmate_id = i.inmate_id 
                ORDER BY s.update_date DESC";
        break;

    case 'crime':
        $reportTitle = "Crime Distribution & Trends";
        $sql = "SELECT offence_category, COUNT(*) as total_inmates, MAX(date_admitted) as last_update 
                FROM inmates 
                WHERE offence_category IS NOT NULL AND offence_category != ''
                GROUP BY offence_category 
                ORDER BY total_inmates DESC";
        break;

    case 'kazi':
        $reportTitle = "Vocational Enrollment Summary";
        $sql = "SELECT i.kims_id, i.full_name, t.workshop_name, SUM(t.hours_logged) as total_hours 
                FROM inmates i 
                INNER JOIN training_logs t ON i.inmate_id = t.inmate_id 
                GROUP BY i.inmate_id, t.workshop_name";
        break;
    
    case 'housing':
        $reportTitle = "Occupancy Audit Report";
        $sql = "SELECT 'Block A' as section, 'General' as class, 150 as cap, 142 as pop, 8 as avail 
                UNION SELECT 'Block B', 'General', 150, 148, 2
                UNION SELECT 'Block C', 'Maximum', 50, 50, 0
                UNION SELECT 'Block D', 'Special', 30, 12, 18";
        break;

    case 'court':
    default:
        $type = 'court'; 
        $reportTitle = "Upcoming Court Schedule";
        $sql = "SELECT i.kims_id, i.full_name, c.court_name, c.next_hearing_date as hearing_date, c.remarks, c.zoom_link 
                FROM inmates i 
                INNER JOIN court_records c ON i.inmate_id = c.inmate_id 
                ORDER BY c.next_hearing_date ASC";
        break;

    case 'releases':
        $reportTitle = "Recent Inmate Releases (Last 3 Months)";
        $sql = "SELECT kims_id, full_name, edd, offence_category, discharged_by
                FROM inmates
                WHERE status = 'Released' AND edd >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
                ORDER BY edd DESC";
        break;
}

$resultData = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIMS — Reports & Analytics</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="reports.css">
    <style>
        .old-charge { color: #d32f2f; text-decoration: line-through; font-size: 11px; }
        .new-charge { color: #2e7d32; font-weight: bold; }
        .update-arrow { padding: 0 10px; color: #666; }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">Records Officer: Warden Mwangi | <a href="login.php">[Logout]</a></div>
    </header>

    <nav class="breadcrumb">Home > Reports & Analytics</nav>

    <div class="main-container">
       <?php include 'sidebar.php'; ?>

        <main class="content">
            <h2>System Reports</h2>
            <hr>

            <div class="report-selection">
                <div class="report-card <?php echo ($type == 'revisions') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=revisions'">
                    <h4>Charge Revisions</h4>
                    <p>Track updates from original to amended crimes.</p>
                </div>
                <div class="report-card <?php echo ($type == 'sentence_revisions') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=sentence_revisions'">
                    <h4>Sentence Revisions</h4>
                    <p>Audit trail of manual sentence adjustments.</p>
                </div>
                <div class="report-card <?php echo ($type == 'crime') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=crime'">
                    <h4>Crime Profiles</h4>
                    <p>Standardized offense stats and entry dates.</p>
                </div>
                <div class="report-card <?php echo ($type == 'kazi') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=kazi'">
                    <h4>Vocational Enrollment</h4>
                    <p>Training hours and eligibility.</p>
                </div>
                <div class="report-card <?php echo ($type == 'court') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=court'">
                    <h4>Court Schedule</h4>
                    <p>Upcoming hearings and logistics.</p>
                </div>
                <div class="report-card <?php echo ($type == 'releases') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=releases'">
                    <h4>Recent Releases</h4>
                    <p>Inmates discharged in the last 3 months.</p>
                </div>
            </div>

            <div class="report-preview">
                <div class="preview-header">
                    <h3 id="reportTitle">PREVIEW: <?php echo $reportTitle; ?></h3>
                    <div class="preview-actions">
                        <button class="btn-secondary" onclick="window.print()">[ PRINT PDF ]</button>
                    </div>
                </div>

                <table class="report-table">
                    <thead>
                        <?php if ($type === 'revisions'): ?>
                            <tr>
                                <th>Date Updated</th>
                                <th>Inmate Profile</th>
                                <th>Charge Revision (From → To)</th>
                                <th>Authorized By</th>
                            </tr>
                        <?php elseif ($type === 'sentence_revisions'): ?>
                            <tr>
                                <th>Date Adjusted</th>
                                <th>Inmate Profile</th>
                                <th>Sentence Adjustment (Old → New)</th>
                                <th>Authorized By</th>
                            </tr>
                        <?php elseif ($type === 'crime'): ?>
                            <tr>
                                <th>Offence Category</th>
                                <th>Total Inmates</th>
                                <th>Most Recent Admission</th>
                                <th>Risk Assessment</th>
                            </tr>
                        <?php elseif ($type === 'kazi'): ?>
                            <tr>
                                <th>Inmate ID</th>
                                <th>Inmate Name</th>
                                <th>Workshop</th>
                                <th>Total Hours</th>
                                <th>Eligibility</th>
                            </tr>
                        <?php elseif ($type === 'housing'): ?>
                            <tr>
                                <th>Block Name</th>
                                <th>Classification</th>
                                <th>Total Capacity</th>
                                <th>Current Pop.</th>
                                <th>Available</th>
                            </tr>
                        <?php elseif ($type === 'releases'): ?>
                            <tr>
                                <th>KIMS-ID</th>
                                <th>Inmate Name</th>
                                <th>Offence</th>
                                <th>Discharge Date</th>
                                <th>Authorized By</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Hearing Date</th>
                                <th>Inmate Name</th>
                                <th>KIMS-ID</th>
                                <th>Court Location</th>
                                <th>Remarks</th>
                                <th>Virtual Link</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultData && $resultData->num_rows > 0) {
                            while($row = $resultData->fetch_assoc()) {
                                if ($type === 'revisions') {
                                    $date = date('d/m/Y H:i', strtotime($row['update_date']));
                                    echo "<tr>
                                            <td>$date</td>
                                            <td><strong>{$row['full_name']}</strong><br><small>ID: {$row['kims_id']}</small></td>
                                            <td>
                                                <span class='old-charge'>{$row['old_offence']}</span>
                                                <span class='update-arrow'>➔</span>
                                                <span class='new-charge'>{$row['new_offence']}</span>
                                            </td>
                                            <td>{$row['updated_by']}</td>
                                          </tr>";
                                }
                                elseif ($type === 'sentence_revisions') {
                                    $date = date('d/m/Y H:i', strtotime($row['update_date']));
                                    echo "<tr>
                                            <td>$date</td>
                                            <td><strong>{$row['full_name']}</strong><br><small>ID: {$row['kims_id']}</small></td>
                                            <td>
                                                <span class='old-charge'>" . number_format($row['old_years'], 2) . " Yrs</span>
                                                <span class='update-arrow'>➔</span>
                                                <span class='new-charge'>" . number_format($row['new_years'], 2) . " Yrs</span>
                                            </td>
                                            <td>{$row['updated_by']}</td>
                                          </tr>";
                                }
                                elseif ($type === 'crime') {
                                    $risk = ($row['total_inmates'] > 5) ? "<span style='color:red;'>High Density</span>" : "Normal";
                                    $lastDate = $row['last_update'] ? date('d/m/Y', strtotime($row['last_update'])) : "N/A";
                                    echo "<tr><td><strong>" . strtoupper($row['offence_category']) . "</strong></td><td>{$row['total_inmates']}</td><td>$lastDate</td><td>$risk</td></tr>";
                                } 
                                elseif ($type === 'kazi') {
                                    $eligibility = ($row['total_hours'] >= 100) ? "<strong>Certified</strong>" : "In Progress";
                                    echo "<tr><td>{$row['kims_id']}</td><td>{$row['full_name']}</td><td>{$row['workshop_name']}</td><td>{$row['total_hours']}</td><td>$eligibility</td></tr>";
                                } 
                                elseif ($type === 'housing') {
                                    echo "<tr><td>{$row['section']}</td><td>{$row['class']}</td><td>{$row['cap']}</td><td>{$row['pop']}</td><td>{$row['avail']}</td></tr>";
                                } 
                                elseif ($type === 'releases') {
                                    echo "<tr>
                                            <td><strong>{$row['kims_id']}</strong></td>
                                            <td>{$row['full_name']}</td>
                                            <td>{$row['offence_category']}</td>
                                            <td>" . date('d/m/Y', strtotime($row['edd'])) . "</td>
                                            <td>{$row['discharged_by']}</td>
                                          </tr>";
                                }
                                else {
                                    $formattedDate = date('d/m/Y', strtotime($row['hearing_date']));
                                    $zoom = !empty($row['zoom_link']) ? "<a href='{$row['zoom_link']}' target='_blank' style='color:#1a73e8;'>Join</a>" : "N/A";
                                    echo "<tr><td>$formattedDate</td><td>{$row['full_name']}</td><td>{$row['kims_id']}</td><td>{$row['court_name']}</td><td>{$row['remarks']}</td><td>$zoom</td></tr>";
                                }

                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding: 30px; color: #777;'>No matching records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>