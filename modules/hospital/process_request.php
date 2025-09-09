<?php
include("../../includes/config.php");

// Get donor_id from POST or GET
$donor_id = isset($_POST['donor_id']) ? intval($_POST['donor_id']) : 0;

if ($donor_id > 0) {
    $stmt = $conn->prepare("SELECT name, email FROM donors WHERE donor_id = ?");
    $stmt->bind_param("i", $donor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $donor = $result->fetch_assoc();

    if ($donor) {
        $donorEmail = $donor['email'];
        $subject = "Blood Donation Request Matched";
        $body = "Dear " . $donor['name'] . ",\n\nYou have been matched with a blood donation request. Please log in for details.";
        $headers = "From: no-reply@bloodbank.local\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($donorEmail, $subject, $body, $headers)) {
            // Log notification in DB
            $stmt2 = $conn->prepare("INSERT INTO notifications (to_email, subject, body) VALUES (?, ?, ?)");
            $stmt2->bind_param("sss", $donorEmail, $subject, $body);
            $stmt2->execute();
        }
    }
} else {
    echo "No donor selected.";
}
header("Location: process_request.php?donor_id=$matched_donor_id");
?>

<form action="process_request.php" method="post">
  <input type="hidden" name="donor_id" value="<?php echo $matched_donor_id; ?>">
  <button type="submit">Notify Donor</button>
</form>

