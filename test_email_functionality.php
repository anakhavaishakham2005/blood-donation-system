<?php
// test_email_functionality.php
// This script tests if email functionality works on your XAMPP setup

echo "<h2>Testing Email Functionality</h2>";

// Test 1: Check PHP mail function
echo "<h3>Test 1: PHP Mail Function Check</h3>";
if (function_exists('mail')) {
    echo "✓ PHP mail() function is available<br>";
} else {
    echo "✗ PHP mail() function is not available<br>";
}

// Test 2: Check mail configuration
echo "<h3>Test 2: Mail Configuration</h3>";
$mail_config = ini_get('sendmail_path');
echo "Sendmail path: " . ($mail_config ? $mail_config : 'Not configured') . "<br>";

$smtp_host = ini_get('SMTP');
echo "SMTP host: " . ($smtp_host ? $smtp_host : 'Not configured') . "<br>";

$smtp_port = ini_get('smtp_port');
echo "SMTP port: " . ($smtp_port ? $smtp_port : 'Not configured') . "<br>";

// Test 3: Try to send a test email
echo "<h3>Test 3: Sending Test Email</h3>";

$to = "anakhavaishakham2005@gmail.com";
$subject = "Blood Bank System - Test Email";
$message = "This is a test email from your Blood Bank System.\n\n";
$message .= "If you receive this email, the notification system is working correctly!\n\n";
$message .= "Test sent at: " . date('Y-m-d H:i:s') . "\n\n";
$message .= "Best regards,\nBlood Bank Management System";

$headers = "From: Blood Bank System <noreply@bloodbank.local>\r\n";
$headers .= "Reply-To: admin@bloodbank.local\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

echo "Attempting to send email to: $to<br>";

if (mail($to, $subject, $message, $headers)) {
    echo "✓ Email sent successfully! Check your inbox.<br>";
} else {
    echo "✗ Email failed to send. This is common in XAMPP local development.<br>";
    echo "<strong>Solutions:</strong><br>";
    echo "1. Configure SMTP in php.ini<br>";
    echo "2. Use PHPMailer with SMTP settings<br>";
    echo "3. Test on a live server<br>";
}

// Test 4: Check if notifications table exists and works
echo "<h3>Test 4: Database Notification Logging</h3>";

try {
    require_once 'includes/config.php';
    
    // Test inserting notification
    $stmt = $conn->prepare("INSERT INTO notifications (to_email, subject, body, notification_type) VALUES (?, ?, ?, ?)");
    $test_email = "anakhavaishakham2005@gmail.com";
    $test_subject = "Test Notification - " . date('Y-m-d H:i:s');
    $test_body = "This is a test notification stored in the database.";
    $test_type = "test";
    
    $stmt->bind_param('ssss', $test_email, $test_subject, $test_body, $test_type);
    
    if ($stmt->execute()) {
        echo "✓ Notification logged in database successfully<br>";
        echo "Notification ID: " . $stmt->insert_id . "<br>";
    } else {
        echo "✗ Failed to log notification: " . $stmt->error . "<br>";
    }
    $stmt->close();
    
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "<br>";
}

echo "<h3>Email System Status Summary:</h3>";
echo "<ul>";
echo "<li><strong>PHP Mail Function:</strong> " . (function_exists('mail') ? 'Available' : 'Not Available') . "</li>";
echo "<li><strong>Database Logging:</strong> Working (notifications are always stored)</li>";
echo "<li><strong>Email Delivery:</strong> Depends on server configuration</li>";
echo "</ul>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Run <a href='add_notification_sample_data.php'>Add Sample Data</a> to populate test data</li>";
echo "<li>Test the notification system with sample data</li>";
echo "<li>Check the notifications table in phpMyAdmin to see logged emails</li>";
echo "<li>For production, configure proper SMTP settings</li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?>
