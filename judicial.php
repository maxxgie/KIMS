<?php
// 1. Security & Session Management
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$inmate = null;
$search_query = "";
$court_history = null;

// 2. Search Logic
if (isset($_GET['search_id'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search_id']);
    // Search by KIMS ID
    $sql = "SELECT * FROM inmates WHERE kims_id = '$search_query'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $inmate = $result->fetch_assoc();
        $inmate_id = $inmate['inmate_id'];
        
        // Fetch existing court history for this specific inmate
        $history_sql = "SELECT * FROM court_records WHERE inmate_id = $inmate_id ORDER BY next_hearing_date DESC";
        $court_history = $conn->query($history_sql);
    } else {
        echo "<script>alert('No inmate record found for ID: $search_query');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIMS — Judicial Tracking</title>
    <link rel="stylesheet" href="style.css"> 
    <style>
        /* Table styles specific to this page but following dashboard theme */
        .report-table { width: 100%; border-collapse: collapse; border: 1px solid #ddd; margin-top: 10px; }
        .report-table th { background: #f8f8f8; text-align: left; padding: 12px; border: 1px solid #ddd; font-size: 11px; text-transform: uppercase; color: #555; }
        .report-table td { padding: 12px; border: 1px solid #ddd; font-size: 13px; }
        .highlight-today { background: #fff3f3; font-weight: bold; border-left: 3px solid #d32f2f; }
        
        /* Section Title matching Dashboard/Registration Style */
        .section-header { 
            background: #eee; padding: 10px 15px; font-weight: bold; 
            border-left: 5px solid #333; margin: 25px 0 10px 0; 
            font-size: 13px; text-transform: uppercase; 
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">
            Duty Officer: <?php echo $_SESSION['full_name'] ?? 'Warden'; ?> | 
            <a href="dashboard.php">[Dashboard]</a>
        </div>
    </header>

    <nav class="breadcrumb">Home > Judicial Tracking > Court Logistics</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <div class="header-actions">
                <h2>Judicial Tracking & Logistics</h2>
                <form action="judicial.php" method="GET" class="search-box">
                    <input type="text" name="search_id" placeholder="Enter KIMS ID (e.g. KIMS-2026-6308)" value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="btn-primary">LOAD RECORDS</button>
                </form>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-size: 13px; font-weight: bold;">
                    ✔ Court Appearance successfully logged in the system.
                </div>
            <?php endif; ?>

            <?php if ($inmate): ?>
            
            <div class="profile-summary">
                <div class="profile-photo">MUGSHOT</div>
                <div style="flex-grow: 1;">
                    <div class="section-header" style="margin-top:0;">1. Subject Information</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; font-size: 13px;">
                        <div><strong>NAME:</strong> <?php echo strtoupper($inmate['full_name']); ?></div>
                        <div><strong>KIMS-ID:</strong> <?php echo $inmate['kims_id']; ?></div>
                        <div><strong>STATUS:</strong> <span style="color: #1a73e8; font-weight: bold;"><?php echo strtoupper($inmate['status']); ?></span></div>
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <section>
                    <div class="section-header">2. Schedule New Appearance</div>
                    <div class="judicial-form">
                        <form action="save_court_record.php" method="POST">
                            <input type="hidden" name="inmate_id" value="<?php echo $inmate['inmate_id']; ?>">
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label>COURT NAME / LOCATION</label>
                                <input type="text" name="court_name" placeholder="e.g. Nyeri High Court - Room 2" required>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label>HEARING DATE</label>
                                <input type="date" name="hearing_date" required>
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label>REMARKS / CASE DETAILS</label>
                                <textarea name="remarks" placeholder="Mention, Bail Application, Judgement..." style="width: 100%; height: 80px; padding: 8px; border: 1px solid #ccc; font-size: 12px;"></textarea>
                            </div>
                            
                            <button type="submit" class="btn-primary" style="width: 100%;">COMMIT TO LEGAL LOGS</button>
                        </form>
                    </div>
                </section>

                <section>
                    <div class="section-header">3. Judicial History Transcript</div>
                    <div class="judicial-form" style="padding: 0; border: none;">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Hearing Date</th>
                                    <th>Court Location</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($court_history && $court_history->num_rows > 0): ?>
                                    <?php while($h = $court_history->fetch_assoc()): 
                                        $isToday = ($h['next_hearing_date'] == date('Y-m-d'));
                                    ?>
                                        <tr class="<?php echo $isToday ? 'highlight-today' : ''; ?>">
                                            <td><?php echo date('d-m-Y', strtotime($h['next_hearing_date'])); ?></td>
                                            <td><?php echo $h['court_name']; ?></td>
                                            <td><?php echo $row['remarks'] ?? 'No detail provided'; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" style="text-align:center; padding: 20px; color: #999;">No judicial history found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <?php else: ?>
                <div style="text-align:center; margin-top:100px; border: 2px dashed #ccc; padding: 60px; background: #fafafa;">
                    <h3 style="color: #999; text-transform: uppercase; letter-spacing: 1px;">Ready for Judicial Processing</h3>
                    <p style="color: #bbb; font-size: 13px;">Enter a valid <strong>KIMS-ID</strong> to retrieve legal transcripts and schedule logistics.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>