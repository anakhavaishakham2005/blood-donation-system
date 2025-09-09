<?php
// setup_smtp.php
// Complete SMTP setup and testing script

require_once 'includes/config.php';
require_once 'includes/smtp_config.php';
require_once 'includes/smtp_notification_service.php';

echo "<h2>🔧 SMTP Setup & Testing</h2>";

echo "<h3>📋 Step-by-Step SMTP Setup Guide</h3>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 1: Enable Gmail App Password</h4>";
echo "<ol>";
echo "<li>Go to your Google Account settings</li>";
echo "<li>Navigate to Security → 2-Step Verification</li>";
echo "<li>Enable 2-Step Verification if not already enabled</li>";
echo "<li>Go to Security → App passwords</li>";
echo "<li>Generate a new app password for 'Mail'</li>";
echo "<li>Copy the 16-character password (e.g., abcd efgh ijkl mnop)</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f3e5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 2: Update SMTP Configuration</h4>";
echo "<p>Edit <code>includes/smtp_config.php</code> and replace:</p>";
echo "<code>define('SMTP_PASSWORD', 'your_app_password_here');</code><br>";
echo "<p>With your actual Gmail app password:</p>";
echo "<code>define('SMTP_PASSWORD', 'abcd efgh ijkl mnop');</code>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 3: Test SMTP Connection</h4>";
echo "</div>";

// Test SMTP connection
echo "<h3>🔍 Testing SMTP Connection</h3>";

$smtpService = new SMTPNotificationService($conn);
$connection_test = $smtpService->testConnection();

if ($connection_test['success']) {
    echo "<p style='color: green;'>✅ " . $connection_test['message'] . "</p>";
} else {
    echo "<p style='color: red;'>❌ " . $connection_test['message'] . "</p>";
}

// Test email sending
echo "<h3>📧 Testing Email Sending</h3>";

$test_email = "anakhavaishakham2005@gmail.com";
$test_subject = "🧪 SMTP Test - Blood Bank System";
$test_body = "This is a test email from your Blood Bank System using SMTP.\n\n";
$test_body .= "If you receive this email, SMTP is working correctly!\n\n";
$test_body .= "Test Details:\n";
$test_body .= "Time: " . date('Y-m-d H:i:s') . "\n";
$test_body .= "SMTP Host: " . SMTP_HOST . "\n";
$test_body .= "SMTP Port: " . SMTP_PORT . "\n";
$test_body .= "From: " . SMTP_FROM_EMAIL . "\n\n";
$test_body .= "Best regards,\nBlood Bank Management System";

echo "<p>Attempting to send test email to: <strong>$test_email</strong></p>";

$email_sent = $smtpService->sendSMTPEmail($test_email, $test_subject, $test_body);

if ($email_sent) {
    echo "<p style='color: green;'>✅ Test email sent successfully!</p>";
    echo "<p>Check your inbox at <strong>$test_email</strong></p>";
} else {
    echo "<p style='color: red;'>❌ Test email failed to send</p>";
    echo "<p>This might be due to:</p>";
    echo "<ul>";
    echo "<li>Incorrect Gmail app password</li>";
    echo "<li>2-Step Verification not enabled</li>";
    echo "<li>SMTP settings not properly configured</li>";
    echo "</ul>";
}

// Show current configuration
echo "<h3>⚙️ Current SMTP Configuration</h3>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Setting</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Value</th>";
echo "</tr>";

$config = getSMTPConfig();
foreach ($config as $key => $value) {
    if ($key === 'password') {
        $value = str_repeat('*', strlen($value));
    }
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . strtoupper($key) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($value) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Alternative setup methods
echo "<h3>🔄 Alternative Setup Methods</h3>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Method 1: PHPMailer (Recommended for Production)</h4>";
echo "<p>Install PHPMailer for better email handling:</p>";
echo "<code>composer require phpmailer/phpmailer</code><br><br>";
echo "<a href='setup_phpmailer.php' class='btn btn-primary'>Setup PHPMailer</a>";
echo "</div>";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Method 2: Configure XAMPP Mail Server</h4>";
echo "<p>Configure XAMPP's built-in mail server:</p>";
echo "<ol>";
echo "<li>Edit <code>C:\\xampp\\php\\php.ini</code></li>";
echo "<li>Find [mail function] section</li>";
echo "<li>Set SMTP = smtp.gmail.com</li>";
echo "<li>Set smtp_port = 587</li>";
echo "<li>Set sendmail_from = your-email@gmail.com</li>";
echo "<li>Restart Apache</li>";
echo "</ol>";
echo "</div>";

// Troubleshooting
echo "<h3>🔧 Troubleshooting</h3>";
echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Common Issues & Solutions</h4>";
echo "<ul>";
echo "<li><strong>Authentication failed:</strong> Check Gmail app password</li>";
echo "<li><strong>Connection timeout:</strong> Check firewall settings</li>";
echo "<li><strong>SSL/TLS errors:</strong> Ensure port 587 is open</li>";
echo "<li><strong>Email not received:</strong> Check spam folder</li>";
echo "</ul>";
echo "</div>";

// Next steps
echo "<h3>🚀 Next Steps</h3>";
echo "<ol>";
echo "<li>✅ Complete Gmail app password setup</li>";
echo "<li>✅ Update SMTP configuration</li>";
echo "<li>✅ Test email sending</li>";
echo "<li>✅ Run notification system tests</li>";
echo "<li>✅ Test with sample data</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='test_email_functionality.php' class='btn btn-secondary'>Test Basic Email</a>";
echo "<a href='add_notification_sample_data.php' class='btn btn-primary'>Add Sample Data</a>";
echo "<a href='test_notification_system.php' class='btn btn-success'>Test Notifications</a>";
echo "</div>";

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?>
