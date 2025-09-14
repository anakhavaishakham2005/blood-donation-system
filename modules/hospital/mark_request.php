<?php
// modules/hospital/mark_request.php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_role('hospital');

$hospital_id = $_SESSION['user_id'];
$request_id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($request_id <= 0 || !in_array($action, ['fulfilled','cancelled'])) {
    header("Location: /blood-donation-system/modules/hospital/dashboard.php");
    exit;
}

// ensure it belongs to this hospital
$stmt = $conn->prepare("SELECT request_id FROM blood_requests WHERE request_id = ? AND hospital_id = ?");
$stmt->bind_param('ii', $request_id, $hospital_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $stmt->close();
    die("Request not found or access denied.");
}
$stmt->close();

// update
$upd = $conn->prepare("UPDATE blood_requests SET status = ? WHERE request_id = ?");
$upd->bind_param('si', $action, $request_id);
$upd->execute();
$upd->close();

header("Location: /blood-donation-system/modules/hospital/dashboard.php");
exit;
