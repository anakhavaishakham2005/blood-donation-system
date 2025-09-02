<?php
include('verify.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>

<div class="container">
    <h2>Welcome, <?= $_SESSION['admin_name']; ?></h2>
    <ul class="admin-links">
        <li><a href="verify_donors.php">Verify Donors</a></li>
        <li><a href="verify_hospitals.php">Verify Hospitals</a></li>
        <li><a href="manage_inventory.php">Manage Inventory</a></li>
        <li><a href="manage_requests.php">Manage Requests</a></li>
        <li><a href="donation_history.php">Donation History</a></li>
        <li><a href="search_donors.php">Search Donors</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<?php include('../../includes/footer.php'); ?>
</body>
</html>
