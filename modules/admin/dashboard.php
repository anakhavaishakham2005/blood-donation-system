<?php
include('verify.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<style>
    .btna{
        background-color: black;
        color: white;
        border: none;
        padding: 5px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        width: 250px;
        transition: background 0.3s, box-shadow 0.3s, transform 0.3s;
    }
</style>
<body>
<?php include('../../includes/header.php'); ?>

<div class="container">
    <h2>Welcome, <?= $_SESSION['admin_name']; ?></h2>
    <div class="d-grid gap-2 col-md-6">
        <a class="btna " href="verify_donors.php">Verify Donors</a>
        <a class="btna " href="verify_hospitals.php">Verify Hospitals</a>
        <a class="btna" href="donation_history.php">Donation History</a>
        <a class="btna" href="manage_requests.php">Manage Requests</a>
        <a class="btna" href="search_donors.php">Search Donors</a>
        <a class="btna" href="manage_inventory.php">Manage Inventory</a>
        <a class="btn" href="profile.php">Profile</a>
        <a class="btn" href="logout.php">Logout</a>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
</body>
</html>
