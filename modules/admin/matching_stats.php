<?php
include('verify.php');
include('../../includes/config.php');
require_once('../../includes/matching_service.php');

$matchingService = new MatchingService($conn);

// Get matching statistics
$stats = $matchingService->getMatchingStats();

// Get matching history
$history = $matchingService->getMatchingHistory(20);

// Get blood group compatibility chart data
$compatibility_data = [];
$blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

foreach ($blood_groups as $group) {
    $compatible = compatible_donors_for_recipient($group);
    $compatibility_data[$group] = $compatible;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Matching Statistics</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.info { border-left-color: #17a2b8; }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        .stat-card.success .stat-number { color: #28a745; }
        .stat-card.warning .stat-number { color: #ffc107; }
        .stat-card.info .stat-number { color: #17a2b8; }
        
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
        
        .compatibility-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .compatibility-table th,
        .compatibility-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .compatibility-table th {
            background-color: #f8f9fa;
        }
        .compatible {
            background-color: #d4edda;
            color: #155724;
        }
        
        .history-item {
            background: #fff;
            border: 1px solid #ddd;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
        }
        
        .donor-count {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Matching Statistics & Analytics</h2>
    
    <!-- Key Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total_requests'] ?></div>
            <div class="stat-label">Total Requests</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-number"><?= $stats['pending_requests'] ?></div>
            <div class="stat-label">Pending Requests</div>
        </div>
        <div class="stat-card success">
            <div class="stat-number"><?= $stats['fulfilled_requests'] ?></div>
            <div class="stat-label">Fulfilled Requests</div>
        </div>
        <div class="stat-card info">
            <div class="stat-number"><?= $stats['notifications_24h'] ?></div>
            <div class="stat-label">Notifications (24h)</div>
        </div>
    </div>
    
    <!-- Available Donors by Blood Group -->
    <h3>Available Donors by Blood Group</h3>
    <div class="stats-grid">
        <?php foreach ($stats['available_donors'] as $group => $count): ?>
            <div class="stat-card">
                <div class="stat-number"><?= $count ?></div>
                <div class="stat-label"><?= $group ?> Available</div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Blood Group Compatibility Chart -->
    <h3>Blood Group Compatibility</h3>
    <p>This table shows which donor blood groups are compatible with recipient blood groups:</p>
    <table class="compatibility-table">
        <thead>
            <tr>
                <th>Recipient Blood Group</th>
                <th>Compatible Donor Groups</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($compatibility_data as $recipient => $compatible): ?>
                <tr>
                    <td><strong><?= $recipient ?></strong></td>
                    <td>
                        <?php foreach ($compatible as $donor): ?>
                            <span class="compatible"><?= $donor ?></span>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Recent Matching History -->
    <h3>Recent Matching History</h3>
    <?php if (empty($history)): ?>
        <div class="alert alert-info">No matching history found.</div>
    <?php else: ?>
        <?php foreach ($history as $match): ?>
            <div class="history-item">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Request #<?= $match['request_id'] ?></strong> - 
                        <?= $match['hospital_name'] ?> (<?= $match['blood_group'] ?>, <?= $match['quantity'] ?> units)
                    </div>
                    <div>
                        <span class="donor-count"><?= $match['matched_count'] ?> donors</span>
                        <span style="margin-left: 10px; color: #666;">
                            <?= date('M j, Y g:i A', strtotime($match['created_at'])) ?>
                        </span>
                    </div>
                </div>
                <?php if (!empty($match['matched_donor_ids'])): ?>
                    <div style="margin-top: 10px;">
                        <strong>Matched Donor IDs:</strong> <?= $match['matched_donor_ids'] ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div style="margin-top: 30px;">
        <a href="manage_requests.php" class="btn btn-primary">Manage Requests</a>
        <a href="notifications.php" class="btn btn-secondary">View Notifications</a>
        <a href="dashboard.php" class="btn btn-info">Admin Dashboard</a>
    </div>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
