<?php
// check_database_data.php - Temporary script to check database content
require_once __DIR__ . '/includes/config.php';

echo "<h2>Database Content Check</h2>";

// Check donors table
echo "<h3>Donors Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM donors");
$row = $result->fetch_assoc();
echo "Total donors: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    $result = $conn->query("SELECT donor_id, name, email, blood_group FROM donors LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['donor_id']}, Name: {$row['name']}, Email: {$row['email']}, Blood Group: {$row['blood_group']}<br>";
    }
} else {
    echo "No donors found in database.<br>";
}

// Check hospitals table
echo "<h3>Hospitals Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM hospitals");
$row = $result->fetch_assoc();
echo "Total hospitals: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    $result = $conn->query("SELECT hospital_id, name, email FROM hospitals LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['hospital_id']}, Name: {$row['name']}, Email: {$row['email']}<br>";
    }
} else {
    echo "No hospitals found in database.<br>";
}

// Check blood_requests table
echo "<h3>Blood Requests Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM blood_requests");
$row = $result->fetch_assoc();
echo "Total blood requests: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    $result = $conn->query("SELECT request_id, hospital_id, blood_group, quantity, status FROM blood_requests LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['request_id']}, Hospital ID: {$row['hospital_id']}, Blood Group: {$row['blood_group']}, Quantity: {$row['quantity']}, Status: {$row['status']}<br>";
    }
} else {
    echo "No blood requests found in database.<br>";
}

// Check donations table
echo "<h3>Donations Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM donations");
$row = $result->fetch_assoc();
echo "Total donations: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    $result = $conn->query("SELECT donation_id, donor_id, donation_date, units FROM donations LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['donation_id']}, Donor ID: {$row['donor_id']}, Date: {$row['donation_date']}, Units: {$row['units']}<br>";
    }
} else {
    echo "No donations found in database.<br>";
}

// Check blood_inventory table
echo "<h3>Blood Inventory Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM blood_inventory");
$row = $result->fetch_assoc();
echo "Total inventory records: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    $result = $conn->query("SELECT inventory_id, blood_group, units FROM blood_inventory");
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['inventory_id']}, Blood Group: {$row['blood_group']}, Units: {$row['units']}<br>";
    }
} else {
    echo "No inventory records found in database.<br>";
}

// Check admins table
echo "<h3>Admins Table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM admins");
$row = $result->fetch_assoc();
echo "Total admins: " . $row['count'] . "<br>";

if ($row['count'] > 0) {
    $result = $conn->query("SELECT admin_id, username, email FROM admins");
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['admin_id']}, Username: {$row['username']}, Email: {$row['email']}<br>";
    }
} else {
    echo "No admins found in database.<br>";
}

echo "<br><strong>Note:</strong> If tables are empty, you need to register some donors and hospitals first, or add some sample data.";
?>
