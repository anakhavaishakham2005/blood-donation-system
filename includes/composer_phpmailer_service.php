<?php
// includes/composer_phpmailer_service.php
// PHPMailer service using Composer installation

require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
            $this->logNotification($to, $subject, $body . "\n\nError: " . $e->getMessage(), "composer_phpmailer_error");
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
