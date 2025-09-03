<?php
session_start();
require_once __DIR__ . '/../../includes/config.php'; 

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Debug: Check if database connection is working
    if ($conn->connect_error) {
        $error = "Database connection failed: " . $conn->connect_error;
    } else {
        // Plain password check (no MD5)
        $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            $error = "Query error: " . mysqli_error($conn);
        } elseif (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['username'];
            header("Location:dashboard.php");
            exit();
        } else {
            $error = "Invalid Username or Password! (Found " . mysqli_num_rows($result) . " matching records)";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<div class="login-container">
    <h2>Admin Login</h2>
    <?php if($error) { echo "<p class='error'>$error</p>"; } ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
