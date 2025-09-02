<?php
// includes/config.php
session_start();

$DB_HOST = "sql.freedb.tech";       // FreeDB host
$DB_PORT = 3306;                    // FreeDB port
$DB_USER = "freedb_anakha";         // FreeDB username
$DB_PASS = 'CVCJ7Yv2%$msdeU';       // FreeDB password (single quotes to avoid PHP parsing $msdeU)
$DB_NAME = "freedb_blood_bank_db";  // FreeDB database name

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

mysqli_set_charset($conn, 'utf8mb4');
?>
