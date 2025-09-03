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
            <th>ID</th><th>Donor</th><th>Blood Group</th><th>Units</th><th>Date</th><th>Notes</th>
        </tr>
        <?php
        $res = $conn->query("SELECT d.donation_id, d.donation_date, d.units, d.notes, 
                                    dn.name AS donor_name, dn.blood_group
                             FROM donations d 
                             JOIN donors dn ON d.donor_id=dn.donor_id
                             ORDER BY d.donation_date DESC");
        while($row = $res->fetch_assoc()){
            echo "<tr>
                <td>{$row['donation_id']}</td>
                <td>{$row['donor_name']}</td>
                <td>{$row['blood_group']}</td>
                <td>{$row['units']}</td>
                <td>{$row['donation_date']}</td>
                <td>{$row['notes']}</td>
            </tr>";
        }
        ?>
    </table>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
