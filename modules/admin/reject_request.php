<?php
include('verify.php');
include('../../includes/config.php');

$request_id = (int)($_GET['request_id'] ?? 0);
if ($request_id > 0) {
	$stmt = $conn->prepare("UPDATE blood_requests SET status='cancelled' WHERE request_id = ? AND status='pending'");
	$stmt->bind_param('i', $request_id);
	$stmt->execute();
	$stmt->close();
}
header('Location: manage_requests.php');
exit;
