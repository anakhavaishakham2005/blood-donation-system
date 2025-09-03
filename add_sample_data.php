<?php
// add_sample_data.php - Add sample data to test the system
require_once __DIR__ . '/includes/config.php';

echo "<h2>Adding Sample Data</h2>";

// Add sample donors
$donors = [
    ['John Doe', 'john@example.com', 'password123', '1234567890', 'A+', 'M', '1990-01-01', 'New York', '123 Main St'],
    ['Jane Smith', 'jane@example.com', 'password123', '0987654321', 'O+', 'F', '1985-05-15', 'Los Angeles', '456 Oak Ave'],
    ['Mike Johnson', 'mike@example.com', 'password123', '5551234567', 'B+', 'M', '1992-08-20', 'Chicago', '789 Pine Rd'],
    ['Sarah Wilson', 'sarah@example.com', 'password123', '4449876543', 'AB+', 'F', '1988-12-10', 'Houston', '321 Elm St'],
    ['David Brown', 'david@example.com', 'password123', '3335557777', 'A-', 'M', '1995-03-25', 'Phoenix', '654 Maple Dr']
];

echo "<h3>Adding Sample Donors:</h3>";
$donor_ids = [];
foreach ($donors as $donor) {
    $hash = password_hash($donor[2], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO donors (name, email, password, phone, blood_group, gender, dob, city, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssss', $donor[0], $donor[1], $hash, $donor[3], $donor[4], $donor[5], $donor[6], $donor[7], $donor[8]);
    
    if ($stmt->execute()) {
        $donor_ids[] = $conn->insert_id;
        echo "Added donor: {$donor[0]} ({$donor[4]}) - ID: " . $conn->insert_id . "<br>";
    } else {
        echo "Error adding donor {$donor[0]}: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Add sample hospitals
$hospitals = [
    ['City General Hospital', 'city@hospital.com', 'password123', '555-0101', '100 Hospital Dr', 'New York'],
    ['Regional Medical Center', 'regional@hospital.com', 'password123', '555-0202', '200 Medical Blvd', 'Los Angeles'],
    ['Community Health Center', 'community@hospital.com', 'password123', '555-0303', '300 Health Ave', 'Chicago']
];

echo "<h3>Adding Sample Hospitals:</h3>";
$hospital_ids = [];
foreach ($hospitals as $hospital) {
    $hash = password_hash($hospital[2], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO hospitals (name, email, password, phone, address, city) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $hospital[0], $hospital[1], $hash, $hospital[3], $hospital[4], $hospital[5]);
    
    if ($stmt->execute()) {
        $hospital_ids[] = $conn->insert_id;
        echo "Added hospital: {$hospital[0]} - ID: " . $conn->insert_id . "<br>";
    } else {
        echo "Error adding hospital {$hospital[0]}: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Add sample blood requests using actual hospital IDs
echo "<h3>Adding Sample Blood Requests:</h3>";
if (!empty($hospital_ids)) {
    $requests = [
        [$hospital_ids[0], 'A+', 2, 'Emergency request'],
        [$hospital_ids[1], 'O+', 1, 'Scheduled surgery'],
        [$hospital_ids[2], 'B+', 3, 'Trauma case']
    ];

    foreach ($requests as $request) {
        $stmt = $conn->prepare("INSERT INTO blood_requests (hospital_id, blood_group, quantity, note) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isis', $request[0], $request[1], $request[2], $request[3]);
        
        if ($stmt->execute()) {
            echo "Added blood request: {$request[1]} x{$request[2]} for hospital ID {$request[0]}<br>";
        } else {
            echo "Error adding blood request: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
} else {
    echo "No hospitals were added, skipping blood requests.<br>";
}

// Add sample donations using actual donor IDs
echo "<h3>Adding Sample Donations:</h3>";
if (!empty($donor_ids)) {
    $donations = [
        [$donor_ids[0], 1, '2024-01-15', 1, 'Regular donation'],
        [$donor_ids[1], 1, '2024-02-20', 1, 'Emergency response'],
        [$donor_ids[2], 1, '2024-03-10', 1, 'Scheduled donation']
    ];

    foreach ($donations as $donation) {
        $stmt = $conn->prepare("INSERT INTO donations (donor_id, admin_id, donation_date, units, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iisis', $donation[0], $donation[1], $donation[2], $donation[3], $donation[4]);
        
        if ($stmt->execute()) {
            echo "Added donation: Donor {$donation[0]} on {$donation[2]}<br>";
        } else {
            echo "Error adding donation: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
} else {
    echo "No donors were added, skipping donations.<br>";
}

// Add sample blood inventory
echo "<h3>Adding Sample Blood Inventory:</h3>";
$inventory = [
    ['A+', 10],
    ['A-', 5],
    ['B+', 8],
    ['B-', 3],
    ['AB+', 4],
    ['AB-', 2],
    ['O+', 15],
    ['O-', 6]
];

foreach ($inventory as $item) {
    $stmt = $conn->prepare("INSERT INTO blood_inventory (blood_group, units) VALUES (?, ?)");
    $stmt->bind_param('si', $item[0], $item[1]);
    
    if ($stmt->execute()) {
        echo "Added inventory: {$item[0]} - {$item[1]} units<br>";
    } else {
        echo "Error adding inventory: " . $stmt->error . "<br>";
    }
    $stmt->close();
}

echo "<br><strong>Sample data added successfully!</strong><br>";
echo "<a href='check_database_data.php'>Check Database Content</a><br>";
echo "<a href='modules/admin/dashboard.php'>Go to Admin Dashboard</a>";
?>
