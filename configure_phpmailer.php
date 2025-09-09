<?php
// configure_phpmailer.php
// Configure PHPMailer with Composer installation

echo "<h2>⚙️ Configure PHPMailer with Composer</h2>";

// Check if Composer PHPMailer is installed
if (!file_exists('vendor/autoload.php')) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h4>❌ Composer PHPMailer Not Installed</h4>";
    echo "<p>Please install Composer and PHPMailer first:</p>";
    echo "<a href='install_composer_phpmailer.php' class='btn btn-primary'>Install Composer & PHPMailer</a>";
    echo "</div>";
    exit;
}

echo "<h3>📁 Composer PHPMailer Found!</h3>";
echo "<p style='color: green;'>✅ vendor/autoload.php exists</p>";
echo "<p style='color: green;'>✅ PHPMailer installed via Composer</p>";

echo "<h3>🔧 Creating PHPMailer Configuration</h3>";

// Create PHPMailer service with Composer
$phpmailer_service_content = '<?php
// includes/composer_phpmailer_service.php
// PHPMailer service using Composer installation

require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\\PHPMailer\\PHPMailer;
use PHPMailer\\PHPMailer\\SMTP;
use PHPMailer\\PHPMailer\\Exception;

class ComposerPHPMailerService {
    private $mail;
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->mail = new PHPMailer(true);
        $this->configureMailer();
    }
    
    private function configureMailer() {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = "smtp.gmail.com";
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = "anakhavaishakham2005@gmail.com";
            $this->mail->Password   = "your_app_password_here"; // Gmail App Password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            
            // Recipients
            $this->mail->setFrom("anakhavaishakham2005@gmail.com", "Blood Bank System");
            $this->mail->addReplyTo("anakhavaishakham2005@gmail.com", "Blood Bank System");
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->CharSet = "UTF-8";
            
        } catch (Exception $e) {
            echo "PHPMailer configuration error: " . $e->getMessage();
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
            $this->logNotification($to, $subject, $body, "composer_phpmailer");
            
            return $result;
            
        } catch (Exception $e) {
            // Log error
            $this->logNotification($to, $subject, $body . "\\n\\nError: " . $e->getMessage(), "composer_phpmailer_error");
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
    
    public function getVersion() {
        return PHPMailer::VERSION;
    }
}
';

// Write the service file
file_put_contents('includes/composer_phpmailer_service.php', $phpmailer_service_content);
echo "<p style='color: green;'>✅ Created Composer PHPMailer service</p>";

// Create notification service that uses Composer PHPMailer
$notification_service_content = '<?php
// includes/composer_notification_service.php
// Notification service using Composer PHPMailer

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/functions.php";
require_once __DIR__ . "/composer_phpmailer_service.php";

class ComposerNotificationService {
    private $conn;
    private $phpmailerService;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->phpmailerService = new ComposerPHPMailerService($connection);
    }
    
    /**
     * Send notification to donor about blood request match
     */
    public function notifyDonorMatch($donor_id, $request_id) {
        // Get donor details
        $donor_query = "SELECT * FROM donors WHERE donor_id = ?";
        $stmt = $this->conn->prepare($donor_query);
        $stmt->bind_param("i", $donor_id);
        $stmt->execute();
        $donor = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$donor) return false;
        
        // Get request details
        $request_query = "SELECT r.*, h.name as hospital_name, h.address as hospital_address, h.phone as hospital_phone 
                         FROM blood_requests r 
                         JOIN hospitals h ON r.hospital_id = h.hospital_id 
                         WHERE r.request_id = ?";
        $stmt = $this->conn->prepare($request_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$request) return false;
        
        // Create email content
        $subject = "🩸 Blood Donation Request - Your Help is Needed!";
        $body = $this->createDonorNotificationHTML($donor, $request);
        
        // Send email via Composer PHPMailer
        $sent = $this->phpmailerService->sendNotification($donor["email"], $subject, $body, true);
        
        return $sent;
    }
    
    /**
     * Send notification to hospital about donor availability
     */
    public function notifyHospitalMatch($hospital_id, $request_id, $matched_donors) {
        // Get hospital details
        $hospital_query = "SELECT * FROM hospitals WHERE hospital_id = ?";
        $stmt = $this->conn->prepare($hospital_query);
        $stmt->bind_param("i", $hospital_id);
        $stmt->execute();
        $hospital = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$hospital) return false;
        
        // Get request details
        $request_query = "SELECT * FROM blood_requests WHERE request_id = ?";
        $stmt = $this->conn->prepare($request_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$request) return false;
        
        // Create email content
        $subject = "🩸 Blood Request Update - Donors Found!";
        $body = $this->createHospitalNotificationHTML($hospital, $request, $matched_donors);
        
        // Send email via Composer PHPMailer
        $sent = $this->phpmailerService->sendNotification($hospital["email"], $subject, $body, true);
        
        return $sent;
    }
    
    /**
     * Send notification to admin about new request
     */
    public function notifyAdminNewRequest($request_id) {
        // Get admin emails
        $admin_query = "SELECT email FROM admins";
        $result = $this->conn->query($admin_query);
        
        // Get request details
        $request_query = "SELECT r.*, h.name as hospital_name FROM blood_requests r 
                         JOIN hospitals h ON r.hospital_id = h.hospital_id 
                         WHERE r.request_id = ?";
        $stmt = $this->conn->prepare($request_query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$request) return false;
        
        $subject = "🩸 New Blood Request - Action Required";
        $body = $this->createAdminNotificationHTML($request);
        
        $sent_count = 0;
        while ($admin = $result->fetch_assoc()) {
            if ($this->phpmailerService->sendNotification($admin["email"], $subject, $body, true)) {
                $sent_count++;
            }
        }
        
        return $sent_count > 0;
    }
    
    /**
     * Create donor notification HTML email
     */
    private function createDonorNotificationHTML($donor, $request) {
        $html = "<!DOCTYPE html>";
        $html .= "<html><head><meta charset=\"UTF-8\">";
        $html .= "<style>";
        $html .= "body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }";
        $html .= ".header { background: #dc3545; color: white; padding: 20px; text-align: center; }";
        $html .= ".content { padding: 20px; }";
        $html .= ".request-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }";
        $html .= ".donor-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; }";
        $html .= ".footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; }";
        $html .= "</style></head><body>";
        
        $html .= "<div class=\"header\">";
        $html .= "<h1>🩸 Blood Donation Request</h1>";
        $html .= "<p>Your Help is Needed!</p>";
        $html .= "</div>";
        
        $html .= "<div class=\"content\">";
        $html .= "<h2>Dear {$donor["name"]},</h2>";
        $html .= "<p>A hospital in your area is urgently requesting blood donations.</p>";
        
        $html .= "<div class=\"request-details\">";
        $html .= "<h3>📋 Request Details:</h3>";
        $html .= "<ul>";
        $html .= "<li><strong>Blood Group Required:</strong> {$request["blood_group"]}</li>";
        $html .= "<li><strong>Quantity Needed:</strong> {$request["quantity"]} units</li>";
        $html .= "<li><strong>Hospital:</strong> {$request["hospital_name"]}</li>";
        $html .= "<li><strong>Hospital Address:</strong> {$request["hospital_address"]}</li>";
        $html .= "<li><strong>Hospital Phone:</strong> {$request["hospital_phone"]}</li>";
        $html .= "</ul>";
        
        if (!empty($request["note"])) {
            $html .= "<p><strong>📝 Additional Notes:</strong> {$request["note"]}</p>";
        }
        $html .= "</div>";
        
        $html .= "<div class=\"donor-info\">";
        $html .= "<h3>✅ Compatibility Confirmed</h3>";
        $html .= "<p>Your blood group (<strong>{$donor["blood_group"]}</strong>) is compatible with this request.</p>";
        $html .= "<p>If you are available and willing to donate, please contact the hospital directly.</p>";
        $html .= "</div>";
        
        $html .= "<p>🙏 Thank you for your willingness to help save lives!</p>";
        $html .= "</div>";
        
        $html .= "<div class=\"footer\">";
        $html .= "<p>Best regards,<br>Blood Bank Management System</p>";
        $html .= "<p>📧 Email: anakhavaishakham2005@gmail.com<br>";
        $html .= "🌐 System: Blood Donation Management</p>";
        $html .= "</div>";
        
        $html .= "</body></html>";
        
        return $html;
    }
    
    /**
     * Create hospital notification HTML email
     */
    private function createHospitalNotificationHTML($hospital, $request, $matched_donors) {
        $html = "<!DOCTYPE html>";
        $html .= "<html><head><meta charset=\"UTF-8\">";
        $html .= "<style>";
        $html .= "body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }";
        $html .= ".header { background: #28a745; color: white; padding: 20px; text-align: center; }";
        $html .= ".content { padding: 20px; }";
        $html .= ".request-details { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }";
        $html .= ".donor-list { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0; }";
        $html .= ".donor-item { background: white; padding: 10px; margin: 10px 0; border-radius: 3px; border-left: 4px solid #28a745; }";
        $html .= ".footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; }";
        $html .= "</style></head><body>";
        
        $html .= "<div class=\"header\">";
        $html .= "<h1>🎉 Donors Found!</h1>";
        $html .= "<p>Blood Request Update</p>";
        $html .= "</div>";
        
        $html .= "<div class=\"content\">";
        $html .= "<h2>Dear {$hospital["name"]},</h2>";
        $html .= "<p>We have found potential donors for your blood request.</p>";
        
        $html .= "<div class=\"request-details\">";
        $html .= "<h3>📋 Request Details:</h3>";
        $html .= "<ul>";
        $html .= "<li><strong>Blood Group:</strong> {$request["blood_group"]}</li>";
        $html .= "<li><strong>Quantity Requested:</strong> {$request["quantity"]} units</li>";
        $html .= "</ul>";
        $html .= "</div>";
        
        $html .= "<div class=\"donor-list\">";
        $html .= "<h3>👥 Matched Donors:</h3>";
        foreach ($matched_donors as $donor) {
            $html .= "<div class=\"donor-item\">";
            $html .= "<h4>• {$donor["name"]} ({$donor["blood_group"]})</h4>";
            $html .= "<p>📧 Email: {$donor["email"]}<br>";
            $html .= "📞 Phone: {$donor["phone"]}<br>";
            $html .= "🏙️ City: {$donor["city"]}</p>";
            $html .= "</div>";
        }
        $html .= "</div>";
        
        $html .= "<p>Please contact these donors directly to coordinate the donation.</p>";
        $html .= "</div>";
        
        $html .= "<div class=\"footer\">";
        $html .= "<p>Best regards,<br>Blood Bank Management System</p>";
        $html .= "<p>📧 Email: anakhavaishakham2005@gmail.com<br>";
        $html .= "🌐 System: Blood Donation Management</p>";
        $html .= "</div>";
        
        $html .= "</body></html>";
        
        return $html;
    }
    
    /**
     * Create admin notification HTML email
     */
    private function createAdminNotificationHTML($request) {
        $html = "<!DOCTYPE html>";
        $html .= "<html><head><meta charset=\"UTF-8\">";
        $html .= "<style>";
        $html .= "body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }";
        $html .= ".header { background: #ffc107; color: #212529; padding: 20px; text-align: center; }";
        $html .= ".content { padding: 20px; }";
        $html .= ".request-details { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0; }";
        $html .= ".footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; }";
        $html .= "</style></head><body>";
        
        $html .= "<div class=\"header\">";
        $html .= "<h1>🩸 New Blood Request</h1>";
        $html .= "<p>Action Required</p>";
        $html .= "</div>";
        
        $html .= "<div class=\"content\">";
        $html .= "<h2>Admin Notification</h2>";
        $html .= "<p>A new blood request has been received and requires your review.</p>";
        
        $html .= "<div class=\"request-details\">";
        $html .= "<h3>📋 Request Details:</h3>";
        $html .= "<ul>";
        $html .= "<li><strong>Request ID:</strong> {$request["request_id"]}</li>";
        $html .= "<li><strong>Hospital:</strong> {$request["hospital_name"]}</li>";
        $html .= "<li><strong>Blood Group:</strong> {$request["blood_group"]}</li>";
        $html .= "<li><strong>Quantity:</strong> {$request["quantity"]} units</li>";
        $html .= "<li><strong>Status:</strong> {$request["status"]}</li>";
        $html .= "<li><strong>Created:</strong> {$request["created_at"]}</li>";
        $html .= "</ul>";
        
        if (!empty($request["note"])) {
            $html .= "<p><strong>📝 Notes:</strong> {$request["note"]}</p>";
        }
        $html .= "</div>";
        
        $html .= "<p>Please review and process this request in the admin panel.</p>";
        $html .= "</div>";
        
        $html .= "<div class=\"footer\">";
        $html .= "<p>Blood Bank Management System</p>";
        $html .= "<p>📧 Email: anakhavaishakham2005@gmail.com<br>";
        $html .= "🌐 System: Blood Donation Management</p>";
        $html .= "</div>";
        
        $html .= "</body></html>";
        
        return $html;
    }
    
    /**
     * Test PHPMailer connection
     */
    public function testConnection() {
        return $this->phpmailerService->testConnection();
    }
    
    /**
     * Update Gmail credentials
     */
    public function updateCredentials($username, $password) {
        $this->phpmailerService->updateCredentials($username, $password);
    }
    
    /**
     * Get PHPMailer version
     */
    public function getVersion() {
        return $this->phpmailerService->getVersion();
    }
}
';

// Write the notification service file
file_put_contents('includes/composer_notification_service.php', $notification_service_content);
echo "<p style='color: green;'>✅ Created Composer notification service</p>";

echo "<h3>🎯 Configuration Required</h3>";
echo "<p>Edit <code>includes/composer_phpmailer_service.php</code> and update:</p>";
echo "<code>";
echo '$this->mail->setUsername("anakhavaishakham2005@gmail.com");<br>';
echo '$this->mail->setPassword("your_app_password_here"); // Replace with Gmail App Password<br>';
echo '</code>';

echo "<h3>🧪 Test Composer PHPMailer</h3>";
echo "<a href='test_phpmailer_composer.php' class='btn btn-primary'>Test Composer PHPMailer</a>";

echo "<h3>📊 What You Get</h3>";
echo "<ul>";
echo "<li>✅ Official PHPMailer via Composer</li>";
echo "<li>✅ Professional email handling</li>";
echo "<li>✅ Better error reporting</li>";
echo "<li>✅ HTML email support</li>";
echo "<li>✅ SMTP authentication</li>";
echo "<li>✅ Database logging</li>";
echo "<li>✅ Easy updates via Composer</li>";
echo "</ul>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='test_phpmailer_composer.php' class='btn btn-success'>Test PHPMailer</a>";
echo "<a href='install_composer_phpmailer.php' class='btn btn-info'>Reinstall</a>";
echo "<a href='index.php' class='btn btn-primary'>Back to Home</a>";
echo "</div>";
?>
