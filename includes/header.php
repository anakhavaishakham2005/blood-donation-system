<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Blood Donation System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/blood-donation-system/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand" href="/blood-donation-system/index.php">BloodBank</a>
    <div>
      <?php if(isset($_SESSION['user_name'])): ?>
        <span class="text-white me-3">Hello, <?=htmlspecialchars($_SESSION['user_name'])?></span>
        <a class="btn btn-outline-light btn-sm" href="/blood-donation-system/logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-outline-light btn-sm" href="/blood-donation-system/index.php">Home</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<div class="container my-4">
