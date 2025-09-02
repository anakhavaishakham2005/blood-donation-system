<?php
include('verify.php');
include('../../includes/config.php');

$admin_id = $_SESSION['admin_id'];
$msg = '';

if(isset($_POST['update'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($password){
        $conn->query("UPDATE admins SET username='$username', password=MD5('$password') WHERE id=$admin_id");
    } else {
        $conn->query("UPDATE admins SET username='$username' WHERE id=$admin_id");
    }
    $msg = "Profile updated successfully!";
}

$res = $conn->query("SELECT * FROM admins WHERE id=$admin_id");
$admin = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
    <h2>Profile</h2>
    <?php if($msg) echo "<p>$msg</p>"; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" value="<?= $admin['username'] ?>" required>
        <label>Password (leave blank to keep unchanged)</label>
        <input type="password" name="password">
        <button type="submit" name="update">Update</button>
    </form>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
