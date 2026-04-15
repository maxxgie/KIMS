<?php
include 'db_connect.php';

$inmate = null;
$search_query = "";

if (isset($_GET['kims_id'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['kims_id']);
    
    // Comprehensive query joining housing, judicial (latest), and vocational (sum)
    $sql = "SELECT i.*, b.block_name, b.classification,
            (SELECT SUM(hours_logged) FROM training_logs WHERE inmate_id = i.inmate_id) as total_voc_hours,
            (SELECT next_hearing_date FROM court_records WHERE inmate_id = i.inmate_id ORDER BY next_hearing_date DESC LIMIT 1) as next_court
            FROM inmates i 
            LEFT JOIN cell_blocks b ON i.block_id = b.block_id
            WHERE i.kims_id = '$search_query'";
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $inmate = $result->fetch_assoc();
    } else {
        echo "<script>alert('Inmate record not found.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Inmate Master Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { display: grid; grid-template-columns: 250px 1fr; gap: 30px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .profile-sidebar { text-align: center; border-right: 1px solid #eee; padding-right: 30px; }
        .mugshot-large { width: 200px; height: 200px; object-fit: cover; border: 5px solid #f0f0f0; border-radius: 4px; margin-bottom: 15px; }
        .data-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .data-item { padding: 15px; background: #f9f9f9; border-left: 4px solid #1a73e8; }
        .data-item label { display: block; font-size: 11px; color: #666; font-weight: bold; text-transform: uppercase; }
        .data-item span { font-size: 18px; color: #333; font-weight: 600; }
        .status-tag { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 12px; margin-top: 10px; }
        .status-active { background: #e6fffa; color: #2c7a7b; }
        .status-released { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .no-print-search { margin-bottom: 20px; }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — Master Search</div>
        <div class="user-info"><a href="dashboard.php">Dashboard</a></div>
    </header>

    <main class="content">
        <div class="no-print-search">
            <form action="search.php" method="GET" class="search-box">
                <input type="text" name="kims_id" placeholder="Enter KIMS ID to pull profile..." value="<?php echo $search_query; ?>" required>
                <button type="submit" class="btn-primary">Execute Search</button>
            </form>
        </div>

        <?php if ($inmate): ?>
        <div class="profile-container">
            <div class="profile-sidebar">
                <img src="uploads/<?php echo $inmate['photo_url']; ?>" class="mugshot-large" alt="Inmate Photo">
                <h3 style="margin-bottom:5px;"><?php echo $inmate['kims_id']; ?></h3>
                <?php $statusClass = ($inmate['status'] == 'Released') ? 'status-released' : 'status-active'; ?>
                <div class="status-tag <?php echo $statusClass; ?>"><?php echo strtoupper($inmate['status']); ?></div>
                <hr style="margin: 20px 0;">
                <button onclick="window.print()" class="btn-secondary" style="width:100%;">Print Dossier</button>
            </div>

            <div class="profile-main">
                <h2 style="margin-top:0; color:#1a73e8;"><?php echo $inmate['full_name']; ?></h2>
                <div class="data-grid">
                    <div class="data-item">
                        <label>Current Housing</label>
                        <span><?php echo $inmate['block_name'] ?? 'UNASSIGNED'; ?> (<?php echo $inmate['classification'] ?? '-'; ?>)</span>
                    </div>
                    <div class="data-item">
                        <label>Offence Category</label>
                        <span><?php echo $inmate['offence_category']; ?></span>
                    </div>
                    <div class="data-item">
                        <label><?php echo ($inmate['status'] == 'Released') ? 'Actual Discharge Date' : 'Earliest Discharge Date (EDD)'; ?></label>
                        <span style="color: #d32f2f;"><?php echo date('d M Y', strtotime($inmate['edd'])); ?></span>
                    </div>
                    <?php if ($inmate['status'] == 'Released'): ?>
                    <div class="data-item">
                        <label>Discharge Authorized By</label>
                        <span><?php echo htmlspecialchars($inmate['discharged_by'] ?? 'N/A'); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="data-item">
                        <label>Next Court Appearance</label>
                        <span><?php echo $inmate['next_court'] ? date('d M Y', strtotime($inmate['next_court'])) : 'No Dates Set'; ?></span>
                    </div>
                    <div class="data-item">
                        <label>Vocational Training</label>
                        <span><?php echo $inmate['total_voc_hours'] ?? '0'; ?> Hours Logged</span>
                    </div>
                    <div class="data-item">
                        <label>Sentence Duration</label>
                        <span><?php echo $inmate['sentence_years']; ?> Years</span>
                    </div>
                </div>
                
                <div style="margin-top:30px; padding:15px; border:1px solid #eee; font-size:13px; color:#777;">
                    <strong>Biometric Note:</strong> Records match National ID <?php echo $inmate['id_number']; ?>. Physical verification required for movement.
                </div>
            </div>
        </div>
        <?php else: ?>
            <div style="text-align:center; padding:100px; color:#999; border: 2px dashed #ccc; background:#fff;">
                <h1>Ready for Search</h1>
                <p>Input a KIMS ID above to view the complete inmate dossier.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>