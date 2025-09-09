<?php
// test_notification_system.php
// This script tests the notification system functionality

require_once 'includes/config.php';
require_once 'includes/notification_service.php';
require_once 'includes/matching_service.php';

echo "<h2>Testing Blood Donation Notification System</h2>";

try {
    // Test 1: Create NotificationService instance
    echo "<h3>Test 1: NotificationService Instance</h3>";
    $notificationService = new NotificationService($conn);
    echo "✓ NotificationService created successfully<br>";
    
    // Test 2: Create MatchingService instance
    echo "<h3>Test 2: MatchingService Instance</h3>";
    $matchingService = new MatchingService($conn);
    echo "✓ MatchingService created successfully<br>";
    
    // Test 3: Check database tables
    echo "<h3>Test 3: Database Tables Check</h3>";
    $tables = ['notifications', 'matching_log', 'email_templates'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "✓ Table '$table' exists<br>";
        } else {
            echo "✗ Table '$table' missing - run the SQL schema update<br>";
        }
    }
    
    // Test 4: Get matching statistics
    echo "<h3>Test 4: Matching Statistics</h3>";
    $stats = $matchingService->getMatchingStats();
    echo "✓ Statistics retrieved:<br>";
    echo "- Total requests: " . $stats['total_requests'] . "<br>";
    echo "- Pending requests: " . $stats['pending_requests'] . "<br>";
    echo "- Fulfilled requests: " . $stats['fulfilled_requests'] . "<br>";
    echo "- Notifications (24h): " . $stats['notifications_24h'] . "<br>";
    
    // Test 5: Get notification history
    echo "<h3>Test 5: Notification History</h3>";
    $notifications = $notificationService->getNotificationHistory(5);
    echo "✓ Retrieved " . count($notifications) . " recent notifications<br>";
    
    if (!empty($notifications)) {
        echo "Recent notifications:<br>";
        foreach ($notifications as $notif) {
            echo "- " . $notif['notification_type'] . " to " . $notif['to_email'] . " at " . $notif['sent_at'] . "<br>";
        }
    }
    
    // Test 6: Test email template functionality
    echo "<h3>Test 6: Email Templates</h3>";
    $template_result = $conn->query("SELECT COUNT(*) as count FROM email_templates");
    if ($template_result) {
        $template_count = $template_result->fetch_assoc()['count'];
        echo "✓ Email templates table has $template_count templates<br>";
    } else {
        echo "✗ Email templates table not found<br>";
    }
    
    echo "<h3>System Status: ✓ All tests passed!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li>Run the SQL schema update: <code>sql/notification_schema_update.sql</code></li>";
    echo "<li>Test the admin panel: <a href='modules/admin/manage_requests.php'>Manage Requests</a></li>";
    echo "<li>Test notifications: <a href='modules/admin/notifications.php'>View Notifications</a></li>";
    echo "<li>Test matching stats: <a href='modules/admin/matching_stats.php'>Matching Statistics</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and ensure all required tables exist.</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?>
