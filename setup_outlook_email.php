<?php
// setup_outlook_email.php
// Setup Outlook/Hotmail email for Blood Bank System

echo "<h2>📧 Setup Outlook/Hotmail Email</h2>";

echo "<h3>🔧 Why Outlook is Easier</h3>";
echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<p><strong>Outlook advantages:</strong></p>";
echo "<ul>";
echo "<li>✅ No app password required</li>";
echo "<li>✅ Uses regular password</li>";
echo "<li>✅ Easier SMTP setup</li>";
echo "<li>✅ Works with existing accounts</li>";
echo "</ul>";
echo "</div>";

echo "<h3>📋 Setup Steps</h3>";

echo "<div style='background: #f3e5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 1: Get Outlook Account</h4>";
echo "<p>If you don't have an Outlook account:</p>";
echo "<ol>";
echo "<li>Go to: <a href='https://outlook.live.com/' target='_blank'>outlook.live.com</a></li>";
echo "<li>Click 'Create free account'</li>";
echo "<li>Choose @outlook.com or @hotmail.com</li>";
echo "<li>Complete registration</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 2: Configure PHPMailer for Outlook</h4>";
echo "</div>";

// Create Outlook PHPMailer service
$outlook_service_content = '<?php
// includes/outlook_phpmailer_service.php
// PHPMailer service configured for Outlook/Hotmail

require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\\PHPMailer\\PHPMailer;
use PHPMailer\\PHPMailer\\SMTP;
use PHPMailer\\PHPMailer\\Exception;

class OutlookPHPMailerService {
    private $mail;
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->mail = new PHPMailer(true);
        $this->configureMailer();
    }
    
    private function configureMailer() {
        try {
            // Server settings for Outlook
            $this->mail->isSMTP();
            $this->mail->Host       = "smtp-mail.outlook.com";
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = "your-email@outlook.com"; // Replace with your Outlook email
            $this->mail->Password   = "your-password"; // Replace with your Outlook password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            
            // Recipients
            $this->mail->setFrom("your-email@outlook.com", "Blood Bank System"); // Replace with your email
            $this->mail->addReplyTo("your-email@outlook.com", "Blood Bank System"); // Replace with your email
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->CharSet = "UTF-8";
            
        } catch (Exception $e) {
            echo "Outlook PHPMailer configuration error: " . $e->getMessage();
        }
    }
    
    public function sendNotification($to, $subject, $body, $isHTML = true) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $result = $this->mail->send();
            
            // Log notification
            $this->logNotification($to, $subject, $body, "outlook_phpmailer");
            
            return $result;
            
        } catch (Exception $e) {
            // Log error
            $this->logNotification($to, $subject, $body . "\\n\\nError: " . $e->getMessage(), "outlook_phpmailer_error");
            return false;
        }
    }
    
    private function logNotification($to_email, $subject, $body, $type) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (to_email, subject, body, notification_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $to_email, $subject, $body, $type);
        $stmt->execute();
        $stmt->close();
    }
    
    public function testConnection() {
        try {
            $this->mail->smtpConnect();
            $this->mail->smtpClose();
            return ["success" => true, "message" => "Outlook SMTP connection successful"];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
    
    public function updateCredentials($username, $password) {
        $this->mail->setUsername($username);
        $this->mail->setPassword($password);
        $this->mail->setFrom($username, "Blood Bank System");
        $this->mail->addReplyTo($username, "Blood Bank System");
    }
}
';

// Write the Outlook service file
file_put_contents('includes/outlook_phpmailer_service.php', $outlook_service_content);
echo "<p style='color: green;'>✅ Created Outlook PHPMailer service</p>";

echo "<h3>⚙️ Configuration Required</h3>";
echo "<p>Edit <code>includes/outlook_phpmailer_service.php</code> and update:</p>";
echo "<code>";
echo '$this->mail->setUsername("your-email@outlook.com");<br>';
echo '$this->mail->setPassword("your-password");<br>';
echo '$this->mail->setFrom("your-email@outlook.com", "Blood Bank System");<br>';
echo '$this->mail->addReplyTo("your-email@outlook.com", "Blood Bank System");<br>';
echo '</code>';

echo "<h3>📊 Outlook SMTP Settings</h3>";
echo "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Setting</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Value</th>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>SMTP Host</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>smtp-mail.outlook.com</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>SMTP Port</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>587</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Encryption</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>STARTTLS</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Authentication</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>SMTP Auth</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Password</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Regular password (no app password needed)</td>";
echo "</tr>";
echo "</table>";

echo "<h3>🧪 Test Outlook Email</h3>";
echo "<a href='test_outlook_email.php' class='btn btn-primary'>Test Outlook Email</a>";

echo "<h3>🔄 Alternative Email Providers</h3>";
echo "<div style='background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Other Easy Options:</h4>";
echo "<ul>";
echo "<li><strong>Yahoo Mail:</strong> smtp.mail.yahoo.com (port 587)</li>";
echo "<li><strong>ProtonMail:</strong> smtp.protonmail.com (port 587)</li>";
echo "<li><strong>Zoho Mail:</strong> smtp.zoho.com (port 587)</li>";
echo "<li><strong>SendGrid:</strong> Professional email service</li>";
echo "</ul>";
echo "</div>";

echo "<h3>🎯 Quick Test Without Email</h3>";
echo "<p>You can test the entire system without configuring email:</p>";
echo "<ol>";
echo "<li>✅ Add sample data</li>";
echo "<li>✅ Test donor matching</li>";
echo "<li>✅ View notifications in database</li>";
echo "<li>✅ Check admin panel</li>";
echo "<li>✅ Configure email later</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='test_outlook_email.php' class='btn btn-success'>Test Outlook Email</a>";
echo "<a href='add_notification_sample_data.php' class='btn btn-primary'>Test System (No Email)</a>";
echo "<a href='alternative_email_setup.php' class='btn btn-info'>More Options</a>";
echo "<a href='index.php' class='btn btn-secondary'>Back to Home</a>";
echo "</div>";

echo "<hr>";
echo "<p><strong>💡 Tip:</strong> Outlook is much easier to set up than Gmail. Just use your regular email and password - no app passwords needed!</p>";
?>
