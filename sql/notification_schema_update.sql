-- Additional tables for notification system
-- Add these to your existing blood_bank.sql file

-- Add notification_type column to notifications table
ALTER TABLE notifications ADD COLUMN notification_type VARCHAR(50) DEFAULT 'general';

-- Create matching log table to track donor-request matches
CREATE TABLE IF NOT EXISTS matching_log (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  request_id INT NOT NULL,
  matched_donor_ids TEXT,
  matched_count INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (request_id) REFERENCES blood_requests(request_id) ON DELETE CASCADE
);

-- Create email templates table for customizable notifications
CREATE TABLE IF NOT EXISTS email_templates (
  template_id INT AUTO_INCREMENT PRIMARY KEY,
  template_name VARCHAR(100) NOT NULL UNIQUE,
  subject VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default email templates
INSERT INTO email_templates (template_name, subject, body) VALUES
('donor_match', 'Blood Donation Request - Your Help is Needed!', 
'Dear {donor_name},

A hospital in your area is urgently requesting blood donations.

REQUEST DETAILS:
Blood Group Required: {blood_group}
Quantity Needed: {quantity} units
Hospital: {hospital_name}
Hospital Address: {hospital_address}
Hospital Phone: {hospital_phone}

{additional_notes}

Your blood group ({donor_blood_group}) is compatible with this request.

If you are available and willing to donate, please contact the hospital directly.

Thank you for your willingness to help save lives!

Best regards,
Blood Bank Management System'),

('hospital_match', 'Blood Request Update - Donors Found!',
'Dear {hospital_name},

We have found potential donors for your blood request.

REQUEST DETAILS:
Blood Group: {blood_group}
Quantity Requested: {quantity} units

MATCHED DONORS:
{matched_donors_list}

Please contact these donors directly to coordinate the donation.

Best regards,
Blood Bank Management System'),

('admin_new_request', 'New Blood Request - Action Required',
'New Blood Request Received

Request ID: {request_id}
Hospital: {hospital_name}
Blood Group: {blood_group}
Quantity: {quantity} units
Status: {status}
Created: {created_at}

{additional_notes}

Please review and process this request in the admin panel.

Blood Bank Management System'),

('donation_complete', 'Thank You for Your Blood Donation!',
'Dear {donor_name},

Thank you for your generous blood donation!

DONATION DETAILS:
Donation Date: {donation_date}
Units Donated: {units}

{notes}

Your donation will help save lives. We appreciate your contribution to our community.

Please remember that you can donate again after 90 days from your last donation.

Best regards,
Blood Bank Management System');

-- Create notification preferences table for users
CREATE TABLE IF NOT EXISTS notification_preferences (
  preference_id INT AUTO_INCREMENT PRIMARY KEY,
  user_type ENUM('donor', 'hospital', 'admin') NOT NULL,
  user_id INT NOT NULL,
  email_notifications TINYINT(1) DEFAULT 1,
  sms_notifications TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add indexes for better performance
CREATE INDEX idx_notifications_type ON notifications(notification_type);
CREATE INDEX idx_notifications_sent_at ON notifications(sent_at);
CREATE INDEX idx_matching_log_request ON matching_log(request_id);
CREATE INDEX idx_matching_log_created ON matching_log(created_at);
CREATE INDEX idx_preferences_user ON notification_preferences(user_type, user_id);
