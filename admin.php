<?php
session_start();
// Security check: Ensure only logged-in users can access
if (!isset($_SESSION['username'])) { 
    header("Location: login.php"); 
    exit(); 
}
include 'db_connect.php';

// Fetch all staff/users
$staff_sql = "SELECT * FROM users ORDER BY full_name ASC";
$staff_result = $conn->query($staff_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — System Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Global Table Styles to match Dashboard */
        .report-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: 1px solid #ddd; 
            background: #fff; 
        }
        .report-table th { 
            background: #f8f8f8; 
            text-align: left; 
            padding: 12px; 
            border: 1px solid #ddd; 
            font-size: 11px; 
            color: #555; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        .report-table td { 
            padding: 12px; 
            border: 1px solid #ddd; 
            font-size: 13px; 
            vertical-align: middle; 
        }
        
        /* Section Header consistency */
        .section-header-bar { 
            background: #eee; 
            padding: 10px 15px; 
            font-weight: bold; 
            border-left: 5px solid #333; 
            margin: 0 0 15px 0; 
            font-size: 12px; 
            text-transform: uppercase; 
        }

        /* Status & Action Styles */
        .status-pill {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            background: #e8f5e9; 
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .action-link {
            color: #d32f2f; 
            font-size: 11px; 
            font-weight: bold; 
            text-decoration: none;
            white-space: nowrap;
        }
        
        .action-link:hover {
            text-decoration: underline;
        }

        /* Form styling from your Registration/Judicial pages */
        .judicial-form {
            background: #fff;
            border: 1px solid #ddd;
            padding: 25px;
        }
        
        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-family: inherit;
        }
    </style>
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — System Administration</div>
        <div class="user-info">
            Super Admin: <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="dashboard.php" style="color:white;">[Dashboard]</a>
        </div>
    </header>

    <nav class="breadcrumb">Home > System Settings > User & Staff Management</nav>

    <div class="main-container">
        <?php include 'sidebar.php'; ?>

        <main class="content">
            <div class="header-actions">
                <h2 style="margin:0 0 20px 0;">User & Staff Management</h2>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'deactivated'): ?>
                <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; margin-bottom: 20px; border: 1px solid #c8e6c9; font-size: 13px; font-weight: bold;">
                    ✔ User account has been successfully deactivated and removed from the system.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'self_delete'): ?>
                <div style="background: #fce8e6; color: #b71c1c; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-size: 13px; font-weight: bold;">
                    ⚠ Action Failed: You cannot deactivate your own account.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'unauthorized'): ?>
                <div style="background: #fce8e6; color: #b71c1c; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-size: 13px; font-weight: bold;">
                    ⚠ Permission Denied: You do not have the required privileges to perform this action.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'user_created'): ?>
                <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; margin-bottom: 20px; border: 1px solid #c8e6c9; font-size: 13px; font-weight: bold;">
                    ✔ New user account has been successfully provisioned.
                </div>
            <?php endif; ?>

            <div class="admin-grid" style="display: grid; grid-template-columns: 350px 1fr; gap: 30px;">
                
                <section>
                    <div class="section-header-bar">Create New Staff Account</div>
                    <div class="judicial-form">
                        <form action="save_user.php" method="POST">
                            <div class="form-group">
                                <label>FULL NAME</label>
                                <input type="text" name="staff_name" placeholder="e.g. Mawell Njane" required>
                            </div>
                            
                            <div class="form-group">
                                <label>SERVICE NUMBER (USERNAME)</label>
                                <input type="text" name="username" placeholder="e.g. C01-9988" required>
                            </div>
                            
                            <div class="form-group">
                                <label>PASSWORD</label>
                                <input type="password" name="password" placeholder="Set a temporary password" required>
                            </div>
                            
                            <div class="form-group">
                                <label>ASSIGNED SYSTEM ROLE</label>
                                <select name="role" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="Warden">Warden / Records Officer</option>
                                    <option value="Instructor">Vocational Instructor</option>
                                    <option value="Admin">Super Admin</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn-primary" style="width:100%; height: 45px; font-weight: bold; background: #333; color: white; border: none; cursor: pointer;">PROVISION ACCOUNT</button>
                        </form>
                    </div>
                </section>

                <section>
                    <div class="section-header-bar">Registered System Users</div>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service No.</th>
                                <th>System Role</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($staff_result && $staff_result->num_rows > 0): ?>
                                <?php while($row = $staff_result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo strtoupper($row['full_name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td>
                                        <?php 
                                            // Fix for the Undefined Key "role" error
                                            echo htmlspecialchars($row['role'] ?? 'Unassigned'); 
                                        ?>
                                    </td>
                                    <td style="text-align: center;"><span class="status-pill">ACTIVE</span></td>
                                    <td style="text-align: center;">
                                        <a href="deactivate_user.php?id=<?php echo $row['user_id']; ?>" class="btn-action btn-delete">DEACTIVATE</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; padding:30px; color:#888;">No registered staff found in the database.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </main>
    </div>
</body>
</html>