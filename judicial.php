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
        .highlight-today { background: #fff3f3; font-weight: bold; border-left: 3px solid #d32f2f; }
        
        /* Section Title matching Dashboard/Registration Style */
        .section-header { 
            background: #eee; padding: 10px 15px; font-weight: bold; 
            border-left: 5px solid #333; margin: 25px 0 10px 0; 
            font-size: 12px; text-transform: uppercase; 
        }

        .report-table { width: 100%; border-collapse: collapse; border: 1px solid #ddd; background: #fff; }
        .report-table th { background: #f8f8f8; text-align: left; padding: 12px; border: 1px solid #ddd; font-size: 11px; color: #555; text-transform: uppercase; }
        .report-table td { padding: 12px; border: 1px solid #ddd; font-size: 13px; }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — King'ong'o Inmate Management System</div>
        <div class="user-info">
            Duty Officer: <?php echo htmlspecialchars($_SESSION['username']); ?> | 
            <a href="dashboard.php" style="color:white;">[Dashboard]</a>
        </div>
    </header>

    <nav class="breadcrumb">Home > Judicial Tracking > Court Logistics</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <div class="header-actions">
                <h2 style="margin:0;">Judicial Tracking & Logistics</h2>
                <form action="judicial.php" method="GET" class="search-box" style="display: flex; gap: 10px;">
                    <input type="text" name="search_id" style="padding: 8px; width: 250px; border: 1px solid #ccc;" placeholder="Enter KIMS ID" value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="btn-primary" style="padding: 8px 20px; background: #333; color: white; border: none; cursor: pointer;">LOAD RECORDS</button>
                </form>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; margin: 20px 0; border: 1px solid #c8e6c9; font-size: 13px; font-weight: bold;">
                    ✔ Action successfully committed to legal records.
                </div>
            <?php endif; ?>

            <?php if ($inmate): ?>
            
            <div class="profile-summary" style="display: flex; gap: 20px; background: #fff; border: 1px solid #ddd; padding: 20px; margin-top: 20px;">
                <div class="profile-photo" style="width: 100px; height: 100px; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999; border: 1px solid #ccc;">MUGSHOT</div>
                <div style="flex-grow: 1;">
                    <div class="section-header" style="margin-top:0;">1. Subject Information</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; font-size: 13px;">
                        <div><strong>NAME:</strong> <?php echo strtoupper($inmate['full_name']); ?></div>
                        <div><strong>KIMS-ID:</strong> <?php echo $inmate['kims_id']; ?></div>
                        <div><strong>STATUS:</strong> <span style="color: #1a73e8; font-weight: bold;"><?php echo strtoupper($inmate['status']); ?></span></div>
                    </div>
                </div>
            </div>

            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-top: 20px;">
                <section>
                    <div class="section-header">2. Schedule New Appearance</div>
                    <div class="judicial-form" style="background: #fff; border: 1px solid #ddd; padding: 20px;">
                        <form action="save_court_record.php" method="POST">
                            <input type="hidden" name="inmate_id" value="<?php echo $inmate['inmate_id']; ?>">
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display:block; font-size: 11px; font-weight: bold; margin-bottom: 5px;">COURT NAME / LOCATION</label>
                                <input type="text" name="court_name" placeholder="e.g. Nyeri High Court" required style="width:100%; padding:10px; border: 1px solid #ccc;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display:block; font-size: 11px; font-weight: bold; margin-bottom: 5px;">HEARING DATE</label>
                                <input type="date" name="hearing_date" required style="width:100%; padding:10px; border: 1px solid #ccc;">
                            </div>
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="display:block; font-size: 11px; font-weight: bold; margin-bottom: 5px;">REMARKS / CASE DETAILS</label>
                                <textarea name="remarks" placeholder="Mention, Bail Application, etc." style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ccc; font-size: 12px;"></textarea>
                            </div>
                            
                            <button type="submit" class="btn-primary" style="width: 100%; padding: 12px; background: #1a73e8; color: white; border: none; font-weight: bold;">COMMIT TO LEGAL LOGS</button>
                        </form>
                    </div>
                </section>

                <section>
                    <div class="section-header">3. Judicial History Transcript</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Hearing Date</th>
                                <th>Court Location</th>
                                <th>Remarks</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($court_history && $court_history->num_rows > 0): ?>
                                <?php while($h = $court_history->fetch_assoc()): 
                                    $isToday = ($h['next_hearing_date'] == date('Y-m-d'));
                                ?>
                                    <tr class="<?php echo $isToday ? 'highlight-today' : ''; ?>">
                                        <td><strong><?php echo date('d-m-Y', strtotime($h['next_hearing_date'])); ?></strong></td>
                                        <td><?php echo htmlspecialchars($h['court_name']); ?></td>
                                        <td style="font-size: 11px; color: #666;"><?php echo htmlspecialchars($h['remarks'] ?? 'No detail provided'); ?></td>
                                        <td style="text-align: center;">
                                            <a href="delete_record.php?type=court&id=<?php echo $h['record_id']; ?>"
                                               class="btn-action btn-delete" 
                                               onclick="return confirm('WARNING: Are you sure you want to remove this official court entry?');">PURGE
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center; padding: 40px; color: #999;">No judicial history found for this subject.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            </div>

            <?php else: ?>
                <div style="text-align:center; margin-top:80px; border: 2px dashed #ddd; padding: 80px; background: #fdfdfd;">
                    <div style="font-size: 40px; color: #ccc; margin-bottom: 15px;">⚖</div>
                    <h3 style="color: #999; text-transform: uppercase; letter-spacing: 1px;">Ready for Judicial Processing</h3>
                    <p style="color: #bbb; font-size: 13px;">Enter a valid <strong>KIMS-ID</strong> to retrieve legal transcripts and schedule logistics.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>