<?php
// includes/matching_service.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/notification_service.php';

class MatchingService {
    private $conn;
    private $notificationService;
    
    public function __construct($connection) {
        $this->conn = $connection;
        $this->notificationService = new NotificationService($connection);
    }
    
    /**
     * Find compatible donors for a blood request and send notifications
     */
    public function findAndNotifyMatches($request_id) {
        // Get request details
        $request_query = "SELECT * FROM blood_requests WHERE request_id = ? AND status = 'pending'";
        $stmt = $this->conn->prepare($request_query);
        $stmt->bind_param('i', $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if (!$request) {
            return ['success' => false, 'message' => 'Request not found or already processed'];
        }
        
        // Find compatible donors
        $compatible_groups = compatible_donors_for_recipient($request['blood_group']);
        if (empty($compatible_groups)) {
            return ['success' => false, 'message' => 'No compatible blood groups found'];
        }
        
        // Get available donors with compatible blood groups
        $donors_query = "SELECT * FROM donors 
                        WHERE availability_status = 1 
                        AND blood_group IN (" . sql_in_list($compatible_groups) . ")
                        AND (last_donation IS NULL OR last_donation <= DATE_SUB(CURDATE(), INTERVAL 90 DAY))
                        ORDER BY city = ? DESC, last_donation ASC";
        
        $stmt = $this->conn->prepare($donors_query);
        $stmt->bind_param('s', $request['city'] ?? '');
        $stmt->execute();
        $result = $stmt->get_result();
        
        $matched_donors = [];
        while ($donor = $result->fetch_assoc()) {
            $matched_donors[] = $donor;
        }
        $stmt->close();
        
        if (empty($matched_donors)) {
            return ['success' => false, 'message' => 'No available donors found'];
        }
        
        // Send notifications
        $notifications_sent = 0;
        
        // Notify donors
        foreach ($matched_donors as $donor) {
            if ($this->notificationService->notifyDonorMatch($donor['donor_id'], $request_id)) {
                $notifications_sent++;
            }
        }
        
        // Notify hospital
        $this->notificationService->notifyHospitalMatch($request['hospital_id'], $request_id, $matched_donors);
        
        // Log the matching in database
        $this->logMatching($request_id, $matched_donors);
        
        return [
            'success' => true,
            'message' => "Found " . count($matched_donors) . " compatible donors",
            'donors_count' => count($matched_donors),
            'notifications_sent' => $notifications_sent,
            'donors' => $matched_donors
        ];
    }
    
    /**
     * Process all pending requests and find matches
     */
    public function processAllPendingRequests() {
        $query = "SELECT request_id FROM blood_requests WHERE status = 'pending' ORDER BY created_at ASC";
        $result = $this->conn->query($query);
        
        $processed = 0;
        $results = [];
        
        while ($row = $result->fetch_assoc()) {
            $result_data = $this->findAndNotifyMatches($row['request_id']);
            $results[] = [
                'request_id' => $row['request_id'],
                'result' => $result_data
            ];
            $processed++;
        }
        
        return [
            'processed' => $processed,
            'results' => $results
        ];
    }
    
    /**
     * Get matching statistics
     */
    public function getMatchingStats() {
        $stats = [];
        
        // Total requests
        $query = "SELECT COUNT(*) as total FROM blood_requests";
        $result = $this->conn->query($query);
        $stats['total_requests'] = $result->fetch_assoc()['total'];
        
        // Pending requests
        $query = "SELECT COUNT(*) as pending FROM blood_requests WHERE status = 'pending'";
        $result = $this->conn->query($query);
        $stats['pending_requests'] = $result->fetch_assoc()['pending'];
        
        // Fulfilled requests
        $query = "SELECT COUNT(*) as fulfilled FROM blood_requests WHERE status = 'fulfilled'";
        $result = $this->conn->query($query);
        $stats['fulfilled_requests'] = $result->fetch_assoc()['fulfilled'];
        
        // Available donors by blood group
        $query = "SELECT blood_group, COUNT(*) as count FROM donors 
                 WHERE availability_status = 1 
                 AND (last_donation IS NULL OR last_donation <= DATE_SUB(CURDATE(), INTERVAL 90 DAY))
                 GROUP BY blood_group";
        $result = $this->conn->query($query);
        $stats['available_donors'] = [];
        while ($row = $result->fetch_assoc()) {
            $stats['available_donors'][$row['blood_group']] = $row['count'];
        }
        
        // Recent notifications
        $query = "SELECT COUNT(*) as count FROM notifications WHERE sent_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $result = $this->conn->query($query);
        $stats['notifications_24h'] = $result->fetch_assoc()['count'];
        
        return $stats;
    }
    
    /**
     * Log matching results in database
     */
    private function logMatching($request_id, $matched_donors) {
        // Create a simple log entry
        $donor_ids = array_column($matched_donors, 'donor_id');
        $donor_ids_str = implode(',', $donor_ids);
        
        $query = "INSERT INTO matching_log (request_id, matched_donor_ids, matched_count, created_at) 
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('isi', $request_id, $donor_ids_str, count($matched_donors));
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Get matching history
     */
    public function getMatchingHistory($limit = 20) {
        $query = "SELECT ml.*, br.blood_group, br.quantity, h.name as hospital_name 
                 FROM matching_log ml 
                 JOIN blood_requests br ON ml.request_id = br.request_id 
                 JOIN hospitals h ON br.hospital_id = h.hospital_id 
                 ORDER BY ml.created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        
        $stmt->close();
        return $history;
    }
}
?>
