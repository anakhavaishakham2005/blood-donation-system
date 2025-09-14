<?php
// modules/hospital/create_request.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('hospital');

$errors = [];
$success = "";
$hospital_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$blood_group = strtoupper(trim($_POST['blood_group'] ?? ''));
	$quantity = (int)($_POST['quantity'] ?? 1);
	$note = trim($_POST['note'] ?? '');

	$valid_groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
	if (!in_array($blood_group, $valid_groups)) $errors[] = "Select a valid blood group.";
	if ($quantity <= 0) $errors[] = "Quantity must be at least 1.";

	if (empty($errors)) {
		$stmt = $conn->prepare("INSERT INTO blood_requests (hospital_id, blood_group, quantity, note) VALUES (?, ?, ?, ?)");
		$stmt->bind_param('isds', $hospital_id, $blood_group, $quantity, $note); // careful binding, but 's' for string ok; using 'isds' will still work in mysqli?
		// Use correct types: i s i s
		$stmt->close();
		// Re-prepare with correct types:
		$stmt = $conn->prepare("INSERT INTO blood_requests (hospital_id, blood_group, quantity, note) VALUES (?, ?, ?, ?)");
		$stmt->bind_param('isis', $hospital_id, $blood_group, $quantity, $note);
		if ($stmt->execute()) {
			$request_id = $stmt->insert_id;
			$success = "Request created successfully. Request ID: {$request_id}. The admin will process this request.";
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
    <h3>Create Blood Request</h3>

    <?php if(!empty($errors)): ?>
      <div class="alert alert-danger"><ul><?php foreach($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
    <?php endif; ?>

    <?php if($success): ?>
      <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
    <?php endif; ?>

    <form method="post" class="row g-3 col-md-6">
      <div class="col-md-4">
        <label class="form-label">Blood Group Needed</label>
        <select name="blood_group" class="form-select" required>
          <option value="">Select</option>
          <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g): 
            $sel = ((($_POST['blood_group'] ?? '') === $g) ? 'selected' : '');
            echo "<option value='{$g}' {$sel}>{$g}</option>";
          endforeach; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Quantity (units)</label>
        <input name="quantity" type="number" min="1" class="form-control" required value="<?=htmlspecialchars($_POST['quantity'] ?? '1')?>">
      </div>

      <div class="col-12">
        <label class="form-label">Note (optional)</label>
        <textarea name="note" class="form-control"><?=htmlspecialchars($_POST['note'] ?? '')?></textarea>
      </div>

      <div class="col-12">
        <button class="btn btn-danger">Create Request</button>
        <a href="/blood-donation-system/modules/hospital/dashboard.php" class="btn btn-link">Back</a>
      </div>
    </form>

  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
