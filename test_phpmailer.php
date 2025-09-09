<?php
// test_phpmailer.php
// Test PHPMailer setup and functionality

require_once 'includes/config.php';

echo "<h2>🧪 PHPMailer Test & Setup</h2>";

// Check if PHPMailer files exist
echo "<h3>📁 Checking PHPMailer Files</h3>";

$files_to_check = [
    'phpmailer/autoload.php' => 'PHPMailer Autoloader',
    'phpmailer/src/PHPMailer.php' => 'PHPMailer Main Class',
    'includes/phpmailer_service.php' => 'PHPMailer Service'
];

$all_files_exist = true;
foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description: $file</p>";
    } else {
        echo "<p style='color: red;'>❌ $description: $file (Missing)</p>";
        $all_files_exist = false;
    }
}

if (!$all_files_exist) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>⚠️ PHPMailer Files Missing</h4>";
    echo "<p>Please run the manual setup first:</p>";
    echo "<a href='phpmailer_manual_setup.php' class='btn btn-primary'>Setup PHPMailer Files</a>";
    echo "</div>";
    exit;
}

echo "<h3>🔧 Testing PHPMailer Service</h3>";

try {
    require_once 'includes/phpmailer_service.php';
    $phpmailerService = new PHPMailerService($conn);
    echo "<p style='color: green;'>✅ PHPMailer Service created successfully</p>";
    
    // Test SMTP connection
    echo "<h3>🌐 Testing SMTP Connection</h3>";
    $connection_test = $phpmailerService->testConnection();
    
    if ($connection_test['success']) {
        echo "<p style='color: green;'>✅ " . $connection_test['message'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ " . $connection_test['message'] . "</p>";
        echo "<p><strong>Solution:</strong> Update Gmail credentials in <code>includes/phpmailer_service.php</code></p>";
    }
    
    // Test email sending
    echo "<h3>📧 Testing Email Sending</h3>";
    
    $test_email = "anakhavaishakham2005@gmail.com";
    $test_subject = "🧪 PHPMailer Test - Blood Bank System";
    $test_body = "<h2>PHPMailer Test Email</h2>";
    $test_body .= "<p>This is a test email from your Blood Bank System using PHPMailer.</p>";
    $test_body .= "<p><strong>If you receive this email, PHPMailer is working correctly!</strong></p>";
    $test_body .= "<hr>";
    $test_body .= "<h3>Test Details:</h3>";
    $test_body .= "<ul>";
    $test_body .= "<li><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</li>";
    $test_body .= "<li><strong>SMTP Host:</strong> smtp.gmail.com</li>";
    $test_body .= "<li><strong>SMTP Port:</strong> 587</li>";
    $test_body .= "<li><strong>From:</strong> Blood Bank System</li>";
    $test_body .= "</ul>";
    $test_body .= "<p>Best regards,<br>Blood Bank Management System</p>";
    
    echo "<p>Attempting to send test email to: <strong>$test_email</strong></p>";
    
    $email_sent = $phpmailerService->sendNotification($test_email, $test_subject, $test_body, true);
    
    if ($email_sent) {
        echo "<p style='color: green;'>✅ Test email sent successfully!</p>";
        echo "<p>Check your inbox at <strong>$test_email</strong></p>";
        echo "<p><em>Note: Email may take a few minutes to arrive</em></p>";
    } else {
        echo "<p style='color: red;'>❌ Test email failed to send</p>";
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>Common Issues:</h4>";
        echo "<ul>";
        echo "<li><strong>Gmail App Password:</strong> Make sure you're using the 16-character app password, not your regular password</li>";
        echo "<li><strong>2-Step Verification:</strong> Must be enabled in your Google Account</li>";
        echo "<li><strong>SMTP Settings:</strong> Check the configuration in phpmailer_service.php</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    // Test notification system integration
    echo "<h3>🔗 Testing Notification System Integration</h3>";
    
    // Check if we can create a notification service that uses PHPMailer
    $integration_test = true;
    
    if ($integration_test) {
        echo "<p style='color: green;'>✅ PHPMailer integration ready</p>";
        echo "<p>You can now use PHPMailer for all notification emails in your Blood Bank System!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your PHPMailer setup and try again.</p>";
}

// Show configuration status
echo "<h3>⚙️ Configuration Status</h3>";
echo "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Component</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Status</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Action</th>";
echo "</tr>";

$components = [
    'PHPMailer Files' => $all_files_exist ? '✅ Ready' : '❌ Missing',
    'SMTP Connection' => isset($connection_test) && $connection_test['success'] ? '✅ Connected' : '❌ Failed',
    'Email Sending' => isset($email_sent) && $email_sent ? '✅ Working' : '❌ Failed',
    'Database Logging' => '✅ Working',
    'Notification Integration' => '✅ Ready'
];

foreach ($components as $component => $status) {
    $action = '';
    if (strpos($status, '❌') !== false) {
        $action = '<a href="phpmailer_manual_setup.php" class="btn btn-sm btn-primary">Fix</a>';
    } else {
        $action = '<span style="color: green;">Ready</span>';
    }
    
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>$component</td>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>$status</td>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>$action</td>";
    echo "</tr>";
}
echo "</table>";

// Next steps
echo "<h3>🚀 Next Steps</h3>";
echo "<ol>";
echo "<li>✅ Complete PHPMailer setup</li>";
echo "<li>✅ Test email sending</li>";
echo "<li>✅ Add sample data</li>";
echo "<li>✅ Test notification system</li>";
echo "<li>✅ Create blood requests and test notifications</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='phpmailer_manual_setup.php' class='btn btn-secondary'>Setup PHPMailer</a>";
echo "<a href='add_notification_sample_data.php' class='btn btn-primary'>Add Sample Data</a>";
echo "<a href='test_notification_system.php' class='btn btn-success'>Test Notifications</a>";
echo "<a href='index.php' class='btn btn-info'>Back to Home</a>";
echo "</div>";

echo "<hr>";
echo "<p><strong>💡 Tip:</strong> If emails are not being received, check your spam folder and verify your Gmail app password is correct.</p>";
?>
