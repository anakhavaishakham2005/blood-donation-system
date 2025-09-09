<?php
// setup_phpmailer.php
// PHPMailer setup for professional email handling

echo "<h2>📧 PHPMailer Setup Guide</h2>";

echo "<h3>🔧 Installation Steps</h3>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 1: Install Composer (if not installed)</h4>";
echo "<ol>";
echo "<li>Download Composer from <a href='https://getcomposer.org/download/' target='_blank'>getcomposer.org</a></li>";
echo "<li>Run the installer</li>";
echo "<li>Verify installation: <code>composer --version</code></li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f3e5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 2: Install PHPMailer</h4>";
echo "<p>Open command prompt in your project directory and run:</p>";
echo "<code>composer require phpmailer/phpmailer</code>";
echo "</div>";

echo "<div style='background: #e8f5e8; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 3: Create PHPMailer Configuration</h4>";
echo "</div>";

// Create PHPMailer configuration file
$phpmailer_config = '<?php
// includes/phpmailer_config.php
// PHPMailer configuration for Blood Bank System

use PHPMailer\\PHPMailer\\PHPMailer;
use PHPMailer\\PHPMailer\\SMTP;
use PHPMailer\\PHPMailer\\Exception;

require_once __DIR__ . \'/../vendor/autoload.php\';

class PHPMailerService {
    private $mail;
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->mail = new PHPMailer(true);
        $this->configureMailer();
    }
    
    private function configureMailer() {
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host       = \'smtp.gmail.com\';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = \'anakhavaishakham2005@gmail.com\';
        $this->mail->Password   = \'your_app_password_here\'; // Gmail App Password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        
        // Recipients
        $this->mail->setFrom(\'anakhavaishakham2005@gmail.com\', \'Blood Bank System\');
        $this->mail->addReplyTo(\'anakhavaishakham2005@gmail.com\', \'Blood Bank System\');
        
        // Content
        $this->mail->isHTML(true);
        $this->mail->CharSet = \'UTF-8\';
    }
    
    public function sendNotification($to, $subject, $body, $isHTML = false) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);
            
            $this->mail->send();
            
            // Log notification
            $this->logNotification($to, $subject, $body, \'phpmailer\');
            
            return true;
        } catch (Exception $e) {
            // Log error
            $this->logNotification($to, $subject, $body . "\\n\\nError: " . $e->getMessage(), \'phpmailer_error\');
            return false;
        }
    }
    
    private function logNotification($to_email, $subject, $body, $type) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (to_email, subject, body, notification_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param(\'ssss\', $to_email, $subject, $body, $type);
        $stmt->execute();
        $stmt->close();
    }
    
    public function testConnection() {
        try {
            $this->mail->smtpConnect();
            $this->mail->smtpClose();
            return [\'success\' => true, \'message\' => \'SMTP connection successful\'];
        } catch (Exception $e) {
            return [\'success\' => false, \'message\' => $e->getMessage()];
        }
    }
}';

// Write the configuration file
file_put_contents('includes/phpmailer_config.php', $phpmailer_config);

echo "<p>✅ Created PHPMailer configuration file: <code>includes/phpmailer_config.php</code></p>";

echo "<h3>📝 Configuration Steps</h3>";
echo "<ol>";
echo "<li>Update the Gmail credentials in <code>includes/phpmailer_config.php</code></li>";
echo "<li>Replace <code>your_app_password_here</code> with your Gmail app password</li>";
echo "<li>Test the connection</li>";
echo "</ol>";

echo "<h3>🧪 Test PHPMailer</h3>";
echo "<p>After installation, you can test PHPMailer:</p>";
echo "<a href='test_phpmailer.php' class='btn btn-primary'>Test PHPMailer</a>";

echo "<h3>📊 PHPMailer vs Basic SMTP</h3>";
echo "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Feature</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Basic SMTP</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>PHPMailer</th>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Ease of Setup</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Simple</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>⚠️ Requires Composer</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Reliability</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>⚠️ Basic</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Professional</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Error Handling</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>❌ Limited</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Comprehensive</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>Attachments</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>❌ Not supported</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Full support</td>";
echo "</tr>";
echo "<tr>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>HTML Emails</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>⚠️ Manual</td>";
echo "<td style='border: 1px solid #ddd; padding: 8px;'>✅ Easy</td>";
echo "</tr>";
echo "</table>";

echo "<h3>🎯 Recommendation</h3>";
echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<p><strong>For Development:</strong> Use Basic SMTP (easier setup)</p>";
echo "<p><strong>For Production:</strong> Use PHPMailer (more reliable)</p>";
echo "</div>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='setup_smtp.php' class='btn btn-secondary'>Basic SMTP Setup</a>";
echo "<a href='test_email_functionality.php' class='btn btn-info'>Test Basic Email</a>";
echo "<a href='index.php' class='btn btn-primary'>Back to Home</a>";
echo "</div>";
?>
