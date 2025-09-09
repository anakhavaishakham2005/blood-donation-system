<?php
// configure_outlook_credentials.php
// Helper script to configure Outlook credentials

echo "<h2>⚙️ Configure Outlook Email Credentials</h2>";

echo "<h3>📧 Current Configuration Status</h3>";

// Read the current file
$file_content = file_get_contents('includes/outlook_phpmailer_service.php');

// Check if still has placeholder values
if (strpos($file_content, 'your-email@outlook.com') !== false) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>❌ Configuration Not Updated</h4>";
    echo "<p>Your Outlook PHPMailer service still has placeholder values:</p>";
    echo "<ul>";
    echo "<li><code>your-email@outlook.com</code> - needs your actual Outlook email</li>";
    echo "<li><code>your-password</code> - needs your actual Outlook password</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>✅ Configuration Updated</h4>";
    echo "<p>Your Outlook credentials appear to be configured!</p>";
    echo "</div>";
}

echo "<h3>🔧 How to Update Configuration</h3>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Method 1: Edit File Directly</h4>";
echo "<ol>";
echo "<li>Open: <code>includes/outlook_phpmailer_service.php</code></li>";
echo "<li>Find line 27: <code>\$this->mail->setUsername(\"your-email@outlook.com\");</code></li>";
echo "<li>Replace <code>your-email@outlook.com</code> with your actual Outlook email</li>";
echo "<li>Find line 28: <code>\$this->mail->setPassword(\"your-password\");</code></li>";
echo "<li>Replace <code>your-password</code> with your actual Outlook password</li>";
echo "<li>Find line 33: <code>\$this->mail->setFrom(\"your-email@outlook.com\", \"Blood Bank System\");</code></li>";
echo "<li>Replace <code>your-email@outlook.com</code> with your actual Outlook email</li>";
echo "<li>Find line 34: <code>\$this->mail->addReplyTo(\"your-email@outlook.com\", \"Blood Bank System\");</code></li>";
echo "<li>Replace <code>your-email@outlook.com</code> with your actual Outlook email</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f3e5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Method 2: Use This Form</h4>";
echo "<p>Enter your Outlook credentials below:</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $outlook_email = $_POST['outlook_email'] ?? '';
    $outlook_password = $_POST['outlook_password'] ?? '';
    
    if (!empty($outlook_email) && !empty($outlook_password)) {
        // Update the file
        $updated_content = str_replace('your-email@outlook.com', $outlook_email, $file_content);
        $updated_content = str_replace('your-password', $outlook_password, $updated_content);
        
        if (file_put_contents('includes/outlook_phpmailer_service.php', $updated_content)) {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<p style='color: green;'>✅ Outlook credentials updated successfully!</p>";
            echo "<p>Email: <strong>$outlook_email</strong></p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<p style='color: red;'>❌ Failed to update credentials. Please edit the file manually.</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<p style='color: #856404;'>⚠️ Please enter both email and password.</p>";
        echo "</div>";
    }
}

echo "<form method='POST' style='background: white; padding: 20px; border-radius: 8px; border: 1px solid #ddd;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='outlook_email' style='display: block; margin-bottom: 5px; font-weight: bold;'>Outlook Email:</label>";
echo "<input type='email' id='outlook_email' name='outlook_email' placeholder='your-email@outlook.com' style='width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;' required>";
echo "</div>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='outlook_password' style='display: block; margin-bottom: 5px; font-weight: bold;'>Outlook Password:</label>";
echo "<input type='password' id='outlook_password' name='outlook_password' placeholder='Your Outlook password' style='width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;' required>";
echo "</div>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Update Credentials</button>";
echo "</form>";
echo "</div>";

echo "<h3>📊 What You Need</h3>";
echo "<ul>";
echo "<li><strong>Outlook Email:</strong> Your @outlook.com or @hotmail.com email address</li>";
echo "<li><strong>Outlook Password:</strong> Your regular Outlook password (no app password needed)</li>";
echo "</ul>";

echo "<h3>🧪 Test After Configuration</h3>";
echo "<p>After updating your credentials, test the email:</p>";
echo "<a href='test_outlook_email.php' class='btn btn-primary'>Test Outlook Email</a>";

echo "<h3>📋 Example Configuration</h3>";
echo "<p>Your configuration should look like this:</p>";
echo "<code style='background: #f8f9fa; padding: 15px; display: block; border-radius: 4px;'>";
echo '$this->mail->setUsername("john.doe@outlook.com");<br>';
echo '$this->mail->setPassword("mypassword123");<br>';
echo '$this->mail->setFrom("john.doe@outlook.com", "Blood Bank System");<br>';
echo '$this->mail->addReplyTo("john.doe@outlook.com", "Blood Bank System");';
echo "</code>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='test_outlook_email.php' class='btn btn-success'>Test Email</a>";
echo "<a href='setup_outlook_email.php' class='btn btn-secondary'>Back to Setup</a>";
echo "<a href='index.php' class='btn btn-info'>Back to Home</a>";
echo "</div>";

echo "<hr>";
echo "<p><strong>💡 Tip:</strong> If you don't have an Outlook account, you can create one for free at <a href='https://outlook.live.com/' target='_blank'>outlook.live.com</a></p>";
?>
