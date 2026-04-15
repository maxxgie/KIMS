<?php
session_start();
include 'db_connect.php';

$error = "";

// Handle the Login Post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize username to prevent SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Do not escape; we need the raw string for verification

    // 1. Fetch the user record by username only
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        /**
         * 2. Verify Password
         * We use password_verify() because your Superadmin likely uses 
         * password_hash() when creating new users.
         */
        if (password_verify($password, $user['password'])) {
            
            // Create the Session "Badge"
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Send to Dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Log the attempt or handle generic error
            $error = "Invalid Service Number or Password.";
        }
    } else {
        $error = "Invalid Service Number or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIMS — Secure Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { 
            background: #eef2f7; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0; 
            font-family: 'Segoe UI', sans-serif; 
        }
        .login-card { 
            background: white; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 400px; 
            text-align: center; 
        }
        .login-card h1 { color: #1a73e8; margin-bottom: 5px; font-size: 24px; }
        .login-card p { color: #5f6368; margin-bottom: 30px; font-size: 14px; }
        
        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            font-size: 11px; 
            font-weight: bold; 
            margin-bottom: 5px; 
            color: #3c4043; 
            letter-spacing: 1px; 
        }
        .form-group input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #dadce0; 
            border-radius: 4px; 
            box-sizing: border-box; 
            font-size: 14px; 
        }
        .form-group input:focus { 
            outline: none; 
            border-color: #1a73e8; 
            box-shadow: 0 0 0 2px rgba(26,115,232,0.2); 
        }
        
        .btn-login { 
            width: 100%; 
            padding: 12px; 
            background: #1a73e8; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 16px; 
            transition: background 0.3s; 
        }
        .btn-login:hover { background: #1765cc; }
        
        .alert { 
            padding: 12px; 
            background: #fdecea; 
            color: #d32f2f; 
            border: 1px solid #f5c6cb; 
            border-radius: 4px; 
            margin-bottom: 20px; 
            font-size: 13px; 
            text-align: left; 
        }
        .success { 
            padding: 12px; 
            background: #e6fffa; 
            color: #2c7a7b; 
            border: 1px solid #b2f5ea; 
            border-radius: 4px; 
            margin-bottom: 20px; 
            font-size: 13px; 
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>KIMS</h1>
        <p>King'ong'o Inmate Management System</p>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'logged_out'): ?>
            <div class="success">Session ended. Securely logged out.</div>
        <?php endif; ?>

        <?php if($error != ""): ?>
            <div class="alert"><strong>Access Denied:</strong> <?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST"> 
            <div class="form-group">
                <label>SERVICE NUMBER / USERNAME</label>
                <input type="text" name="username" placeholder="e.g. admin" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">SIGN IN TO COMMAND</button>
        </form>
        
        <div style="margin-top: 25px; font-size: 10px; color: #9aa0a6; line-height: 1.5;">
            OFFICIAL USE ONLY. Unauthorized access is monitored and prohibited under the Kenya Information and Communications Act.
        </div>
    </div>
</body>
</html>