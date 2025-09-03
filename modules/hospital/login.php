<?php
// modules/hospital/login.php
session_start();
require_once __DIR__ . '/../../includes/config.php';

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = "Enter a valid email.";

    if (!$err) {
        $stmt = $conn->prepare("SELECT hospital_id, name, password FROM hospitals WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($hospital_id, $name, $hash);
        if ($stmt->fetch()) {
            // Debug: Check if password verification is working
            $password_verify_result = password_verify($password, $hash);
            if ($password_verify_result) {
                $_SESSION['user_id'] = $hospital_id;
                $_SESSION['user_role'] = 'hospital';
                $_SESSION['user_name'] = $name;
                header("Location: /blood-donation-system/modules/hospital/dashboard.php");
                exit;
            } else {
                $err = "Incorrect password. (Debug: password_verify returned false)";
            }
        } else {
            $err = "No hospital found with this email.";
        }
        $stmt->close();
    }
}
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="card">
  <div class="card-body">
    <h3 class="card-title">Hospital Login</h3>
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
        <a href="/blood-donation-system/modules/hospital/register.php" class="btn btn-link">Register</a>
      </div>
    </form>
  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
