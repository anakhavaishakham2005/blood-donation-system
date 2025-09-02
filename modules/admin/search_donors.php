<?php
include('verify.php');
include('../../includes/config.php');

$blood = '';
$location = '';
$results = [];

if(isset($_GET['search'])){
    $blood = $_GET['blood_group'];
    $location = $_GET['location'];

    $sql = "SELECT * FROM donors WHERE status='Approved'";
    if($blood) $sql .= " AND blood_group='$blood'";
    if($location) $sql .= " AND city LIKE '%$location%'";
    
    $res = $conn->query($sql);
    while($row = $res->fetch_assoc()){
        $results[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search Donors</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Search Donors</h2>
    <form method="GET">
        <label>Blood Group</label>
        <input type="text" name="blood_group" value="<?= htmlspecialchars($blood) ?>">
        <label>City / Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($location) ?>">
        <button type="submit" name="search">Search</button>
    </form>

    <?php if($results): ?>
    <table>
        <tr>
            <th>Name</th><th>Blood Group</th><th>Email</th><th>Phone</th><th>City</th>
        </tr>
        <?php foreach($results as $row): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['blood_group'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['city'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
        <p>No donors found.</p>
    <?php endif; ?>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
