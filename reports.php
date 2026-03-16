<?php
include 'db_connect.php';

// Get the report type from URL, default to 'court'
$type = isset($_GET['type']) ? $_GET['type'] : 'court';
$reportTitle = "";
$resultData = null;

// Determine SQL Query and Title based on selection
switch ($type) {
    case 'kazi':
        $reportTitle = "Vocational Enrollment Summary";
        // Logic: Joins inmates with training logs and sums their hours
        $sql = "SELECT i.kims_id, i.full_name, t.workshop_name, SUM(t.hours_logged) as total_hours 
                FROM inmates i 
                INNER JOIN training_logs t ON i.inmate_id = t.inmate_id 
                GROUP BY i.inmate_id, t.workshop_name";
        break;
    
    case 'housing':
        $reportTitle = "Occupancy Audit Report";
        // Static simulation for now; usually this would query a 'cells' table
        $sql = "SELECT 'Block A' as section, 'General' as class, 150 as cap, 142 as pop, 8 as avail 
                UNION SELECT 'Block B', 'General', 150, 148, 2
                UNION SELECT 'Block C', 'Maximum', 50, 50, 0
                UNION SELECT 'Block D', 'Special', 30, 12, 18";
        break;

    case 'court':
    default:
        $type = 'court'; // Reset to default if invalid type provided
        $reportTitle = "Upcoming Court Schedule";
        // Logic: Joins inmates with their court appearance records
        $sql = "SELECT i.kims_id, i.full_name, c.court_name, c.next_hearing_date as hearing_date, c.remarks 
                FROM inmates i 
                INNER JOIN court_records c ON i.inmate_id = c.inmate_id 
                ORDER BY c.next_hearing_date ASC";
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
                <div class="report-card <?php echo ($type == 'kazi') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=kazi'">
                    <h4>Vocational Enrollment</h4>
                    <p>Training hours and certificate eligibility.</p>
                </div>
                <div class="report-card <?php echo ($type == 'court') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=court'">
                    <h4>Court Schedule</h4>
                    <p>Upcoming hearings and transport requirements.</p>
                </div>
                <div class="report-card <?php echo ($type == 'housing') ? 'active' : ''; ?>" onclick="location.href='reports.php?type=housing'">
                    <h4>Occupancy Audit</h4>
                    <p>Block-wise population and cell availability.</p>
                </div>
            </div>

            <section class="filter-bar">
                <div class="filter-group">
                    <label>Target Date</label>
                    <input type="date" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="filter-group">
                    <label>Section Filter</label>
                    <select>
                        <option>All Sections</option>
                        <option>Block A</option>
                        <option>Block B</option>
                        <option>Tailoring Workshop</option>
                    </select>
                </div>
                <button class="btn-primary">Filter Results</button>
            </section>

            <div class="report-preview">
                <div class="preview-header">
                    <h3 id="reportTitle">PREVIEW: <?php echo $reportTitle; ?></h3>
                    <div class="preview-actions">
                        <button class="btn-secondary" onclick="window.print()">[ PRINT PDF ]</button>
                    </div>
                </div>

                <table class="report-table">
                    <thead>
                        <?php if ($type === 'kazi'): ?>
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
                        <?php else: ?>
                            <tr>
                                <th>Hearing Date</th>
                                <th>Inmate Name</th>
                                <th>KIMS-ID</th>
                                <th>Court Location</th>
                                <th>Remarks</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultData && $resultData->num_rows > 0) {
                            while($row = $resultData->fetch_assoc()) {
                                if ($type === 'kazi') {
                                    $eligibility = ($row['total_hours'] >= 100) ? "<strong>Certified</strong>" : "In Progress";
                                    echo "<tr><td>{$row['kims_id']}</td><td>{$row['full_name']}</td><td>{$row['workshop_name']}</td><td>{$row['total_hours']}</td><td>$eligibility</td></tr>";
                                } 
                                elseif ($type === 'housing') {
                                    echo "<tr><td>{$row['section']}</td><td>{$row['class']}</td><td>{$row['cap']}</td><td>{$row['pop']}</td><td>{$row['avail']}</td></tr>";
                                } 
                                else {
                                    $formattedDate = date('d/m/Y', strtotime($row['hearing_date']));
                                    echo "<tr><td>$formattedDate</td><td>{$row['full_name']}</td><td>{$row['kims_id']}</td><td>{$row['court_name']}</td><td>{$row['remarks']}</td></tr>";
                                }
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>No records found for this report type.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>