<?php
include('verify.php');
include('../../includes/config.php');

// Add / Update Blood Inventory
if(isset($_POST['update'])){
    $blood = $_POST['blood_group'];
    $qty = $_POST['quantity'];

    // Check if record exists
    $res = $conn->query("SELECT * FROM inventory WHERE blood_group='$blood'");
    if($res->num_rows > 0){
        $conn->query("UPDATE inventory SET quantity=quantity+$qty WHERE blood_group='$blood'");
    } else {
        $conn->query("INSERT INTO inventory(blood_group, quantity) VALUES('$blood', $qty)");
    }
    header("Location: manage_inventory.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Inventory</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Manage Blood Inventory</h2>

    <form method="POST">
        <label>Blood Group</label>
        <input type="text" name="blood_group" required>
        <label>Quantity</label>
        <input type="number" name="quantity" required>
        <button type="submit" name="update">Add / Update</button>
    </form>

    <h3>Current Inventory</h3>
    <table>
        <tr><th>Blood Group</th><th>Quantity</th></tr>
        <?php
        $res = $conn->query("SELECT * FROM inventory");
        while($row = $res->fetch_assoc()){
            echo "<tr><td>{$row['blood_group']}</td><td>{$row['quantity']}</td></tr>";
        }
        ?>
    </table>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
