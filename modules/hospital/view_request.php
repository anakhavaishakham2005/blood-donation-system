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
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="card">
  <div class="card-body">
    <h4>Request #<?=htmlspecialchars($rid)?> — <?=htmlspecialchars($rbg)?> (Qty: <?=htmlspecialchars($rqty)?>)</h4>
    <p><strong>Status:</strong> <?=htmlspecialchars($rstatus)?> &nbsp; <small class="text-muted"><?=htmlspecialchars($rcreated)?></small></p>
    <?php if($rnote): ?><p><strong>Note:</strong> <?=nl2br(htmlspecialchars($rnote))?></p><?php endif; ?>

    <div class="alert alert-info">The admin team will coordinate donors and supply the required units. You can update the request status to fulfilled or cancelled from your dashboard when appropriate.</div>

    <div class="mt-3">
      <a class="btn btn-link" href="/blood-donation-system/modules/hospital/dashboard.php">Back</a>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
