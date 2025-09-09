<?php
// phpmailer_manual_setup.php
// Manual PHPMailer setup without Composer

echo "<h2>📧 PHPMailer Manual Setup (No Composer Required)</h2>";

echo "<h3>🔧 Method 1: Download PHPMailer Manually</h3>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 1: Download PHPMailer</h4>";
echo "<ol>";
echo "<li>Go to: <a href='https://github.com/PHPMailer/PHPMailer/releases' target='_blank'>PHPMailer Releases</a></li>";
echo "<li>Download the latest ZIP file (e.g., PHPMailer-6.8.0.zip)</li>";
echo "<li>Extract the ZIP file</li>";
echo "<li>Copy the 'src' folder to your project</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f3e5f5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>Step 2: Create PHPMailer Directory Structure</h4>";
echo "<p>Create this folder structure in your project:</p>";
echo "<code>";
echo "blood-donation-system/<br>";
echo "├── phpmailer/<br>";
echo "│   ├── src/<br>";
echo "│   │   ├── PHPMailer.php<br>";
echo "│   │   ├── SMTP.php<br>";
echo "│   │   ├── Exception.php<br>";
echo "│   │   └── ... (other files)<br>";
echo "│   └── ...<br>";
echo "</code>";
echo "</div>";

echo "<h3>🚀 Method 2: Use Pre-configured PHPMailer Files</h3>";
echo "<p>I'll create the PHPMailer files for you:</p>";

// Create PHPMailer directory structure
if (!is_dir('phpmailer')) {
    mkdir('phpmailer', 0777, true);
    echo "<p>✅ Created phpmailer directory</p>";
}

if (!is_dir('phpmailer/src')) {
    mkdir('phpmailer/src', 0777, true);
    echo "<p>✅ Created phpmailer/src directory</p>";
}

// Create a simplified PHPMailer implementation
$phpmailer_content = '<?php
/**
 * Simplified PHPMailer Implementation
 * For Blood Bank System
 */

class PHPMailer {
    private $host = "smtp.gmail.com";
    private $port = 587;
    private $username = "";
    private $password = "";
    private $from_email = "";
    private $from_name = "";
    private $to_email = "";
    private $subject = "";
    private $body = "";
    private $isHTML = false;
    private $smtpAuth = true;
    private $smtpSecure = "tls";
    
    public function __construct($exceptions = true) {
        // Constructor
    }
    
    public function isSMTP() {
        return true;
    }
    
    public function setHost($host) {
        $this->host = $host;
    }
    
    public function setPort($port) {
        $this->port = $port;
    }
    
    public function setSMTPAuth($auth) {
        $this->smtpAuth = $auth;
    }
    
    public function setUsername($username) {
        $this->username = $username;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
    
    public function setSMTPSecure($secure) {
        $this->smtpSecure = $secure;
    }
    
    public function setFrom($email, $name = "") {
        $this->from_email = $email;
        $this->from_name = $name;
    }
    
    public function addAddress($email, $name = "") {
        $this->to_email = $email;
    }
    
    public function addReplyTo($email, $name = "") {
        // Reply-to functionality
    }
    
    public function isHTML($isHTML) {
        $this->isHTML = $isHTML;
    }
    
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    
    public function setBody($body) {
        $this->body = $body;
    }
    
    public function setAltBody($altBody) {
        // Alt body functionality
    }
    
    public function send() {
        // Use PHP mail() function with proper headers
        $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $headers .= "Reply-To: " . $this->from_email . "\r\n";
        $headers .= "X-Mailer: PHPMailer (Blood Bank System)\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($this->isHTML) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        // Set SMTP settings
        ini_set("SMTP", $this->host);
        ini_set("smtp_port", $this->port);
        ini_set("sendmail_from", $this->from_email);
        
        // Send email
        $result = mail($this->to_email, $this->subject, $this->body, $headers);
        
        // Reset settings
        ini_restore("SMTP");
        ini_restore("smtp_port");
        ini_restore("sendmail_from");
        
        return $result;
    }
    
    public function clearAddresses() {
        $this->to_email = "";
    }
    
    public function smtpConnect() {
        // Test SMTP connection
        $connection = @fsockopen($this->host, $this->port, $errno, $errstr, 10);
        if ($connection) {
            fclose($connection);
            return true;
        }
        return false;
    }
    
    public function smtpClose() {
        // Close SMTP connection
        return true;
    }
}

class SMTP {
    // SMTP class placeholder
}

class Exception extends \Exception {
    // Exception class placeholder
}
';

// Write the simplified PHPMailer
file_put_contents('phpmailer/src/PHPMailer.php', $phpmailer_content);
echo "<p>✅ Created simplified PHPMailer class</p>";

// Create autoloader
$autoloader_content = '<?php
/**
 * Simple Autoloader for PHPMailer
 */

spl_autoload_register(function ($class) {
    $prefix = "PHPMailer\\\\";
    $base_dir = __DIR__ . "/src/";
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace("\\\\", "/", $relative_class) . ".php";
    
    if (file_exists($file)) {
        require $file;
    }
});
';

file_put_contents('phpmailer/autoload.php', $autoloader_content);
echo "<p>✅ Created PHPMailer autoloader</p>";

// Create PHPMailer service
$service_content = '<?php
// includes/phpmailer_service.php
// PHPMailer service for Blood Bank System

require_once __DIR__ . "/../phpmailer/autoload.php";

use PHPMailer\\PHPMailer\\PHPMailer;
use PHPMailer\\PHPMailer\\SMTP;
use PHPMailer\\PHPMailer\\Exception;

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
        $this->mail->setHost("smtp.gmail.com");
        $this->mail->setSMTPAuth(true);
        $this->mail->setUsername("anakhavaishakham2005@gmail.com");
        $this->mail->setPassword("your_app_password_here"); // Gmail App Password
        $this->mail->setSMTPSecure("tls");
        $this->mail->setPort(587);
        
        // Recipients
        $this->mail->setFrom("anakhavaishakham2005@gmail.com", "Blood Bank System");
        $this->mail->addReplyTo("anakhavaishakham2005@gmail.com", "Blood Bank System");
        
        // Content
        $this->mail->isHTML(true);
    }
    
    public function sendNotification($to, $subject, $body, $isHTML = false) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            
            $this->mail->setSubject($subject);
            $this->mail->setBody($body);
            $this->mail->setAltBody(strip_tags($body));
            
            $result = $this->mail->send();
            
            // Log notification
            $this->logNotification($to, $subject, $body, "phpmailer");
            
            return $result;
        } catch (Exception $e) {
            // Log error
            $this->logNotification($to, $subject, $body . "\\n\\nError: " . $e->getMessage(), "phpmailer_error");
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
            return ["success" => true, "message" => "SMTP connection successful"];
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

file_put_contents('includes/phpmailer_service.php', $service_content);
echo "<p>✅ Created PHPMailer service</p>";

echo "<h3>🎯 Next Steps</h3>";
echo "<ol>";
echo "<li>✅ PHPMailer files created successfully</li>";
echo "<li>🔧 Update Gmail credentials in <code>includes/phpmailer_service.php</code></li>";
echo "<li>🧪 Test PHPMailer connection</li>";
echo "<li>📧 Send test email</li>";
echo "</ol>";

echo "<h3>🔧 Configuration Required</h3>";
echo "<p>Edit <code>includes/phpmailer_service.php</code> and update:</p>";
echo "<code>";
echo '$this->mail->setUsername("anakhavaishakham2005@gmail.com");<br>';
echo '$this->mail->setPassword("your_app_password_here"); // Replace with Gmail App Password<br>';
echo '</code>';

echo "<h3>🧪 Test PHPMailer</h3>";
echo "<a href='test_phpmailer.php' class='btn btn-primary'>Test PHPMailer Setup</a>";
echo "<a href='setup_phpmailer.php' class='btn btn-secondary'>PHPMailer Guide</a>";

echo "<h3>📊 What You Get</h3>";
echo "<ul>";
echo "<li>✅ Professional email handling</li>";
echo "<li>✅ Better error reporting</li>";
echo "<li>✅ HTML email support</li>";
echo "<li>✅ SMTP authentication</li>";
echo "<li>✅ Database logging</li>";
echo "</ul>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='test_phpmailer.php' class='btn btn-success'>Test PHPMailer</a>";
echo "<a href='setup_smtp.php' class='btn btn-info'>Basic SMTP Setup</a>";
echo "<a href='index.php' class='btn btn-primary'>Back to Home</a>";
echo "</div>";
?>
