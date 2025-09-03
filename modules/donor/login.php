<?php
// modules/donor/login.php
session_start();
require_once __DIR__ . '/../../includes/config.php';

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = "Enter a valid email.";

    if (!$err) {
        $stmt = $conn->prepare("SELECT donor_id, name, password, last_donation, availability_status FROM donors WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($donor_id, $name, $hash, $last_donation, $avail);
        if ($stmt->fetch()) {
            if (password_verify($password, $hash)) {
                // login success
                $_SESSION['user_id'] = $donor_id;
                $_SESSION['user_role'] = 'donor';
                $_SESSION['user_name'] = $name;
                $_SESSION['last_donation'] = $last_donation;
                $_SESSION['availability_status'] = $avail;
                header("Location: /blood-donation-system/modules/donor/dashboard.php");
                exit;
            } else {
                $err = "Incorrect password.";
            }
        } else {
            $err = "No donor found with this email.";
        }
        $stmt->close();
    }
}
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="card">
  <div class="card-body">
    <h3 class="card-title">Donor Login</h3>
    <?php if($err): ?>
      <div class="alert alert-danger"><?=htmlspecialchars($err)?></div>
    <?php endif; ?>
    <form method="post" class="row g-3 col-md-6">
      <div class="col-12">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
      </div>
      <div class="col-12">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <div class="col-12">
        <button class="btn btn-danger">Login</button>
        <a href="/blood-donation-system/modules/donor/register.php" class="btn btn-link">Register</a>
      </div>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
