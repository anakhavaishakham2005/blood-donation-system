<?php
include('verify.php');
include('../../includes/config.php');
require_once('../../includes/matching_service.php');

$matchingService = new MatchingService($conn);

// Process matching for new requests
if(isset($_GET['match_request'])){
    $request_id = $_GET['match_request'];
    $result = $matchingService->findAndNotifyMatches($request_id);
    
    if($result['success']) {
        $_SESSION['success_message'] = "Found " . $result['donors_count'] . " compatible donors and sent notifications!";
    } else {
        $_SESSION['error_message'] = $result['message'];
    }
    header("Location: manage_requests.php");
    exit;
}

// Process all pending requests
if(isset($_GET['match_all'])){
    $results = $matchingService->processAllPendingRequests();
    $_SESSION['success_message'] = "Processed " . $results['processed'] . " pending requests!";
    header("Location: manage_requests.php");
    exit;
}

// Approve / Reject Requests
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->query("UPDATE blood_requests SET status='fulfilled' WHERE request_id=$id");
    header("Location: manage_requests.php");
}
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->query("UPDATE blood_requests SET status='cancelled' WHERE request_id=$id");
    header("Location: manage_requests.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Requests</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Hospital Blood Requests</h2>
    
    <!-- Success/Error Messages -->
    <?php if(isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="?match_all=1" class="btn btn-primary">Match All Pending Requests</a>
        <a href="notifications.php" class="btn btn-secondary">View Notifications</a>
        <a href="matching_stats.php" class="btn btn-info">Matching Statistics</a>
    </div>
    
    <table>
        <tr>
            <th>ID</th><th>Hospital</th><th>Blood Group</th><th>Quantity</th><th>Status</th><th>Action</th>
        </tr>
        <?php
        $res = $conn->query("SELECT r.*, h.name AS hospital_name FROM blood_requests r JOIN hospitals h ON r.hospital_id=h.hospital_id");
        while($row = $res->fetch_assoc()){
            $statusClass = '';
            if($row['status'] == 'fulfilled') $statusClass = 'status-approved';
            elseif($row['status'] == 'cancelled') $statusClass = 'status-rejected';
            
            echo "<tr>
                <td>{$row['request_id']}</td>
                <td>{$row['hospital_name']}</td>
                <td>{$row['blood_group']}</td>
                <td>{$row['quantity']}</td>
                <td class='$statusClass'>{$row['status']}</td>
                <td>";
            
            if($row['status'] == 'pending') {
                echo "<a href='?match_request={$row['request_id']}' class='btn-match'>Find Donors</a> |
                      <a href='?approve={$row['request_id']}' class='btn-approve'>Fulfill</a> |
                      <a href='?reject={$row['request_id']}' class='btn-reject'>Cancel</a>";
            } else {
                echo "<span style='color: gray;'>Processed</span>";
            }
            
            echo "</td>
            </tr>";
        }
        ?>
    </table>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
