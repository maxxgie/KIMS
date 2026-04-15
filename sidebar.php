<?php
// This helps to highlight the active page in the navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <ul>
        <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" onclick="location.href='dashboard.php'">Dashboard</li>
                
        <li class="<?php echo ($current_page == 'registration.php') ? 'active' : ''; ?>" onclick="location.href='registration.php'">New Inmate</li>
        
        <li class="<?php echo ($current_page == 'discharge_audit.php') ? 'active' : ''; ?>" onclick="location.href='discharge_audit.php'">Discharge Audit</li>
        
        <li class="<?php echo ($current_page == 'housing.php') ? 'active' : ''; ?>" onclick="location.href='housing.php'">Housing Audit</li>
        
        <li class="<?php echo ($current_page == 'judicial.php') ? 'active' : ''; ?>" onclick="location.href='judicial.php'">Judicial Records</li>
        
        <li class="<?php echo ($current_page == 'kazi.php' || $current_page == 'vocational.php') ? 'active' : ''; ?>" onclick="location.href='kazi.php'">Vocational Logs</li>
        
        <li class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>" onclick="location.href='reports.php'">Reports</li>

         <li class="<?php echo ($current_page == 'alerts.php') ? 'active' : ''; ?>" onclick="location.href='alerts.php'">System Alerts</li>
        
        <li class="<?php echo ($current_page == 'admin.php') ? 'active' : ''; ?>" onclick="location.href='admin.php'">System Admin</li>
    </ul>
</aside>