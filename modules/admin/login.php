<?php
session_start();
require_once __DIR__ . '/../../includes/config.php'; 

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Debug: Check if database connection is working
    if ($conn->connect_error) {
        $error = "Database connection failed: " . $conn->connect_error;
    } else {
        // Use prepared statement and password verification
        $stmt = $conn->prepare("SELECT admin_id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($admin_id, $db_username, $hash);
        
        if ($stmt->fetch()) {
            // Check if password is hashed or plain text (for backward compatibility)
            if (password_verify($password, $hash) || $password === $hash) {
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['admin_name'] = $db_username;
                header("Location:dashboard.php");
                exit();
            } else {
                $error = "Invalid Username or Password!";
            }
        } else {
            $error = "Invalid Username or Password!";
        }
        $stmt->close();
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
