<?php
// includes/phpmailer_service.php
// PHPMailer service for Blood Bank System

require_once __DIR__ . "/../phpmailer/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
            $this->logNotification($to, $subject, $body . "\n\nError: " . $e->getMessage(), "phpmailer_error");
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
