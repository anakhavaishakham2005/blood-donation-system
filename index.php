<?php include("includes/config.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blood Donation Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">Blood Bank</a>
  </div>
</nav>

<div class="container text-center mt-5">
  <h1 class="fw-bold text-danger">Welcome to the Blood Donation Management System</h1>
  <p class="lead">Bridging the gap between donors, hospitals, and those in need.</p>
  <div class="mt-4">
    <a href="modules/donor/login.php" class="btn btn-outline-danger btn-lg m-2">Donor Login</a>
    <a href="modules/hospital/login.php" class="btn btn-outline-danger btn-lg m-2">Hospital Login</a>
    <a href="modules/admin/login.php" class="btn btn-outline-dark btn-lg m-2">Admin Login</a>
  </div>
</div>

</body>
</html>
