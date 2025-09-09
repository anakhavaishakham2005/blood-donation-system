<?php
// alternative_email_setup.php
// Alternative email setup methods without Gmail App Password

echo "<h2>📧 Alternative Email Setup Methods</h2>";

echo "<h3>🔍 Why App Passwords Aren't Visible</h3>";
echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<p><strong>Common reasons:</strong></p>";
echo "<ul>";
echo "<li>2-Step Verification not enabled</li>";
echo "<li>Google Workspace account (different interface)</li>";
echo "<li>Account security settings restricted</li>";
echo "<li>New Google account interface</li>";
echo "</ul>";
echo "</div>";

echo "<h3>🚀 Alternative Solutions</h3>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Option 1: Enable 2-Step Verification First</h4>";
echo "<ol>";
echo "<li>Go to: <a href='https://myaccount.google.com/security' target='_blank'>Google Security Settings</a></li>";
echo "<li>Click <strong>'2-Step Verification'</strong></li>";
echo "<li>Follow the setup process (phone number required)</li>";
echo "<li>After enabling, go back to Security</li>";
echo "<li>Look for <strong>'App passwords'</strong> option</li>";
echo "</ol>";
echo "<p><strong>Note:</strong> App passwords only appear AFTER 2-Step Verification is enabled</p>";
echo "</div>";

echo "<div style='background: #f3e5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Option 2: Use Different Email Provider</h4>";
echo "<p>Use other email providers that are easier to configure:</p>";
echo "<ul>";
echo "<li><strong>Outlook/Hotmail:</strong> Easier SMTP setup</li>";
echo "<li><strong>Yahoo Mail:</strong> App passwords available</li>";
echo "<li><strong>ProtonMail:</strong> Professional email service</li>";
echo "<li><strong>SendGrid:</strong> Email service provider</li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Option 3: Use XAMPP Mail Server (Local Testing)</h4>";
echo "<p>Configure XAMPP's built-in mail server for local testing:</p>";
echo "<ol>";
echo "<li>Edit <code>C:\\xampp\\php\\php.ini</code></li>";
echo "<li>Find [mail function] section</li>";
echo "<li>Set SMTP = localhost</li>";
echo "<li>Set smtp_port = 25</li>";
echo "<li>Set sendmail_from = your-email@localhost</li>";
echo "<li>Restart Apache</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Option 4: Use Database-Only Mode</h4>";
echo "<p>Test the notification system without sending actual emails:</p>";
echo "<ul>";
echo "<li>All notifications are logged in database</li>";
echo "<li>View notifications in admin panel</li>";
echo "<li>Test the complete workflow</li>";
echo "<li>Configure email later for production</li>";
echo "</ul>";
echo "</div>";

echo "<h3>🔧 Quick Setup - Outlook/Hotmail</h3>";
echo "<p>If you have an Outlook/Hotmail account, it's easier to set up:</p>";

// Create Outlook configuration
$outlook_config = '<?php
// includes/outlook_smtp_config.php
// Outlook/Hotmail SMTP configuration

class OutlookSMTPConfig {
    public static function getConfig() {
        return [
            "host" => "smtp-mail.outlook.com",
            "port" => 587,
            "username" => "your-email@outlook.com", // Replace with your Outlook email
            "password" => "your-password", // Your Outlook password
            "from_email" => "your-email@outlook.com",
            "from_name" => "Blood Bank System",
            "encryption" => "tls"
        ];
    }
}
';

file_put_contents('includes/outlook_smtp_config.php', $outlook_config);
echo "<p style='color: green;'>✅ Created Outlook SMTP configuration</p>";

echo "<h3>🧪 Test Without Email (Database Only)</h3>";
echo "<p>You can test the entire notification system without sending emails:</p>";
echo "<ol>";
echo "<li>✅ Add sample data</li>";
echo "<li>✅ Create blood requests</li>";
echo "<li>✅ Test donor matching</li>";
echo "<li>✅ View notifications in database</li>";
echo "<li>✅ Check admin panel</li>";
echo "</ol>";

echo "<h3>📊 What Works Without Email</h3>";
echo "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Feature</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Status</th>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Donor Matching</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Works</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Request Processing</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Works</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Database Logging</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Works</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Admin Panel</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Works</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Email Sending</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>⚠️ Needs Configuration</td>";
echo "</tr>";
echo "</table>";

echo "<h3>🎯 Recommended Next Steps</h3>";
echo "<ol>";
echo "<li><strong>Test the system first:</strong> Add sample data and test notifications</li>";
echo "<li><strong>Enable 2-Step Verification:</strong> Try the Gmail app password again</li>";
echo "<li><strong>Use alternative email:</strong> Try Outlook or Yahoo</li>";
echo "<li><strong>Configure for production:</strong> Set up proper email later</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='add_notification_sample_data.php' class='btn btn-primary'>Test System (No Email)</a>";
echo "<a href='test_notification_system.php' class='btn btn-success'>Test Notifications</a>";
echo "<a href='setup_outlook_email.php' class='btn btn-info'>Setup Outlook Email</a>";
echo "<a href='index.php' class='btn btn-secondary'>Back to Home</a>";
echo "</div>";

echo "<hr>";
echo "<p><strong>💡 Tip:</strong> You can test the entire blood donation system without email. All notifications are logged in the database, so you can see the complete workflow!</p>";
?>
