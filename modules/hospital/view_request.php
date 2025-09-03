<?php
// modules/hospital/view_request.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('hospital');

$hospital_id = $_SESSION['user_id'];
$request_id = (int)($_GET['id'] ?? 0);

if ($request_id <= 0) {
    header("Location: /blood-donation-system/modules/hospital/dashboard.php");
    exit;
}

// fetch request & ensure it belongs to this hospital
$stmt = $conn->prepare("SELECT request_id, blood_group, quantity, status, note, created_at FROM blood_requests WHERE request_id = ? AND hospital_id = ?");
$stmt->bind_param('ii', $request_id, $hospital_id);
$stmt->execute();
$stmt->bind_result($rid, $rbg, $rqty, $rstatus, $rnote, $rcreated);
if (!$stmt->fetch()) {
    $stmt->close();
    die("Request not found or access denied.");
}
$stmt->close();

// find matched donors (same logic as create_request)
$recipient = $rbg;
$acceptable = compatible_donors_for_recipient($recipient);
$matched = [];
if (!empty($acceptable)) {
    $in_list = sql_in_list($acceptable);
    $sql = "SELECT donor_id, name, email, phone, blood_group, last_donation, availability_status, city FROM donors WHERE blood_group IN ($in_list) AND availability_status = 1 ORDER BY name ASC LIMIT 200";
    $res = $conn->query($sql);
    if ($res) {
        while ($d = $res->fetch_assoc()) {
            if (is_donor_currently_available($d['last_donation'], $d['availability_status'])) {
                $matched[] = $d;
            }
        }
    }
}
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="card">
  <div class="card-body">
    <h4>Request #<?=htmlspecialchars($rid)?> — <?=htmlspecialchars($rbg)?> (Qty: <?=htmlspecialchars($rqty)?>)</h4>
    <p><strong>Status:</strong> <?=htmlspecialchars($rstatus)?> &nbsp; <small class="text-muted"><?=htmlspecialchars($rcreated)?></small></p>
    <?php if($rnote): ?><p><strong>Note:</strong> <?=nl2br(htmlspecialchars($rnote))?></p><?php endif; ?>

    <?php if(empty($matched)): ?>
      <div class="alert alert-warning">No currently available compatible donors found.</div>
    <?php else: ?>
      <h5>Matched Donors (<?=count($matched)?>)</h5>
      <table class="table table-sm">
        <thead>
          <tr><th>Name</th><th>Group</th><th>Phone</th><th>Email</th><th>City</th><th>Last Donation</th></tr>
        </thead>
        <tbody>
          <?php foreach($matched as $d): ?>
            <tr>
              <td><?=htmlspecialchars($d['name'])?></td>
              <td><?=htmlspecialchars($d['blood_group'])?></td>
              <td><?=htmlspecialchars($d['phone'])?></td>
              <td><?=htmlspecialchars($d['email'])?></td>
              <td><?=htmlspecialchars($d['city'])?></td>
              <td><?= $d['last_donation'] ? htmlspecialchars($d['last_donation']) : 'Never' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="small text-muted">You can contact donors directly using the phone/email shown. Notifications were automatically attempted when the request was created.</div>
    <?php endif; ?>

    <div class="mt-3">
      <a class="btn btn-link" href="/blood-donation-system/modules/hospital/dashboard.php">Back</a>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
