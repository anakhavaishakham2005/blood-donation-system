<?php
// includes/smtp_config.php
// Gmail SMTP configuration for Blood Bank System

// Gmail SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'anakhavaishakham2005@gmail.com'); // Your Gmail
define('SMTP_PASSWORD', 'your_app_password_here'); // Gmail App Password
define('SMTP_FROM_EMAIL', 'anakhavaishakham2005@gmail.com');
define('SMTP_FROM_NAME', 'Blood Bank System');

// Enable SMTP debugging (set to 0 for production)
define('SMTP_DEBUG', 1);

// SMTP Configuration for php.ini
$smtp_config = [
    'host' => SMTP_HOST,
    'port' => SMTP_PORT,
    'username' => SMTP_USERNAME,
    'password' => SMTP_PASSWORD,
    'from_email' => SMTP_FROM_EMAIL,
    'from_name' => SMTP_FROM_NAME
];

// Function to get SMTP config
function getSMTPConfig() {
    return [
        'host' => SMTP_HOST,
        'port' => SMTP_PORT,
        'username' => SMTP_USERNAME,
        'password' => SMTP_PASSWORD,
        'from_email' => SMTP_FROM_EMAIL,
        'from_name' => SMTP_FROM_NAME,
        'debug' => SMTP_DEBUG
    ];
}

// Function to test SMTP connection
function testSMTPConnection() {
    $config = getSMTPConfig();
    
    // Test connection using fsockopen
    $connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 10);
    
    if ($connection) {
        fclose($connection);
        return ['success' => true, 'message' => 'SMTP server is reachable'];
    } else {
        return ['success' => false, 'message' => "Cannot connect to SMTP server: $errstr ($errno)"];
    }
}
?>
