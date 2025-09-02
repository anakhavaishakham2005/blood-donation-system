<?php
include('verify.php');
include('../../includes/config.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Donation History</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Donation History</h2>
    <table>
        <tr>
            <th>ID</th><th>Donor</th><th>Blood Group</th><th>Quantity</th><th>Date</th><th>Hospital</th>
        </tr>
        <?php
        $res = $conn->query("SELECT d.name AS donor_name, r.blood_group, r.quantity, r.date, h.name AS hospital_name 
                             FROM donations r 
                             JOIN donors d ON r.donor_id=d.id
                             JOIN hospitals h ON r.hospital_id=h.id
                             ORDER BY r.date DESC");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                <td></td>
                <td>{$row['donor_name']}</td>
                <td>{$row['blood_group']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['date']}</td>
                <td>{$row['hospital_name']}</td>
            </tr>";
        }
        ?>
    </table>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
