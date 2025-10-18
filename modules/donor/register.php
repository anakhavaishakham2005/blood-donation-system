<?php
// modules/donor/register.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $blood_group = strtoupper(trim($_POST['blood_group'] ?? ''));
    $dob = $_POST['dob'] ?? null;
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // basic validation
    if (!$name) $errors[] = "Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
    // enforce strong password: min 8 chars, at least 1 number and 1 special character
    if (!preg_match('/^(?=.*\\d)(?=.*[^a-zA-Z0-9]).{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters, include a number and a special character.";
    }
    if ($password !== $confirm) $errors[] = "Password confirmation does not match.";
    $valid_groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
    if (!in_array($blood_group, $valid_groups)) $errors[] = "Select a valid blood group.";

    // check duplicate email
    $stmt = $conn->prepare("SELECT donor_id FROM donors WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = "Email already registered.";
    $stmt->close();

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO donors (name, email, password, phone, blood_group, dob, city, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', $name, $email, $hash, $phone, $blood_group, $dob, $city, $address);
        if ($stmt->execute()) {
            $success = "Registration successful. You can login now.";
        } else {
            $errors[] = "DB error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="card">
  <div class="card-body">
    <h3 class="card-title">Donor Registration</h3>

    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul><?php foreach($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
      </div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" required value="<?=htmlspecialchars($_POST['name'] ?? '')?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
      </div>

      <div class="col-md-4">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required pattern="^(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}$" title="At least 8 characters, including a number and a special character">
      </div>

      <div class="col-md-4">
        <label class="form-label">Confirm Password</label>
        <input name="confirm" type="password" class="form-control" required>
      </div>

      <div class="col-md-4">
        <label class="form-label">Blood Group</label>
        <select name="blood_group" class="form-select" required>
          <option value="">Select</option>
          <?php
          $groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
          foreach ($groups as $g) {
              $sel = (($_POST['blood_group'] ?? '') === $g) ? 'selected' : '';
              echo "<option value='{$g}' {$sel}>{$g}</option>";
          }
          ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input name="phone" class="form-control" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>">
      </div>

      <div class="col-md-4">
        <label class="form-label">Date of Birth</label>
        <input name="dob" type="date" class="form-control" value="<?=htmlspecialchars($_POST['dob'] ?? '')?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">City</label>
        <input name="city" class="form-control" value="<?=htmlspecialchars($_POST['city'] ?? '')?>">
      </div>

      <div class="col-12">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control"><?=htmlspecialchars($_POST['address'] ?? '')?></textarea>
      </div>

      <div class="col-12">
        <button class="btn btn-danger">Register</button>
        <a href="/blood-donation-system/modules/donor/login.php" class="btn btn-link">Already have account? Login</a>
      </div>
    </form>

  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
