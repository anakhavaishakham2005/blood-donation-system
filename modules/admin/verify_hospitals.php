<?php
include('verify.php');
include('../../includes/config.php');

// Approve or Reject Hospital
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->query("UPDATE hospitals SET status='Approved' WHERE id=$id");
    header("Location: verify_hospitals.php");
}
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->query("UPDATE hospitals SET status='Rejected' WHERE id=$id");
    header("Location: verify_hospitals.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Hospitals</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Verify Hospitals</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Status</th><th>Action</th>
        </tr>
        <?php
        $res = $conn->query("SELECT * FROM hospitals WHERE status='Pending'");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['address']}</td>
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
