<?php
// includes/config.php
// session_start();
$DB_HOST = "localhost";
$DB_USER = "root"; // change if needed
$DB_PASS = "";     // change if needed
$DB_NAME = "blood_bank_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
mysqli_set_charset($conn, 'utf8mb4');
?>
