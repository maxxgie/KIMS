<?php
include 'db_connect.php';

// Query to get each block's name, capacity, and current number of inmates
$sql = "SELECT b.block_name, b.classification, b.capacity, 
               COUNT(i.inmate_id) as current_occupancy 
        FROM cell_blocks b 
        LEFT JOIN inmates i ON b.block_id = i.block_id AND i.status = 'In Custody'
        GROUP BY b.block_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — Housing Audit</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .occupancy-bar-bg { background: #eee; border-radius: 10px; width: 100%; height: 10px; margin-top: 5px; overflow: hidden; }
        .occupancy-bar-fill { height: 100%; background: #1a73e8; border-radius: 10px; }
        .full { background: #d32f2f !important; }
        .status-pill { padding: 4px 10px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-low { background: #e6fffa; color: #2c7a7b; }
        .status-critical { background: #fff5f5; color: #c53030; }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — Housing & Occupancy Audit</div>
        <div class="user-info"><a href="dashboard.php">Back to Dashboard</a></div>
    </header>

    <main class="content" style="padding: 40px;">
        <h2>Facility Capacity Report</h2>
        <p>Real-time occupancy tracking for King'ong'o Prison Blocks.</p>
        <hr>

        <div class="report-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <?php while($row = $result->fetch_assoc()): 
                $percentage = ($row['capacity'] > 0) ? ($row['current_occupancy'] / $row['capacity']) * 100 : 0;
                $status_class = ($percentage > 90) ? 'status-critical' : 'status-low';
                $bar_class = ($percentage > 90) ? 'full' : '';
            ?>
                <div class="stat-card" style="border: 1px solid #ddd; padding: 20px; background: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3 style="margin: 0;"><?php echo $row['block_name']; ?></h3>
                            <small><?php echo $row['classification']; ?> Section</small>
                        </div>
                        <span class="status-pill <?php echo $status_class; ?>">
                            <?php echo round($percentage); ?>% Full
                        </span>
                    </div>

                    <div style="margin: 20px 0;">
                        <span style="font-size: 24px; font-weight: bold;"><?php echo $row['current_occupancy']; ?></span>
                        <span style="color: #666;">/ <?php echo $row['capacity']; ?> Inmates</span>
                        
                        <div class="occupancy-bar-bg">
                            <div class="occupancy-bar-fill <?php echo $bar_class; ?>" style="width: <?php echo min($percentage, 100); ?>%;"></div>
                        </div>
                    </div>
                    
                    <button onclick="location.href='search.php?block_id=<?php echo $row['block_name']; ?>'" class="btn-secondary" style="width: 100%; font-size: 12px;">View Block List</button>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>