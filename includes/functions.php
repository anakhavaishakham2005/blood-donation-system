<?php
// includes/functions.php
require_once __DIR__ . '/config.php';

/**
 * Compute availability based on last donation date:
 * Donor is available if availability_status=1 AND (last_donation is null or last_donation <= today - 90 days)
 */
function is_donor_currently_available($last_donation_date, $availability_status) {
    if ((int)$availability_status !== 1) return false;
    if (empty($last_donation_date)) return true;
    $last = new DateTime($last_donation_date);
    $limit = new DateTime();
    $limit->modify('-90 days');
    return ($last <= $limit);
}

/**
 * Get list of donor blood groups compatible for a given recipient blood group.
 * i.e., if hospital requests X (recipient), which donor groups are accepted?
 */
function compatible_donors_for_recipient($recipient_group) {
    $map = [
        'A+'  => ['A+','A-','O+','O-'],
        'A-'  => ['A-','O-'],
        'B+'  => ['B+','B-','O+','O-'],
        'B-'  => ['B-','O-'],
        'AB+' => ['A+','A-','B+','B-','AB+','AB-','O+','O-'], // universal recipient
        'AB-' => ['AB-','A-','B-','O-'],
        'O+'  => ['O+','O-'],
        'O-'  => ['O-']
    ];

    $recipient_group = strtoupper(trim($recipient_group));
    return $map[$recipient_group] ?? [];
}

/**
 * Escape an array of values for SQL IN clause (safe because values are internal mapping)
 */
function sql_in_list($arr) {
    global $conn;
    $clean = [];
    foreach ($arr as $val) {
        $clean[] = "'" . $conn->real_escape_string($val) . "'";
    }
    return implode(',', $clean);
}

/**
 * Simple mail wrapper (placeholder). If you want better reliability, integrate PHPMailer.
 */
function send_notification_email($to, $subject, $body) {
    // Basic PHP mail (may not work on local XAMPP without SMTP config)
    $headers = "From: no-reply@bloodbank.local\r\n";
    $headers .= "Content-type: text/plain; charset=utf-8\r\n";
    $sent = mail($to, $subject, $body, $headers);

    // store in notifications table
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (to_email, subject, body) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $to, $subject, $body);
    $stmt->execute();
    $stmt->close();

    return $sent;
}

/**
 * Require login helper
 */
function require_role($role) {
    // role = 'donor' | 'hospital' | 'admin'
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        header("Location: /blood-donation-system/index.php");
        exit;
    }
}
?>
