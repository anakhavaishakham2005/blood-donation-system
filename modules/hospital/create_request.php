<?php
// modules/hospital/create_request.php
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
            $success = "Request created successfully. Request ID: {$request_id}.";

            // --- Matching donors ---
            $recipient = $blood_group;
            $acceptable = compatible_donors_for_recipient($recipient);
            if (!empty($acceptable)) {
                // Build SQL to get donors whose blood_group IN (...) AND availability_status=1 AND last_donation <= (today - 90 days OR last_donation IS NULL)
                $in_list = sql_in_list($acceptable); // safe mapping escape
                // We will fetch donors and filter in PHP with is_donor_currently_available for last_donation
                $sql = "SELECT donor_id, name, email, phone, blood_group, last_donation, availability_status, city FROM donors WHERE blood_group IN ($in_list) AND availability_status = 1";
                $res = $conn->query($sql);
                $matched = [];
                if ($res) {
                    while ($d = $res->fetch_assoc()) {
                        if (is_donor_currently_available($d['last_donation'], $d['availability_status'])) {
                            $matched[] = $d;
                        }
                    }
                }

                // Notify matched donors (limit so we don't spam)
                $notify_limit = 10; // change as needed
                $count_notified = 0;
                foreach ($matched as $donor) {
                    if ($count_notified >= $notify_limit) break;
                    $to = $donor['email'];
                    $subject = "Blood Donation Request: {$recipient} needed";
                    $body = "Hello {$donor['name']},\n\nThe hospital '{$_SESSION['user_name']}' has requested {$quantity} unit(s) of blood (Group: {$recipient}). You are identified as a compatible donor. If you are available and willing to donate, please contact the hospital or reply to this email.\n\nHospital: {$_SESSION['user_name']}\nRequest ID: {$request_id}\n\nThank you,\nBlood Bank Team";

                    // store in notifications and attempt to send
                    send_notification_email($to, $subject, $body);
                    $count_notified++;
                }

                $success .= " Found " . count($matched) . " compatible donors; notified top {$count_notified}.";
            } else {
                $success .= " No compatible donor groups found for {$recipient}.";
            }

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
        <button class="btn btn-danger">Create Request & Notify Donors</button>
        <a href="/blood-donation-system/modules/hospital/dashboard.php" class="btn btn-link">Back</a>
      </div>
    </form>

  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
