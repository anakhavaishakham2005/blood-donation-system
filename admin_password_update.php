<?php
// admin_password_update.php - Run this once to hash the admin password
require_once __DIR__ . '/includes/config.php';

// Get the current admin record
$stmt = $conn->prepare("SELECT admin_id, username, password FROM admins WHERE username = 'admin'");
$stmt->execute();
$stmt->bind_result($admin_id, $username, $current_password);
$stmt->fetch();
$stmt->close();

if ($current_password && !password_verify('admin123', $current_password)) {
    // Password is not hashed, update it
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $update_stmt = $conn->prepare("UPDATE admins SET password = ? WHERE admin_id = ?");
    $update_stmt->bind_param('si', $hashed_password, $admin_id);
    
    if ($update_stmt->execute()) {
        echo "Admin password has been successfully hashed!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "You can now login securely.<br>";
    } else {
        echo "Error updating password: " . $update_stmt->error;
    }
    $update_stmt->close();
} else {
    echo "Admin password is already hashed or admin doesn't exist.";
}
?>
