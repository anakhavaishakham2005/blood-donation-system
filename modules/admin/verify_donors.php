<?php
include('verify.php');
include('../../includes/config.php');

// Approve or Reject Donor
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->query("UPDATE donors SET availability_status=1 WHERE donor_id=$id");
    header("Location: verify_donors.php");
}
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->query("UPDATE donors SET availability_status=0 WHERE donor_id=$id");
    header("Location: verify_donors.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Donors</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Verify Donors</h2>
    <table class="table table-striped table-bordered">
        <tr>
            <th>ID</th><th>Name</th><th>Blood Group</th><th>Email</th><th>Phone</th><th>Status</th><th>Action</th>
        </tr>
        <?php
        // Show all donors since there's no verification status in the current schema
        // We'll show availability_status instead
        $res = $conn->query("SELECT * FROM donors ORDER BY donor_id DESC");
        while($row = $res->fetch_assoc()){
            $status = $row['availability_status'] == 1 ? 'Available' : 'Not Available';
            $statusClass = $row['availability_status'] == 1 ? 'status-approved' : 'status-rejected';
            
            echo "<tr>
                <td>{$row['donor_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['blood_group']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td class='$statusClass'>$status</td>
                <td>";
            
            if($row['availability_status'] == 1) {
                echo "<a class='btn btn-sm btn-outline-secondary' href='?reject={$row['donor_id']}'>Mark Unavailable</a>";
            } else {
                echo "<a class='btn btn-sm btn-success' href='?approve={$row['donor_id']}'>Mark Available</a>";
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

