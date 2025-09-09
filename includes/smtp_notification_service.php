<?php
// includes/smtp_notification_service.php
// Enhanced notification service with SMTP support

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/smtp_config.php';

class SMTPNotificationService {
    private $conn;
    private $smtp_config;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->smtp_config = getSMTPConfig();
    }
    
    /**
     * Send email using SMTP (Gmail)
     */
    public function sendSMTPEmail($to, $subject, $body, $isHTML = false) {
        // Create email headers
        $boundary = md5(uniqid(time()));
        $headers = $this->createEmailHeaders($boundary, $isHTML);
        
        // Create email body
        $email_body = $this->createEmailBody($body, $boundary, $isHTML);
        
        // Try to send via SMTP using socket
        $result = $this->sendViaSocket($to, $subject, $email_body, $headers);
        
        // Always log the notification
        $this->logNotification($to, $subject, $body, 'smtp_email');
        
        return $result;
    }
    
    /**
     * Create email headers
     */
    private function createEmailHeaders($boundary, $isHTML) {
        $headers = "From: " . $this->smtp_config['from_name'] . " <" . $this->smtp_config['from_email'] . ">\r\n";
        $headers .= "Reply-To: " . $this->smtp_config['from_email'] . "\r\n";
        $headers .= "X-Mailer: Blood Bank System\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($isHTML) {
            $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        return $headers;
    }
    
    /**
     * Create email body
     */
    private function createEmailBody($body, $boundary, $isHTML) {
        if ($isHTML) {
            $email_body = "--$boundary\r\n";
            $email_body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $email_body .= strip_tags($body) . "\r\n\r\n";
            $email_body .= "--$boundary\r\n";
            $email_body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $email_body .= $body . "\r\n\r\n";
            $email_body .= "--$boundary--\r\n";
        } else {
            $email_body = $body;
        }
        
        return $email_body;
    }
    
    /**
     * Send email via socket connection (simplified SMTP)
     */
    private function sendViaSocket($to, $subject, $body, $headers) {
        // For now, we'll use PHP's mail() function with proper headers
        // In production, you'd implement full SMTP protocol here
        
        // Set SMTP settings via ini_set
        ini_set('SMTP', $this->smtp_config['host']);
        ini_set('smtp_port', $this->smtp_config['port']);
        ini_set('sendmail_from', $this->smtp_config['from_email']);
        
        // Try to send email
        $result = mail($to, $subject, $body, $headers);
        
        // Reset settings
        ini_restore('SMTP');
        ini_restore('smtp_port');
        ini_restore('sendmail_from');
        
        return $result;
    }
    
    /**
     * Send notification to donor about blood request match
     */
    public function notifyDonorMatch($donor_id, $request_id) {
        // Get donor details
        $donor_query = "SELECT * FROM donors WHERE donor_id = ?";
        $stmt = $this->conn->prepare($donor_query);
        $stmt->bind_param('i', $donor_id);
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
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$request) return false;
        
        // Create email content
        $subject = "🩸 Blood Donation Request - Your Help is Needed!";
        $body = $this->createDonorNotificationBody($donor, $request);
        
        // Send email via SMTP
        $sent = $this->sendSMTPEmail($donor['email'], $subject, $body);
        
        return $sent;
    }
    
    /**
     * Send notification to hospital about donor availability
     */
    public function notifyHospitalMatch($hospital_id, $request_id, $matched_donors) {
        // Get hospital details
        $hospital_query = "SELECT * FROM hospitals WHERE hospital_id = ?";
        $stmt = $this->conn->prepare($hospital_query);
        $stmt->bind_param('i', $hospital_id);
        $stmt->execute();
        $hospital = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$hospital) return false;
        
        // Get request details
        $request_query = "SELECT * FROM blood_requests WHERE request_id = ?";
        $stmt = $this->conn->prepare($request_query);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$request) return false;
        
        // Create email content
        $subject = "🩸 Blood Request Update - Donors Found!";
        $body = $this->createHospitalNotificationBody($hospital, $request, $matched_donors);
        
        // Send email via SMTP
        $sent = $this->sendSMTPEmail($hospital['email'], $subject, $body);
        
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
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$request) return false;
        
        $subject = "🩸 New Blood Request - Action Required";
        $body = $this->createAdminNotificationBody($request);
        
        $sent_count = 0;
        while ($admin = $result->fetch_assoc()) {
            if ($this->sendSMTPEmail($admin['email'], $subject, $body)) {
                $sent_count++;
            }
        }
        
        return $sent_count > 0;
    }
    
    /**
     * Create donor notification email body
     */
    private function createDonorNotificationBody($donor, $request) {
        $body = "Dear {$donor['name']},\n\n";
        $body .= "🩸 A hospital in your area is urgently requesting blood donations.\n\n";
        $body .= "📋 REQUEST DETAILS:\n";
        $body .= "Blood Group Required: {$request['blood_group']}\n";
        $body .= "Quantity Needed: {$request['quantity']} units\n";
        $body .= "Hospital: {$request['hospital_name']}\n";
        $body .= "Hospital Address: {$request['hospital_address']}\n";
        $body .= "Hospital Phone: {$request['hospital_phone']}\n\n";
        
        if (!empty($request['note'])) {
            $body .= "📝 Additional Notes: {$request['note']}\n\n";
        }
        
        $body .= "✅ Your blood group ({$donor['blood_group']}) is compatible with this request.\n\n";
        $body .= "If you are available and willing to donate, please contact the hospital directly.\n\n";
        $body .= "🙏 Thank you for your willingness to help save lives!\n\n";
        $body .= "Best regards,\n";
        $body .= "Blood Bank Management System\n";
        $body .= "📧 Email: " . $this->smtp_config['from_email'] . "\n";
        $body .= "🌐 System: Blood Donation Management";
        
        return $body;
    }
    
    /**
     * Create hospital notification email body
     */
    private function createHospitalNotificationBody($hospital, $request, $matched_donors) {
        $body = "Dear {$hospital['name']},\n\n";
        $body .= "🎉 We have found potential donors for your blood request!\n\n";
        $body .= "📋 REQUEST DETAILS:\n";
        $body .= "Blood Group: {$request['blood_group']}\n";
        $body .= "Quantity Requested: {$request['quantity']} units\n\n";
        
        $body .= "👥 MATCHED DONORS:\n";
        foreach ($matched_donors as $donor) {
            $body .= "• {$donor['name']} ({$donor['blood_group']})\n";
            $body .= "  📧 Email: {$donor['email']}\n";
            $body .= "  📞 Phone: {$donor['phone']}\n";
            $body .= "  🏙️ City: {$donor['city']}\n\n";
        }
        
        $body .= "Please contact these donors directly to coordinate the donation.\n\n";
        $body .= "Best regards,\n";
        $body .= "Blood Bank Management System\n";
        $body .= "📧 Email: " . $this->smtp_config['from_email'] . "\n";
        $body .= "🌐 System: Blood Donation Management";
        
        return $body;
    }
    
    /**
     * Create admin notification email body
     */
    private function createAdminNotificationBody($request) {
        $body = "🩸 NEW BLOOD REQUEST RECEIVED\n\n";
        $body .= "📋 Request Details:\n";
        $body .= "Request ID: {$request['request_id']}\n";
        $body .= "Hospital: {$request['hospital_name']}\n";
        $body .= "Blood Group: {$request['blood_group']}\n";
        $body .= "Quantity: {$request['quantity']} units\n";
        $body .= "Status: {$request['status']}\n";
        $body .= "Created: {$request['created_at']}\n\n";
        
        if (!empty($request['note'])) {
            $body .= "📝 Notes: {$request['note']}\n\n";
        }
        
        $body .= "Please review and process this request in the admin panel.\n\n";
        $body .= "Blood Bank Management System\n";
        $body .= "📧 Email: " . $this->smtp_config['from_email'] . "\n";
        $body .= "🌐 System: Blood Donation Management";
        
        return $body;
    }
    
    /**
     * Log notification in database
     */
    private function logNotification($to_email, $subject, $body, $type = 'smtp_email') {
        $stmt = $this->conn->prepare("INSERT INTO notifications (to_email, subject, body, notification_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $to_email, $subject, $body, $type);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Test SMTP connection
     */
    public function testConnection() {
        return testSMTPConnection();
    }
}
?>
