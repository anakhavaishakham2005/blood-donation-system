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
    <div class="d-grid gap-2 col-md-6">
        <a class="btn btn-danger" href="verify_donors.php">Verify Donors</a>
        <a class="btn btn-danger" href="verify_hospitals.php">Verify Hospitals</a>
        <a class="btn btn-danger" href="manage_inventory.php">Manage Inventory</a>
        <a class="btn btn-danger" href="manage_requests.php">Manage Requests</a>
        <a class="btn btn-danger" href="donation_history.php">Donation History</a>
        <a class="btn btn-danger" href="search_donors.php">Search Donors</a>
        <a class="btn btn-outline-danger" href="profile.php">Profile</a>
        <a class="btn btn-outline-secondary" href="logout.php">Logout</a>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>
</body>
</html>
