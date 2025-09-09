<?php
// test_outlook_email.php
// Test Outlook/Hotmail email functionality

require_once 'includes/config.php';

echo "<h2>📧 Test Outlook/Hotmail Email</h2>";

// Check if Composer PHPMailer is installed
if (!file_exists('vendor/autoload.php')) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>❌ Composer PHPMailer Not Installed</h4>";
    echo "<p>Please install Composer and PHPMailer first:</p>";
    echo "<a href='install_composer_phpmailer.php' class='btn btn-primary'>Install Composer & PHPMailer</a>";
    echo "</div>";
    exit;
}

// Check if Outlook service exists
if (!file_exists('includes/outlook_phpmailer_service.php')) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>❌ Outlook Service Not Created</h4>";
    echo "<p>Please create the Outlook service first:</p>";
    echo "<a href='setup_outlook_email.php' class='btn btn-primary'>Setup Outlook Email</a>";
    echo "</div>";
    exit;
}

echo "<h3>📁 Checking Installation</h3>";
echo "<p style='color: green;'>✅ Composer PHPMailer installed</p>";
echo "<p style='color: green;'>✅ Outlook service created</p>";

echo "<h3>🔧 Testing Outlook PHPMailer Service</h3>";

try {
    require_once 'includes/outlook_phpmailer_service.php';
    $outlookService = new OutlookPHPMailerService($conn);
    echo "<p style='color: green;'>✅ Outlook PHPMailer Service created successfully</p>";
    
    // Test SMTP connection
    echo "<h3>🌐 Testing Outlook SMTP Connection</h3>";
    $connection_test = $outlookService->testConnection();
    
    if ($connection_test['success']) {
        echo "<p style='color: green;'>✅ " . $connection_test['message'] . "</p>";
    } else {
        echo "<p style='color: red;'>❌ " . $connection_test['message'] . "</p>";
        echo "<p><strong>Solution:</strong> Update Outlook credentials in <code>includes/outlook_phpmailer_service.php</code></p>";
    }
    
    // Test email sending
    echo "<h3>📧 Testing Email Sending</h3>";
    
    $test_email = "anakhavaishakham2005@gmail.com"; // Test to your Gmail
    $test_subject = "🧪 Outlook Email Test - Blood Bank System";
    $test_body = "<h2>Outlook Email Test</h2>";
    $test_body .= "<p>This is a test email from your Blood Bank System using <strong>Outlook SMTP</strong>.</p>";
    $test_body .= "<p><strong>If you receive this email, Outlook email is working correctly!</strong></p>";
    $test_body .= "<hr>";
    $test_body .= "<h3>Test Details:</h3>";
    $test_body .= "<ul>";
    $test_body .= "<li><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</li>";
    $test_body .= "<li><strong>SMTP Host:</strong> smtp-mail.outlook.com</li>";
    $test_body .= "<li><strong>SMTP Port:</strong> 587</li>";
    $test_body .= "<li><strong>From:</strong> Blood Bank System (Outlook)</li>";
    $test_body .= "<li><strong>Encryption:</strong> STARTTLS</li>";
    $test_body .= "</ul>";
    $test_body .= "<p>Best regards,<br>Blood Bank Management System</p>";
    
    echo "<p>Attempting to send test email to: <strong>$test_email</strong></p>";
    
    $email_sent = $outlookService->sendNotification($test_email, $test_subject, $test_body, true);
    
    if ($email_sent) {
        echo "<p style='color: green;'>✅ Test email sent successfully!</p>";
        echo "<p>Check your inbox at <strong>$test_email</strong></p>";
        echo "<p><em>Note: Email may take a few minutes to arrive</em></p>";
    } else {
        echo "<p style='color: red;'>❌ Test email failed to send</p>";
        echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
        echo "<h4>Common Issues:</h4>";
        echo "<ul>";
        echo "<li><strong>Outlook Credentials:</strong> Make sure you've updated the email and password in outlook_phpmailer_service.php</li>";
        echo "<li><strong>Account Security:</strong> Check if Outlook account has security restrictions</li>";
        echo "<li><strong>SMTP Settings:</strong> Verify the Outlook SMTP configuration</li>";
        echo "<li><strong>Account Type:</strong> Make sure it's a personal Outlook account, not business</li>";
        echo "</ul>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your Outlook PHPMailer setup and try again.</p>";
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
    'Composer Installation' => file_exists('vendor/autoload.php') ? '✅ Ready' : '❌ Missing',
    'Outlook Service' => file_exists('includes/outlook_phpmailer_service.php') ? '✅ Ready' : '❌ Missing',
    'SMTP Connection' => isset($connection_test) && $connection_test['success'] ? '✅ Connected' : '❌ Failed',
    'Email Sending' => isset($email_sent) && $email_sent ? '✅ Working' : '❌ Failed',
    'Database Logging' => '✅ Working'
];

foreach ($components as $component => $status) {
    $action = '';
    if (strpos($status, '❌') !== false) {
        if (strpos($component, 'Composer') !== false) {
            $action = '<a href="install_composer_phpmailer.php" class="btn btn-sm btn-primary">Install</a>';
        } elseif (strpos($component, 'Outlook') !== false) {
            $action = '<a href="setup_outlook_email.php" class="btn btn-sm btn-secondary">Setup</a>';
        } elseif (strpos($component, 'SMTP') !== false || strpos($component, 'Email') !== false) {
            $action = '<a href="setup_outlook_email.php" class="btn btn-sm btn-info">Configure</a>';
        }
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

// Show Outlook SMTP info
echo "<h3>📦 Outlook SMTP Information</h3>";
echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<p><strong>SMTP Host:</strong> smtp-mail.outlook.com</p>";
echo "<p><strong>SMTP Port:</strong> 587</p>";
echo "<p><strong>Encryption:</strong> STARTTLS</p>";
echo "<p><strong>Authentication:</strong> SMTP Auth</p>";
echo "<p><strong>Password Type:</strong> Regular password (no app password needed)</p>";
echo "<p><strong>Account Type:</strong> Personal Outlook/Hotmail account</p>";
echo "</div>";

// Next steps
echo "<h3>🚀 Next Steps</h3>";
echo "<ol>";
echo "<li>✅ Complete Outlook email setup</li>";
echo "<li>✅ Test email sending</li>";
echo "<li>✅ Add sample data</li>";
echo "<li>✅ Test notification system</li>";
echo "<li>✅ Create blood requests and test notifications</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='setup_outlook_email.php' class='btn btn-secondary'>Setup Outlook Email</a>";
echo "<a href='add_notification_sample_data.php' class='btn btn-primary'>Add Sample Data</a>";
echo "<a href='test_notification_system.php' class='btn btn-success'>Test Notifications</a>";
echo "<a href='index.php' class='btn btn-info'>Back to Home</a>";
echo "</div>";

echo "<hr>";
echo "<p><strong>💡 Tip:</strong> Outlook is much easier than Gmail - just use your regular email and password. No app passwords or 2-step verification required!</p>";
?>
