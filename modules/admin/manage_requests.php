<?php
include('verify.php');
include('../../includes/config.php');

// Approve / Reject Requests
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->query("UPDATE requests SET status='Approved' WHERE id=$id");
    header("Location: manage_requests.php");
}
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->query("UPDATE requests SET status='Rejected' WHERE id=$id");
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
        $res = $conn->query("SELECT r.*, h.name AS hospital_name FROM requests r JOIN hospitals h ON r.hospital_id=h.id");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['hospital_name']}</td>
                <td>{$row['blood_group']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['status']}</td>
                <td>
                    <a href='?approve={$row['id']}'>Approve</a> |
                    <a href='?reject={$row['id']}'>Reject</a>
                </td>
            </tr>";
        }
        ?>
    </table>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
