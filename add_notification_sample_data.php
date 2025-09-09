<?php
// add_notification_sample_data.php
// This script adds sample data to test the notification system

require_once 'includes/config.php';

echo "<h2>Adding Sample Data for Notification Testing</h2>";

try {
    // Check if donors already exist
    echo "<h3>Checking Existing Donors...</h3>";
    $existing_donors = $conn->query("SELECT COUNT(*) as count FROM donors")->fetch_assoc()['count'];
    echo "Found $existing_donors existing donors<br>";
    
    if ($existing_donors == 0) {
        // Add sample donors
        echo "<h3>Adding Sample Donors...</h3>";
        
        $donors = [
            ['John Doe', 'anakhavaishakham2005@gmail.com', 'password123', '9876543210', 'A+', 'M', '1990-05-15', 'New York', '123 Main St', '2023-01-01', 1],
            ['Jane Smith', 'anakhavaishakham2005.cloud@gmail.com', 'password123', '9876543211', 'B+', 'F', '1988-03-20', 'Los Angeles', '456 Oak Ave', '2023-02-15', 1],
            ['Mike Johnson', 'mike.johnson@example.com', 'password123', '9876543212', 'O+', 'M', '1992-07-10', 'Chicago', '789 Pine St', '2023-03-01', 1],
            ['Sarah Wilson', 'sarah.wilson@example.com', 'password123', '9876543213', 'AB+', 'F', '1985-11-25', 'Houston', '321 Elm St', '2023-04-10', 1],
            ['David Brown', 'david.brown@example.com', 'password123', '9876543214', 'A-', 'M', '1991-09-05', 'Phoenix', '654 Maple Dr', '2023-05-20', 1],
            ['Lisa Davis', 'lisa.davis@example.com', 'password123', '9876543215', 'B-', 'F', '1987-12-12', 'Philadelphia', '987 Cedar Ln', '2023-06-05', 1],
            ['Tom Miller', 'tom.miller@example.com', 'password123', '9876543216', 'O-', 'M', '1993-04-18', 'San Antonio', '147 Birch Rd', '2023-07-15', 1],
            ['Amy Garcia', 'amy.garcia@example.com', 'password123', '9876543217', 'AB-', 'F', '1989-08-30', 'San Diego', '258 Spruce St', '2023-08-01', 1]
        ];
        
        $stmt = $conn->prepare("INSERT INTO donors (name, email, password, phone, blood_group, gender, dob, city, address, last_donation, availability_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($donors as $donor) {
            $stmt->bind_param('ssssssssssi', ...$donor);
            if ($stmt->execute()) {
                echo "✓ Added donor: {$donor[0]} ({$donor[4]}) - {$donor[1]}<br>";
            } else {
                echo "✗ Failed to add donor: {$donor[0]} - " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    } else {
        echo "✓ Donors already exist, skipping donor creation<br>";
    }
    
    // Check if hospitals already exist
    echo "<h3>Checking Existing Hospitals...</h3>";
    $existing_hospitals = $conn->query("SELECT COUNT(*) as count FROM hospitals")->fetch_assoc()['count'];
    echo "Found $existing_hospitals existing hospitals<br>";
    
    if ($existing_hospitals == 0) {
        // Add sample hospitals
        echo "<h3>Adding Sample Hospitals...</h3>";
        
        $hospitals = [
            ['City General Hospital', 'city.general@hospital.com', 'password123', '555-0101', '123 Hospital Ave, New York', 'New York'],
            ['Metro Medical Center', 'metro.medical@hospital.com', 'password123', '555-0102', '456 Medical Blvd, Los Angeles', 'Los Angeles'],
            ['Regional Health Center', 'regional.health@hospital.com', 'password123', '555-0103', '789 Health St, Chicago', 'Chicago']
        ];
        
        $stmt = $conn->prepare("INSERT INTO hospitals (name, email, password, phone, address, city) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($hospitals as $hospital) {
            $stmt->bind_param('ssssss', ...$hospital);
            if ($stmt->execute()) {
                echo "✓ Added hospital: {$hospital[0]} - {$hospital[1]}<br>";
            } else {
                echo "✗ Failed to add hospital: {$hospital[0]} - " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    } else {
        echo "✓ Hospitals already exist, skipping hospital creation<br>";
    }
    
    // Get hospital IDs first
    echo "<h3>Getting Hospital IDs...</h3>";
    $hospital_ids = [];
    $hospital_result = $conn->query("SELECT hospital_id, name FROM hospitals ORDER BY hospital_id");
    while ($row = $hospital_result->fetch_assoc()) {
        $hospital_ids[] = $row['hospital_id'];
        echo "✓ Found hospital: {$row['name']} (ID: {$row['hospital_id']})<br>";
    }
    
    if (empty($hospital_ids)) {
        echo "✗ No hospitals found. Cannot add blood requests.<br>";
    } else {
        // Check if blood requests already exist
        echo "<h3>Checking Existing Blood Requests...</h3>";
        $existing_requests = $conn->query("SELECT COUNT(*) as count FROM blood_requests")->fetch_assoc()['count'];
        echo "Found $existing_requests existing blood requests<br>";
        
        if ($existing_requests == 0) {
            // Add sample blood requests using actual hospital IDs
            echo "<h3>Adding Sample Blood Requests...</h3>";
            
            $requests = [
                [$hospital_ids[0], 'A+', 2, 'Urgent request for surgery patient'],
                [$hospital_ids[1], 'B+', 1, 'Emergency blood transfusion needed'],
                [$hospital_ids[2], 'O+', 3, 'Multiple patients requiring blood'],
                [$hospital_ids[0], 'AB+', 1, 'Rare blood group needed'],
                [$hospital_ids[1], 'A-', 2, 'Patient with specific blood type requirement']
            ];
            
            $stmt = $conn->prepare("INSERT INTO blood_requests (hospital_id, blood_group, quantity, note) VALUES (?, ?, ?, ?)");
            
            foreach ($requests as $request) {
                $stmt->bind_param('isis', ...$request);
                if ($stmt->execute()) {
                    echo "✓ Added request: {$request[1]} ({$request[2]} units) - Request ID: {$stmt->insert_id}<br>";
                } else {
                    echo "✗ Failed to add request: {$request[1]} - " . $stmt->error . "<br>";
                }
            }
            $stmt->close();
        } else {
            echo "✓ Blood requests already exist, skipping request creation<br>";
        }
    }
    
    // Check if admin already exists
    echo "<h3>Checking Existing Admins...</h3>";
    $existing_admins = $conn->query("SELECT COUNT(*) as count FROM admins")->fetch_assoc()['count'];
    echo "Found $existing_admins existing admins<br>";
    
    if ($existing_admins == 0) {
        // Add sample admin
        echo "<h3>Adding Sample Admin...</h3>";
        
        $admin_stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
        $admin_data = ['admin', 'anakhavaishakham2005@gmail.com', 'admin123'];
        $admin_stmt->bind_param('sss', ...$admin_data);
        
        if ($admin_stmt->execute()) {
            echo "✓ Added admin: {$admin_data[0]} - {$admin_data[1]}<br>";
        } else {
            echo "✗ Failed to add admin: {$admin_data[0]} - " . $admin_stmt->error . "<br>";
        }
        $admin_stmt->close();
    } else {
        echo "✓ Admins already exist, skipping admin creation<br>";
    }
    
    echo "<h3>Sample Data Added Successfully!</h3>";
    echo "<p><strong>Test the notification system:</strong></p>";
    echo "<ul>";
    echo "<li><a href='test_notification_system.php'>Run Notification Test</a></li>";
    echo "<li><a href='modules/admin/manage_requests.php'>Admin - Manage Requests</a></li>";
    echo "<li><a href='modules/admin/notifications.php'>Admin - View Notifications</a></li>";
    echo "<li><a href='modules/hospital/create_request.php'>Hospital - Create Request</a></li>";
    echo "</ul>";
    
    echo "<h3>Email Testing Notes:</h3>";
    echo "<p><strong>For XAMPP Local Development:</strong></p>";
    echo "<ul>";
    echo "<li>PHP mail() function may not work on local XAMPP without SMTP configuration</li>";
    echo "<li>Check XAMPP mail settings in php.ini</li>";
    echo "<li>All notifications are logged in the 'notifications' table regardless of email success</li>";
    echo "<li>For production, configure proper SMTP settings</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a></p>";
?>
