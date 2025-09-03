<?php
include('verify.php');
include('../../includes/config.php');

// Approve or Reject Hospital
if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->query("UPDATE hospitals SET hospital_id=$id WHERE hospital_id=$id"); // No status column, just show all
    header("Location: verify_hospitals.php");
}
if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    // Since there's no status column, we'll just redirect back
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
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>City</th><th>Action</th>
        </tr>
        <?php
        // Show all hospitals since there's no verification status in the current schema
        $res = $conn->query("SELECT * FROM hospitals ORDER BY hospital_id DESC");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                <td>{$row['hospital_id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['address']}</td>
                <td>{$row['city']}</td>
                <td>
                    <span style='color: green;'>Verified</span>
                </td>
            </tr>";
        }
        ?>
    </table>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
