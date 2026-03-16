<?php
include 'db_connect.php';

// Fetch all staff/users
$staff_sql = "SELECT * FROM users ORDER BY created_at DESC";
$staff_result = $conn->query($staff_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KIMS — System Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="top-nav">
        <div class="logo">KIMS — System Administration</div>
        <div class="user-info">Super Admin | <a href="dashboard.php">Dashboard</a></div>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <ul>
                <li onclick="location.href='dashboard.php'">Dashboard</li>
                <li onclick="location.href='registration.php'">Inmate Registration</li>
                <li onclick="location.href='judicial.php'">Judicial Tracking</li>
                <li onclick="location.href='kazi.php'">Kazi na Masomo</li>
                <li onclick="location.href='housing.php'">Housing / Cell Block</li>
                <li onclick="location.href='reports.php'">Reports</li>
                <li class="active">System Admin</li>
            </ul>
        </aside>

        <main class="content">
            <h2>User & Staff Management</h2>
            <hr>

            <div class="admin-grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
                <section class="admin-form" style="background: white; padding: 20px; border: 1px solid #ddd;">
                    <h3>Create New Staff Account</h3>
                    <form action="save_user.php" method="POST">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="staff_name" required style="width:100%; padding:8px; margin-bottom:10px;">
                        </div>
                        <div class="form-group">
                            <label>Service Number (Username)</label>
                            <input type="text" name="username" required style="width:100%; padding:8px; margin-bottom:10px;">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" style="width:100%; padding:8px; margin-bottom:10px;">
                                <option value="Warden">Warden / Records</option>
                                <option value="Instructor">Vocational Instructor</option>
                                <option value="Admin">Super Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary" style="width:100%;">Create Account</button>
                    </form>
                </section>

                <section class="staff-list">
                    <h3>Registered System Users</h3>
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Service No.</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $staff_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['full_name']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['role']; ?></td>
                                <td><span style="color: green;">● Active</span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </section>
            </div>
        </main>
    </div>
</body>
</html>