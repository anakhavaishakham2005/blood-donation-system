<?php
// includes/notification_service.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

class NotificationService {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
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
        $subject = "Blood Donation Request - Your Help is Needed!";
        $body = $this->createDonorNotificationBody($donor, $request);
        
        // Send email
        $sent = $this->sendEmail($donor['email'], $subject, $body);
        
        // Log notification
        $this->logNotification($donor['email'], $subject, $body, 'donor_match');
        
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
        $subject = "Blood Request Update - Donors Found!";
        $body = $this->createHospitalNotificationBody($hospital, $request, $matched_donors);
        
        // Send email
        $sent = $this->sendEmail($hospital['email'], $subject, $body);
        
        // Log notification
        $this->logNotification($hospital['email'], $subject, $body, 'hospital_match');
        
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
        
        $subject = "New Blood Request - Action Required";
        $body = $this->createAdminNotificationBody($request);
        
        $sent_count = 0;
        while ($admin = $result->fetch_assoc()) {
            if ($this->sendEmail($admin['email'], $subject, $body)) {
                $sent_count++;
            }
            $this->logNotification($admin['email'], $subject, $body, 'admin_new_request');
        }
        
        return $sent_count > 0;
    }
    
    /**
     * Send notification about donation completion
     */
    public function notifyDonationComplete($donor_id, $donation_id) {
        // Get donor details
        $donor_query = "SELECT * FROM donors WHERE donor_id = ?";
        $stmt = $this->conn->prepare($donor_query);
        $stmt->bind_param('i', $donor_id);
        $stmt->execute();
        $donor = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$donor) return false;
        
        // Get donation details
        $donation_query = "SELECT * FROM donations WHERE donation_id = ?";
        $stmt = $this->conn->prepare($donation_query);
        $stmt->bind_param('i', $donation_id);
        $stmt->execute();
        $donation = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$donation) return false;
        
        $subject = "Thank You for Your Blood Donation!";
        $body = $this->createDonationCompleteBody($donor, $donation);
        
        $sent = $this->sendEmail($donor['email'], $subject, $body);
        $this->logNotification($donor['email'], $subject, $body, 'donation_complete');
        
        return $sent;
    }
    
    /**
     * Create donor notification email body
     */
    private function createDonorNotificationBody($donor, $request) {
        $body = "Dear {$donor['name']},\n\n";
        $body .= "A hospital in your area is urgently requesting blood donations.\n\n";
        $body .= "REQUEST DETAILS:\n";
        $body .= "Blood Group Required: {$request['blood_group']}\n";
        $body .= "Quantity Needed: {$request['quantity']} units\n";
        $body .= "Hospital: {$request['hospital_name']}\n";
        $body .= "Hospital Address: {$request['hospital_address']}\n";
        $body .= "Hospital Phone: {$request['hospital_phone']}\n\n";
        
        if (!empty($request['note'])) {
            $body .= "Additional Notes: {$request['note']}\n\n";
        }
        
        $body .= "Your blood group ({$donor['blood_group']}) is compatible with this request.\n\n";
        $body .= "If you are available and willing to donate, please contact the hospital directly.\n\n";
        $body .= "Thank you for your willingness to help save lives!\n\n";
        $body .= "Best regards,\n";
        $body .= "Blood Bank Management System";
        
        return $body;
    }
    
    /**
     * Create hospital notification email body
     */
    private function createHospitalNotificationBody($hospital, $request, $matched_donors) {
        $body = "Dear {$hospital['name']},\n\n";
        $body .= "We have found potential donors for your blood request.\n\n";
        $body .= "REQUEST DETAILS:\n";
        $body .= "Blood Group: {$request['blood_group']}\n";
        $body .= "Quantity Requested: {$request['quantity']} units\n\n";
        
        $body .= "MATCHED DONORS:\n";
        foreach ($matched_donors as $donor) {
            $body .= "- {$donor['name']} ({$donor['blood_group']})\n";
            $body .= "  Email: {$donor['email']}\n";
            $body .= "  Phone: {$donor['phone']}\n";
            $body .= "  City: {$donor['city']}\n\n";
        }
        
        $body .= "Please contact these donors directly to coordinate the donation.\n\n";
        $body .= "Best regards,\n";
        $body .= "Blood Bank Management System";
        
        return $body;
    }
    
    /**
     * Create admin notification email body
     */
    private function createAdminNotificationBody($request) {
        $body = "New Blood Request Received\n\n";
        $body .= "Request ID: {$request['request_id']}\n";
        $body .= "Hospital: {$request['hospital_name']}\n";
        $body .= "Blood Group: {$request['blood_group']}\n";
        $body .= "Quantity: {$request['quantity']} units\n";
        $body .= "Status: {$request['status']}\n";
        $body .= "Created: {$request['created_at']}\n\n";
        
        if (!empty($request['note'])) {
            $body .= "Notes: {$request['note']}\n\n";
        }
        
        $body .= "Please review and process this request in the admin panel.\n\n";
        $body .= "Blood Bank Management System";
        
        return $body;
    }
    
    /**
     * Create donation completion email body
     */
    private function createDonationCompleteBody($donor, $donation) {
        $body = "Dear {$donor['name']},\n\n";
        $body .= "Thank you for your generous blood donation!\n\n";
        $body .= "DONATION DETAILS:\n";
        $body .= "Donation Date: {$donation['donation_date']}\n";
        $body .= "Units Donated: {$donation['units']}\n";
        
        if (!empty($donation['notes'])) {
            $body .= "Notes: {$donation['notes']}\n";
        }
        
        $body .= "\nYour donation will help save lives. We appreciate your contribution to our community.\n\n";
        $body .= "Please remember that you can donate again after 90 days from your last donation.\n\n";
        $body .= "Best regards,\n";
        $body .= "Blood Bank Management System";
        
        return $body;
    }
    
    /**
     * Send email using PHP mail function
     */
    private function sendEmail($to, $subject, $body) {
        $headers = "From: Blood Bank System <noreply@bloodbank.local>\r\n";
        $headers .= "Reply-To: admin@bloodbank.local\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        return mail($to, $subject, $body, $headers);
    }
    
    /**
     * Log notification in database
     */
    private function logNotification($to_email, $subject, $body, $type = 'general') {
        $stmt = $this->conn->prepare("INSERT INTO notifications (to_email, subject, body, notification_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $to_email, $subject, $body, $type);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Get notification history
     */
    public function getNotificationHistory($limit = 50) {
        $query = "SELECT * FROM notifications ORDER BY sent_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        $stmt->close();
        return $notifications;
    }
}
?>
