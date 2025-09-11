<?php
include('verify.php');
include('../../includes/config.php');

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
                echo "<a href='?approve={$row['request_id']}'>Fulfill</a> |
                      <a href='?reject={$row['request_id']}'>Cancel</a>";
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
