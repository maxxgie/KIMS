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
    // Search by KIMS ID in the inmates table
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
        
        .section-header { 
            background: #eee; padding: 10px 15px; font-weight: bold; 
            border-left: 5px solid #333; margin: 25px 0 10px 0; 
            font-size: 12px; text-transform: uppercase; 
        }

        .report-table { width: 100%; border-collapse: collapse; border: 1px solid #ddd; background: #fff; }
        .report-table th { background: #f8f8f8; text-align: left; padding: 12px; border: 1px solid #ddd; font-size: 11px; color: #555; text-transform: uppercase; }
        .report-table td { padding: 12px; border: 1px solid #ddd; font-size: 13px; }

        .btn-amend {
            padding: 6px 15px; background: #333; color: white; border: none; 
            cursor: pointer; font-size: 11px; font-weight: bold; transition: background 0.2s;
        }
        .btn-amend:hover { background: #000; }
        
        .crime-select {
            padding: 8px; border: 1px solid #ccc; width: 450px; 
            font-size: 13px; background: #fffde7;
        }

        /* Styles for the mugshot container */
        .profile-photo-container {
            width: 140px; 
            height: 160px; 
            background: #f0f0f0; 
            border: 2px solid #ddd; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            overflow: hidden;
        }
        .profile-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
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
                
                <div class="profile-photo-container">
                    <?php 
                        $photo_filename = !empty($inmate['photo_url']) ? $inmate['photo_url'] : 'default.png';
                        $photo_path = "uploads/" . $photo_filename;
                        
                        if (file_exists($photo_path)) {
                            echo '<img src="'.$photo_path.'" alt="Subject Mugshot">';
                        } else {
                            echo '<div style="font-size: 10px; color: #999; text-align: center;">IMAGE NOT<br>FOUND</div>';
                        }
                    ?>
                </div>

                <div style="flex-grow: 1;">
                    <div class="section-header" style="margin-top:0;">1. Subject Information</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; font-size: 13px;">
                        <div><strong>NAME:</strong> <?php echo strtoupper($inmate['full_name']); ?></div>
                        <div><strong>KIMS-ID:</strong> <?php echo $inmate['kims_id']; ?></div>
                        <div><strong>STATUS:</strong> <span style="color: #1a73e8; font-weight: bold;"><?php echo strtoupper($inmate['status']); ?></span></div>
                    </div>

                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ddd;">
                        <form action="update_offence.php" method="POST" 
                              onsubmit="return confirm('WARNING: You are about to officially change the legal charge for this inmate in the KIMS database. Do you wish to proceed?');"
                              style="display: flex; align-items: center; gap: 15px;">
                            <input type="hidden" name="inmate_id" value="<?php echo $inmate['inmate_id']; ?>">
                            <input type="hidden" name="kims_id" value="<?php echo $inmate['kims_id']; ?>">
                            
                            <label style="font-size: 11px; font-weight: bold; color: #555;">AMEND CHARGE:</label>
                            
                            <select name="new_offence" class="crime-select" required>
                                <option value="">-- Select Verified Offence (Penal Code) --</option>
                                
                                <optgroup label="Offences Against Persons">
                                    <option value="Murder" <?php if($inmate['offence_category'] == 'Murder') echo 'selected'; ?>>Murder</option>
                                    <option value="Attempted Murder" <?php if($inmate['offence_category'] == 'Attempted Murder') echo 'selected'; ?>>Attempted Murder</option>
                                    <option value="Manslaughter" <?php if($inmate['offence_category'] == 'Manslaughter') echo 'selected'; ?>>Manslaughter</option>
                                    <option value="Assault causing GH" <?php if($inmate['offence_category'] == 'Assault causing GH') echo 'selected'; ?>>Assault causing Grievous Harm</option>
                                    <option value="Common Assault" <?php if($inmate['offence_category'] == 'Common Assault') echo 'selected'; ?>>Common Assault</option>
                                    <option value="Threatening Violence" <?php if($inmate['offence_category'] == 'Threatening Violence') echo 'selected'; ?>>Threatening Violence</option>
                                    <option value="Kidnapping / Abduction" <?php if($inmate['offence_category'] == 'Kidnapping / Abduction') echo 'selected'; ?>>Kidnapping / Abduction</option>
                                    <option value="Affray" <?php if($inmate['offence_category'] == 'Affray') echo 'selected'; ?>>Affray (Public Fighting)</option>
                                </optgroup>

                                <optgroup label="Sexual Offences">
                                    <option value="Defilement" <?php if($inmate['offence_category'] == 'Defilement') echo 'selected'; ?>>Defilement</option>
                                    <option value="Rape" <?php if($inmate['offence_category'] == 'Rape') echo 'selected'; ?>>Rape</option>
                                    <option value="Attempted Rape" <?php if($inmate['offence_category'] == 'Attempted Rape') echo 'selected'; ?>>Attempted Rape</option>
                                    <option value="Sexual Assault" <?php if($inmate['offence_category'] == 'Sexual Assault') echo 'selected'; ?>>Sexual Assault</option>
                                    <option value="Indecent Act" <?php if($inmate['offence_category'] == 'Indecent Act') echo 'selected'; ?>>Indecent Act</option>
                                    <option value="Incest" <?php if($inmate['offence_category'] == 'Incest') echo 'selected'; ?>>Incest</option>
                                    <option value="Sodomy" <?php if($inmate['offence_category'] == 'Sodomy') echo 'selected'; ?>>Unnatural Offences (Sodomy)</option>
                                </optgroup>

                                <optgroup label="Offences Against Property">
                                    <option value="Robbery with Violence" <?php if($inmate['offence_category'] == 'Robbery with Violence') echo 'selected'; ?>>Robbery with Violence</option>
                                    <option value="Simple Robbery" <?php if($inmate['offence_category'] == 'Simple Robbery') echo 'selected'; ?>>Simple Robbery</option>
                                    <option value="Burglary" <?php if($inmate['offence_category'] == 'Burglary') echo 'selected'; ?>>Burglary (Night)</option>
                                    <option value="House Breaking" <?php if($inmate['offence_category'] == 'House Breaking') echo 'selected'; ?>>House Breaking (Day)</option>
                                    <option value="Theft of Motor Vehicle" <?php if($inmate['offence_category'] == 'Theft of Motor Vehicle') echo 'selected'; ?>>Theft of Motor Vehicle</option>
                                    <option value="Stealing by Servant" <?php if($inmate['offence_category'] == 'Stealing by Servant') echo 'selected'; ?>>Stealing by Servant</option>
                                    <option value="General Stealing" <?php if($inmate['offence_category'] == 'General Stealing') echo 'selected'; ?>>General Stealing</option>
                                    <option value="Stock Theft" <?php if($inmate['offence_category'] == 'Stock Theft') echo 'selected'; ?>>Stock Theft (Cattle)</option>
                                    <option value="Handling Stolen Goods" <?php if($inmate['offence_category'] == 'Handling Stolen Goods') echo 'selected'; ?>>Handling Stolen Property</option>
                                    <option value="Arson" <?php if($inmate['offence_category'] == 'Arson') echo 'selected'; ?>>Arson (Setting Fire)</option>
                                    <option value="Malicious Damage" <?php if($inmate['offence_category'] == 'Malicious Damage') echo 'selected'; ?>>Malicious Damage to Property</option>
                                </optgroup>

                                <optgroup label="Economic & Fraud Offences">
                                    <option value="Obtaining by False Pretences" <?php if($inmate['offence_category'] == 'Obtaining by False Pretences') echo 'selected'; ?>>Obtaining by False Pretences</option>
                                    <option value="Forgery" <?php if($inmate['offence_category'] == 'Forgery') echo 'selected'; ?>>Forgery</option>
                                    <option value="Uttering False Documents" <?php if($inmate['offence_category'] == 'Uttering False Documents') echo 'selected'; ?>>Uttering False Documents</option>
                                    <option value="Money Laundering" <?php if($inmate['offence_category'] == 'Money Laundering') echo 'selected'; ?>>Money Laundering</option>
                                    <option value="Conspiracy to Defraud" <?php if($inmate['offence_category'] == 'Conspiracy to Defraud') echo 'selected'; ?>>Conspiracy to Defraud</option>
                                    <option value="Cyber Crime" <?php if($inmate['offence_category'] == 'Cyber Crime') echo 'selected'; ?>>Cyber Crime Act</option>
                                </optgroup>

                                <optgroup label="Drug & Narcotics Offences">
                                    <option value="Trafficking Narcotics" <?php if($inmate['offence_category'] == 'Trafficking Narcotics') echo 'selected'; ?>>Trafficking in Narcotics</option>
                                    <option value="Possession of Narcotics" <?php if($inmate['offence_category'] == 'Possession of Narcotics') echo 'selected'; ?>>Possession of Narcotics</option>
                                    <option value="Cultivation of Bhang" <?php if($inmate['offence_category'] == 'Cultivation of Bhang') echo 'selected'; ?>>Cultivation of Forbidden Plants</option>
                                </optgroup>

                                <optgroup label="Public Order & State">
                                    <option value="Treason" <?php if($inmate['offence_category'] == 'Treason') echo 'selected'; ?>>Treason</option>
                                    <option value="Incitement to Violence" <?php if($inmate['offence_category'] == 'Incitement to Violence') echo 'selected'; ?>>Incitement to Violence</option>
                                    <option value="Terrorism" <?php if($inmate['offence_category'] == 'Terrorism') echo 'selected'; ?>>Terrorism Related</option>
                                    <option value="Escaping from Custody" <?php if($inmate['offence_category'] == 'Escaping from Custody') echo 'selected'; ?>>Escaping Lawful Custody</option>
                                    <option value="Bribery" <?php if($inmate['offence_category'] == 'Bribery') echo 'selected'; ?>>Bribery / Corruption</option>
                                </optgroup>

                                <optgroup label="Miscellaneous">
                                    <option value="Possession of Firearm" <?php if($inmate['offence_category'] == 'Possession of Firearm') echo 'selected'; ?>>Illegal Possession of Firearm</option>
                                    <option value="Wildlife Crimes" <?php if($inmate['offence_category'] == 'Wildlife Crimes') echo 'selected'; ?>>Poaching / Wildlife Crimes</option>
                                    <option value="Other Felony" <?php if($inmate['offence_category'] == 'Other Felony') echo 'selected'; ?>>Other Felony</option>
                                    <option value="Other Misdemeanor" <?php if($inmate['offence_category'] == 'Other Misdemeanor') echo 'selected'; ?>>Other Misdemeanor</option>
                                </optgroup>
                            </select>
                            
                            <button type="submit" class="btn-amend">UPDATE LEGAL CHARGE</button>
                        </form>
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
                                               style="color: #d32f2f; font-size: 11px; font-weight: bold; text-decoration: none;"
                                               onclick="return confirm('WARNING: Are you sure you want to remove this official court entry?');">[ PURGE ]
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