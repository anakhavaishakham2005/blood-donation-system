<?php
// modules/hospital/dashboard.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('hospital');

$hospital_id = $_SESSION['user_id'];

// hospital info
$stmt = $conn->prepare("SELECT name, email, phone, city, address FROM hospitals WHERE hospital_id = ?");
$stmt->bind_param('i', $hospital_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $city, $address);
$stmt->fetch();
$stmt->close();

// fetch this hospital's requests
$requests = [];
$stmt = $conn->prepare("SELECT request_id, blood_group, quantity, status, note, created_at FROM blood_requests WHERE hospital_id = ? ORDER BY created_at DESC");
$stmt->bind_param('i', $hospital_id);
$stmt->execute();
$stmt->bind_result($rid, $rbg, $rqty, $rstatus, $rnote, $rcreated);
while ($stmt->fetch()) {
    $requests[] = [
        'request_id'=>$rid,
        'blood_group'=>$rbg,
        'quantity'=>$rqty,
        'status'=>$rstatus,
        'note'=>$rnote,
        'created_at'=>$rcreated
    ];
}
$stmt->close();
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card mb-3">
      <div class="card-body">
        <h5><?=htmlspecialchars($name)?></h5>
        <p class="mb-1"><strong>Email:</strong> <?=htmlspecialchars($email)?></p>
        <p class="mb-1"><strong>Phone:</strong> <?=htmlspecialchars($phone)?></p>
        <p class="mb-1"><strong>City:</strong> <?=htmlspecialchars($city)?></p>
        <p class="mb-1"><strong>Address:</strong> <?=htmlspecialchars($address)?></p>
        <a class="btn btn-danger mt-2" href="/blood-donation-system/modules/hospital/create_request.php">Create Blood Request</a>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <h5>Your Requests</h5>
        <?php if(empty($requests)): ?>
          <p>No requests created yet.</p>
        <?php else: ?>
          <div class="list-group">
            <?php foreach($requests as $r): ?>
              <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1"><?=htmlspecialchars($r['blood_group'])?> — Qty: <?=htmlspecialchars($r['quantity'])?></h6>
                  <small><?=htmlspecialchars($r['created_at'])?></small>
                </div>
                <p class="mb-1"><?=nl2br(htmlspecialchars($r['note']))?></p>
                <small>Status: <strong><?=htmlspecialchars($r['status'])?></strong></small>
                <div class="mt-2">
                  <a class="btn btn-outline-primary btn-sm" href="/blood-donation-system/modules/hospital/view_request.php?id=<?=urlencode($r['request_id'])?>">View / Manage</a>
                  <?php if($r['status'] === 'pending'): ?>
                    <a class="btn btn-outline-success btn-sm" href="/blood-donation-system/modules/hospital/mark_request.php?id=<?=urlencode($r['request_id'])?>&action=fulfilled">Mark Fulfilled</a>
                    <a class="btn btn-outline-danger btn-sm" href="/blood-donation-system/modules/hospital/mark_request.php?id=<?=urlencode($r['request_id'])?>&action=cancelled">Cancel</a>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
