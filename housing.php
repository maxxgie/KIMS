<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$inmate = null;
$search_query = "";

// 1. Fetch all blocks for the dropdown
$blocks_result = $conn->query("SELECT * FROM cell_blocks");

// 2. Search for inmate
if (isset($_GET['search_id'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search_id']);
    $sql = "SELECT i.*, b.block_name 
            FROM inmates i 
            LEFT JOIN cell_blocks b ON i.block_id = b.block_id 
            WHERE i.kims_id = '$search_query'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $inmate = $result->fetch_assoc();
    } else {
        echo "<script>alert('Inmate not found.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Housing Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Section Title matching Dashboard/Registration Style */
        .section-header { 
            background: #eee; padding: 10px 15px; font-weight: bold; 
            border-left: 5px solid #333; margin: 25px 0 10px 0; 
            font-size: 13px; text-transform: uppercase; 
        }
        .placeholder-box { 
            margin-top: 100px; text-align: center; border: 2px dashed #ccc; 
            padding: 80px; color: #888; border-radius: 8px; 
        }
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
        <div class="user-info">Duty Officer: <?php echo $_SESSION['full_name'] ?? 'Warden'; ?> | <a href="logout.php">[Logout]</a></div>
    </header>

    <nav class="breadcrumb">Home > Housing / Cell Block > Transfer Management</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <div class="header-actions">
                <h2>Cell Assignment & Transfers</h2>
                <form action="housing.php" method="GET" class="search-box">
                    <input type="text" name="search_id" placeholder="Enter KIMS-ID..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="btn-primary">LOCATE</button>
                </form>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-size: 13px; font-weight: bold;">
                    ✔ Housing assignment updated successfully.
                </div>
            <?php endif; ?>

            <?php if ($inmate): ?>
            
            <div class="profile-summary" style="display: flex; gap: 20px; background: #fff; border: 1px solid #ddd; padding: 20px; margin-top: 20px;">
                <div class="profile-photo-container">
                    <?php 
                        $photo_filename = !empty($inmate['photo_url']) ? $inmate['photo_url'] : 'default.png';
                        $photo_path = "uploads/" . $photo_filename;
                        
                        if (file_exists($photo_path)) {
                            echo '<img src="' . $photo_path . '" alt="Subject Mugshot">';
                        } else {
                            echo '<div style="font-size: 10px; color: #999; text-align: center;">IMAGE NOT<br>FOUND</div>';
                        }
                    ?>
                </div>

                <div style="flex-grow: 1;">
                    <div class="section-header" style="margin-top:0;">1. Subject Location Details</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; font-size: 13px;">
                        <div><strong>NAME:</strong> <?php echo strtoupper($inmate['full_name']); ?></div>
                        <div><strong>KIMS-ID:</strong> <?php echo $inmate['kims_id']; ?></div>
                        <div><strong>CURRENT BLOCK:</strong> <span style="color: #d32f2f; font-weight: bold;"><?php echo strtoupper($inmate['block_name'] ?? 'UNASSIGNED'); ?></span></div>
                    </div>
                </div>
            </div>

            <div class="section-header">2. Internal Transfer Authorization</div>
            <div class="judicial-form">
                <form action="save_housing.php" method="POST" class="form-grid">
                    <input type="hidden" name="inmate_id" value="<?php echo $inmate['inmate_id']; ?>">
                    <input type="hidden" name="kims_id" value="<?php echo $inmate['kims_id']; ?>">
                    
                    <div class="form-group full-width">
                        <label>SELECT DESTINATION BLOCK</label>
                        <select name="block_id" required>
                            <option value="">-- Choose Target Block --</option>
                            <?php 
                            $blocks_result->data_seek(0); 
                            while($b = $blocks_result->fetch_assoc()): 
                            ?>
                                <option value="<?php echo $b['block_id']; ?>" <?php echo ($inmate['block_id'] == $b['block_id']) ? 'disabled' : ''; ?>>
                                    <?php echo $b['block_name']; ?> (<?php echo $b['classification']; ?>) — Capacity: <?php echo $b['capacity']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <p style="font-size: 10px; color: #777; margin-top: 5px;">Current assigned block is disabled to prevent redundant transfers.</p>
                    </div>

                    <div class="full-width" style="margin-top: 10px;">
                        <button type="submit" class="btn-primary" style="width: 100%;">AUTHORIZE INTERNAL TRANSFER</button>
                    </div>
                </form>
            </div>

            <?php else: ?>
                <div class="placeholder-box">
                    <h3 style="color: #999; text-transform: uppercase; letter-spacing: 1px;">Inmate Housing Control</h3>
                    <p style="color: #bbb; font-size: 13px;">Search for an inmate to initiate a cell transfer or view current housing assignments.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>