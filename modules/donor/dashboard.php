<?php
// modules/donor/dashboard.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('donor');

$donor_id = $_SESSION['user_id'];

// fetch donor info
$stmt = $conn->prepare("SELECT name, email, phone, blood_group, last_donation, availability_status, city FROM donors WHERE donor_id = ?");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $blood_group, $last_donation, $availability_status, $city);
$stmt->fetch();
$stmt->close();

// compute availability
$available_now = is_donor_currently_available($last_donation, $availability_status);

// donation history
$hist = [];
$stmt = $conn->prepare("SELECT donation_date, units, notes FROM donations WHERE donor_id = ? ORDER BY donation_date DESC");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$stmt->bind_result($d_date, $d_units, $d_notes);
while ($stmt->fetch()) {
    $hist[] = ['date'=>$d_date,'units'=>$d_units,'notes'=>$d_notes];
}
$stmt->close();

// show current open requests compatible for this donor's blood group (so donor can see where they might be called)
$compatible_list = [];
// We want to find requests where requested recipient group accepts donor's group.
// So we invert: for each recipient group, if donor_group in compatible list of that recipient, then that request is compatible.
$reqs = [];
$res = $conn->query("SELECT request_id, hospital_id, blood_group, quantity, status, created_at FROM blood_requests WHERE status='pending' ORDER BY created_at DESC LIMIT 50");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $recipient = strtoupper($r['blood_group']);
        $comp = compatible_donors_for_recipient($recipient); // donor groups acceptable
        if (in_array($blood_group, $comp)) {
            // fetch hospital name
            $hstmt = $conn->prepare("SELECT name, city FROM hospitals WHERE hospital_id = ?");
            $hstmt->bind_param('i', $r['hospital_id']);
            $hstmt->execute();
            $hstmt->bind_result($hname, $hcity);
            $hstmt->fetch();
            $hstmt->close();

            $r['hospital_name'] = $hname ?? 'Unknown';
            $r['hospital_city'] = $hcity ?? '';
            $reqs[] = $r;
        }
    }
}

?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>

<div class="row">
  <div class="col-md-4">
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title"><?=htmlspecialchars($name)?></h5>
        <p class="mb-1"><strong>Email:</strong> <?=htmlspecialchars($email)?></p>
        <p class="mb-1"><strong>Phone:</strong> <?=htmlspecialchars($phone)?></p>
        <p class="mb-1"><strong>Blood Group:</strong> <?=htmlspecialchars($blood_group)?></p>
        <p class="mb-1"><strong>City:</strong> <?=htmlspecialchars($city)?></p>
        <p class="mb-1"><strong>Last Donation:</strong> <?= $last_donation ? htmlspecialchars($last_donation) : 'Never' ?></p>
        <p class="mb-1"><strong>Availability:</strong>
          <?php if($available_now): ?>
            <span class="badge bg-success">Available</span>
          <?php else: ?>
            <span class="badge bg-secondary">Not Available</span>
          <?php endif; ?>
        </p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6>Actions</h6>
        <form method="post" action="/blood-donation-system/modules/donor/update_availability.php">
          <!-- This endpoint toggles availability manually; create it later -->
          <button class="btn btn-outline-danger btn-sm" disabled>Toggle Availability (coming)</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card mb-3">
      <div class="card-body">
        <h5>Donation History</h5>
        <?php if(empty($hist)): ?>
          <p>No donation history yet.</p>
        <?php else: ?>
          <ul class="list-group">
            <?php foreach($hist as $h): ?>
              <li class="list-group-item">
                <strong><?=htmlspecialchars($h['date'])?></strong> — Units: <?=htmlspecialchars($h['units'])?> 
                <?php if($h['notes']): ?>
                  <div class="small text-muted"><?=htmlspecialchars($h['notes'])?></div>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5>Open Requests That You're Compatible With</h5>
        <?php if(empty($reqs)): ?>
          <p>No matching requests right now. We'll notify you when hospitals need your group.</p>
        <?php else: ?>
          <div class="list-group">
            <?php foreach($reqs as $r): ?>
              <div class="list-group-item">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1"><?=htmlspecialchars($r['hospital_name'])?> <small class="text-muted"><?=htmlspecialchars($r['hospital_city'])?></small></h6>
                  <small><?=htmlspecialchars($r['created_at'])?></small>
                </div>
                <p class="mb-1">Requested: <strong><?=htmlspecialchars($r['blood_group'])?></strong> — Quantity: <?=htmlspecialchars($r['quantity'])?></p>
                <small class="text-muted">Request status: <?=htmlspecialchars($r['status'])?></small>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
