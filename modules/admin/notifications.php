<?php
include('verify.php');
include('../../includes/config.php');
require_once('../../includes/notification_service.php');

$notificationService = new NotificationService($conn);

// Get notification history
$notifications = $notificationService->getNotificationHistory(100);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_notifications,
    COUNT(CASE WHEN sent_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as notifications_24h,
    COUNT(CASE WHEN notification_type = 'donor_match' THEN 1 END) as donor_matches,
    COUNT(CASE WHEN notification_type = 'hospital_match' THEN 1 END) as hospital_matches
    FROM notifications";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notification Management</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        .notification-item {
            background: #fff;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .notification-type {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }
        .notification-time {
            color: #666;
            font-size: 0.9em;
        }
        .notification-body {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 0.9em;
            max-height: 200px;
            overflow-y: auto;
        }
        .filter-section {
            margin-bottom: 20px;
        }
        .filter-section select, .filter-section input {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Notification Management</h2>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total_notifications'] ?></div>
            <div class="stat-label">Total Notifications</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['notifications_24h'] ?></div>
            <div class="stat-label">Last 24 Hours</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['donor_matches'] ?></div>
            <div class="stat-label">Donor Matches</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['hospital_matches'] ?></div>
            <div class="stat-label">Hospital Matches</div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="filter-section">
        <form method="GET">
            <label>Filter by Type:</label>
            <select name="type">
                <option value="">All Types</option>
                <option value="donor_match" <?= (isset($_GET['type']) && $_GET['type'] == 'donor_match') ? 'selected' : '' ?>>Donor Match</option>
                <option value="hospital_match" <?= (isset($_GET['type']) && $_GET['type'] == 'hospital_match') ? 'selected' : '' ?>>Hospital Match</option>
                <option value="admin_new_request" <?= (isset($_GET['type']) && $_GET['type'] == 'admin_new_request') ? 'selected' : '' ?>>Admin New Request</option>
                <option value="donation_complete" <?= (isset($_GET['type']) && $_GET['type'] == 'donation_complete') ? 'selected' : '' ?>>Donation Complete</option>
            </select>
            
            <label>Search Email:</label>
            <input type="text" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" placeholder="Enter email">
            
            <button type="submit">Filter</button>
            <a href="notifications.php" class="btn btn-secondary">Clear Filters</a>
        </form>
    </div>
    
    <!-- Notifications List -->
    <div class="notifications-list">
        <?php
        // Apply filters
        $filtered_notifications = $notifications;
        
        if (isset($_GET['type']) && !empty($_GET['type'])) {
            $filtered_notifications = array_filter($filtered_notifications, function($n) {
                return $n['notification_type'] == $_GET['type'];
            });
        }
        
        if (isset($_GET['email']) && !empty($_GET['email'])) {
            $email_filter = $_GET['email'];
            $filtered_notifications = array_filter($filtered_notifications, function($n) use ($email_filter) {
                return stripos($n['to_email'], $email_filter) !== false;
            });
        }
        
        if (empty($filtered_notifications)): ?>
            <div class="alert alert-info">No notifications found matching your criteria.</div>
        <?php else: ?>
            <?php foreach ($filtered_notifications as $notification): ?>
                <div class="notification-item">
                    <div class="notification-header">
                        <div>
                            <strong><?= htmlspecialchars($notification['to_email']) ?></strong>
                            <span class="notification-type"><?= htmlspecialchars($notification['notification_type']) ?></span>
                        </div>
                        <div class="notification-time">
                            <?= date('M j, Y g:i A', strtotime($notification['sent_at'])) ?>
                        </div>
                    </div>
                    <div>
                        <strong>Subject:</strong> <?= htmlspecialchars($notification['subject']) ?>
                    </div>
                    <div class="notification-body">
                        <?= htmlspecialchars($notification['body']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Back to Admin Panel -->
    <div style="margin-top: 30px;">
        <a href="manage_requests.php" class="btn btn-primary">Back to Manage Requests</a>
        <a href="dashboard.php" class="btn btn-secondary">Admin Dashboard</a>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
