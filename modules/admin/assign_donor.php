<?php
// modules/admin/assign_donor.php
include('verify.php');
require_once __DIR__ . '/../../includes/functions.php';

$request_id = (int)($_GET['request_id'] ?? 0);
$errors = [];
$success = '';

if ($request_id <= 0) {
	header('Location: manage_requests.php');
	exit;
}

// Fetch request details
$stmt = $conn->prepare("SELECT r.request_id, r.hospital_id, r.blood_group, r.quantity, r.status, h.name AS hospital_name FROM blood_requests r JOIN hospitals h ON r.hospital_id=h.hospital_id WHERE r.request_id = ?");
if (!$stmt) {
	die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param('i', $request_id);
$stmt->execute();
$stmt->bind_result($rid, $hospital_id, $rbg, $rqty, $rstatus, $hospital_name);
if (!$stmt->fetch()) {
	$stmt->close();
	die('Request not found');
}
$stmt->close();

if ($rstatus !== 'pending') {
	$errors[] = 'This request is already processed.';
}

// Build compatible donors list
$acceptable = compatible_donors_for_recipient($rbg);
$donors = [];
if (!empty($acceptable)) {
	$in_list = sql_in_list($acceptable);
	$sql = "SELECT donor_id, name, email, phone, blood_group, last_donation, availability_status, city FROM donors WHERE blood_group IN ($in_list) AND availability_status = 1 ORDER BY name ASC LIMIT 300";
	$res = $conn->query($sql);
	if ($res) {
		while ($d = $res->fetch_assoc()) {
			if (is_donor_currently_available($d['last_donation'], $d['availability_status'])) {
				$donors[] = $d;
			}
		}
	} else {
		$errors[] = 'Failed to fetch donors: ' . $conn->error;
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
	$selected_donor_id = (int)($_POST['donor_id'] ?? 0);
	$donation_date = trim($_POST['donation_date'] ?? '');
	$units = (int)($_POST['units'] ?? 1);
	$notes = trim($_POST['notes'] ?? '');

	if ($selected_donor_id <= 0) $errors[] = 'Please select a donor.';
	if (!$donation_date) $errors[] = 'Please set a donation date.';
	if ($units <= 0) $errors[] = 'Units must be at least 1.';

	if (empty($errors)) {
		$conn->begin_transaction();
		try {
			$admin_id = $_SESSION['user_id'];

			// Insert donations record with request linkage if column exists; otherwise without it
			$ins = $conn->prepare("INSERT INTO donations (donor_id, admin_id, donation_date, units, notes, request_id) VALUES (?, ?, ?, ?, ?, ?)");
			if ($ins) {
				$ins->bind_param('iisisi', $selected_donor_id, $admin_id, $donation_date, $units, $notes, $request_id);
				$ins->execute();
				$ins->close();
			} else {
				// Fallback: try without request_id column
				$ins2 = $conn->prepare("INSERT INTO donations (donor_id, admin_id, donation_date, units, notes) VALUES (?, ?, ?, ?, ?)");
				if (!$ins2) {
					throw new Exception('Prepare donations failed: ' . $conn->error);
				}
				$ins2->bind_param('iisis', $selected_donor_id, $admin_id, $donation_date, $units, $notes);
				$ins2->execute();
				$ins2->close();
			}

			// Update donor last_donation and availability (set unavailable until cooldown)
			$updDonor = $conn->prepare("UPDATE donors SET last_donation = ?, availability_status = 0 WHERE donor_id = ?");
			if (!$updDonor) {
				throw new Exception('Prepare donor update failed: ' . $conn->error);
			}
			$updDonor->bind_param('si', $donation_date, $selected_donor_id);
			$updDonor->execute();
			$updDonor->close();

			// Mark request fulfilled and record assigned donor if column exists; otherwise just fulfill
			$updReq = $conn->prepare("UPDATE blood_requests SET status='fulfilled', assigned_donor_id = ? WHERE request_id = ?");
			if ($updReq) {
				$updReq->bind_param('ii', $selected_donor_id, $request_id);
				$updReq->execute();
				$updReq->close();
			} else {
				$updReq2 = $conn->prepare("UPDATE blood_requests SET status='fulfilled' WHERE request_id = ?");
				if (!$updReq2) {
					throw new Exception('Prepare request update failed: ' . $conn->error);
				}
				$updReq2->bind_param('i', $request_id);
				$updReq2->execute();
				$updReq2->close();
			}

			$conn->commit();
			$success = 'Donor assigned and request marked fulfilled.';
		} catch (Throwable $e) {
			$conn->rollback();
			$errors[] = 'Transaction failed: ' . $e->getMessage();
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Assign Donor</title>
	<link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<?php include('../../includes/header.php'); ?>
<div class="container">
	<h2>Assign Donor for Request #<?=htmlspecialchars($rid)?> (<?=htmlspecialchars($rbg)?>, Qty <?=htmlspecialchars($rqty)?>) — Hospital: <?=htmlspecialchars($hospital_name)?></h2>
	<?php if(!empty($errors)): ?>
		<div class="alert alert-danger"><?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?></div>
	<?php endif; ?>
	<?php if($success): ?>
		<div class="alert alert-success"><?=htmlspecialchars($success)?> <a href="manage_requests.php">Back to list</a></div>
	<?php endif; ?>

	<?php if(empty($success) && $rstatus === 'pending'): ?>

    <?php if(empty($donors)): ?>
        <div class="alert alert-warning">No currently available compatible donors found.</div>
    <?php else: ?>
        <h4>Matched Donors (<?=count($donors)?>)</h4>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Blood Group</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Last Donation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($donors as $d): ?>
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
    <?php endif; ?>

    <form method="post">
        <label>Choose Donor</label>
        <select name="donor_id" required>
            <option value="">Select a donor</option>
            <?php foreach($donors as $d): ?>
                <option value="<?=htmlspecialchars($d['donor_id'])?>"><?=htmlspecialchars($d['name'])?> — <?=htmlspecialchars($d['blood_group'])?> — <?=htmlspecialchars($d['city'])?> <?php if($d['last_donation']) echo '(Last: '.htmlspecialchars($d['last_donation']).')'; ?></option>
            <?php endforeach; ?>
        </select>

        <div style="margin-top:8px;">
            <label>Donation Date</label>
            <input type="date" name="donation_date" required>
        </div>
        <!-- <div style="margin-top:8px;">
            <label>Units</label>
            <input type="number" name="units" min="1" value="1" required>
        </div> -->
        <div style="margin-top:8px;">
            <label>Notes (optional)</label>
            <textarea name="notes"></textarea>
        </div>
        <div style="margin-top:12px;">
            <button type="submit">Save Assignment</button>
            <a href="manage_requests.php">Cancel</a>
        </div>
    </form>
<?php endif; ?>
</div>
<?php include('../../includes/footer.php'); ?>
</body>
</html>
