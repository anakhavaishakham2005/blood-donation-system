<?php
// modules/donor/update_availability.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('donor');

$donor_id = $_SESSION['user_id'];

// Fetch current donor status and last donation date
$stmt = $conn->prepare("SELECT last_donation, availability_status FROM donors WHERE donor_id = ?");
$stmt->bind_param('i', $donor_id);
$stmt->execute();
$stmt->bind_result($last_donation, $availability_status);
$stmt->fetch();
$stmt->close();

$desired = isset($_POST['set']) ? trim($_POST['set']) : '';

// Helper: has it been at least 90 days since last donation?
$ninety_days_ok = false;
if (empty($last_donation)) {
    $ninety_days_ok = true; // never donated -> can opt-in
} else {
    try {
        $last = new DateTime($last_donation);
        $limit = new DateTime();
        $limit->modify('-90 days');
        $ninety_days_ok = ($last <= $limit);
    } catch (Exception $e) {
        $ninety_days_ok = false;
    }
}

$msg = '';
$err = '';

if ($desired === '1') {
    // enabling availability requires 90-day rule
    if ($ninety_days_ok) {
        if ((int)$availability_status !== 1) {
            $stmt = $conn->prepare("UPDATE donors SET availability_status = 1 WHERE donor_id = ?");
            $stmt->bind_param('i', $donor_id);
            if ($stmt->execute()) {
                $msg = 'Availability enabled.';
            } else {
                $err = 'Failed to update availability.';
            }
            $stmt->close();
        } else {
            $msg = 'You are already marked as available.';
        }
    } else {
        $err = 'You can enable availability only after 90 days from your last donation.';
    }
} elseif ($desired === '0') {
    // disabling is always allowed
    if ((int)$availability_status !== 0) {
        $stmt = $conn->prepare("UPDATE donors SET availability_status = 0 WHERE donor_id = ?");
        $stmt->bind_param('i', $donor_id);
        if ($stmt->execute()) {
            $msg = 'Availability disabled.';
        } else {
            $err = 'Failed to update availability.';
        }
        $stmt->close();
    } else {
        $msg = 'You are already marked as not available.';
    }
} else {
    $err = 'Invalid request.';
}

if ($err) $_SESSION['flash_error'] = $err;
if ($msg) $_SESSION['flash_success'] = $msg;

header("Location: /blood-donation-system/modules/donor/dashboard.php");
exit;
?>


