<?php
// admin_create.php - run once, then delete
require_once __DIR__ . '/includes/config.php';

$pw = 'admin123'; // change directly here or later in DB
$email = 'admin@bloodbank.local';
$username = 'admin';

$stmt = $conn->prepare("UPDATE admins SET password = ?, username = ? WHERE email = ?");
$stmt->bind_param('sss', $pw, $username, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Admin password updated. Email: $email, Password: $pw";
} else {
    // maybe the admin row didn't exist; insert
    $ins = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $ins->bind_param('sss', $username, $email, $pw);
    $ins->execute();
    echo "Admin created. Email: $email, Password: $pw";
    $ins->close();
}

$stmt->close();
?>
