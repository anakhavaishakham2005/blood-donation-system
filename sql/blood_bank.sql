-- blood_bank.sql
CREATE DATABASE IF NOT EXISTS blood_bank_db;
USE blood_bank_db;

-- Donors
CREATE TABLE IF NOT EXISTS donors (
  donor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  blood_group VARCHAR(5) NOT NULL,
  gender ENUM('M','F','Other') DEFAULT 'M',
  dob DATE,
  city VARCHAR(100),
  address TEXT,
  last_donation DATE DEFAULT NULL,
  availability_status TINYINT(1) DEFAULT 1, -- 1 = available, 0 = not available
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hospitals
CREATE TABLE IF NOT EXISTS hospitals (
  hospital_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  city VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins
CREATE TABLE IF NOT EXISTS admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blood requests (created by hospitals)
CREATE TABLE IF NOT EXISTS blood_requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  hospital_id INT NOT NULL,
  blood_group VARCHAR(5) NOT NULL,
  quantity INT NOT NULL,
  note TEXT,
  status ENUM('pending','fulfilled','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id) ON DELETE CASCADE
);
ALTER TABLE blood_requests
  ADD COLUMN assigned_donor_id INT NULL,
  ADD CONSTRAINT fk_requests_assigned_donor
    FOREIGN KEY (assigned_donor_id) REFERENCES donors(donor_id)
    ON DELETE SET NULL;

-- Donations log (when donor donates, admin records)
CREATE TABLE IF NOT EXISTS donations (
  donation_id INT AUTO_INCREMENT PRIMARY KEY,
  donor_id INT NOT NULL,
  admin_id INT DEFAULT NULL,
  donation_date DATE NOT NULL,
  units INT DEFAULT 1,
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (donor_id) REFERENCES donors(donor_id) ON DELETE CASCADE,
  FOREIGN KEY (admin_id) REFERENCES admins(admin_id) ON DELETE SET NULL
);
ALTER TABLE donations
  ADD COLUMN request_id INT NULL,
  ADD CONSTRAINT fk_donations_request
    FOREIGN KEY (request_id) REFERENCES blood_requests(request_id)
    ON DELETE SET NULL;

-- Simple blood inventory (maintained by admin)
CREATE TABLE IF NOT EXISTS blood_inventory (
  inventory_id INT AUTO_INCREMENT PRIMARY KEY,
  blood_group VARCHAR(5) NOT NULL,
  units INT DEFAULT 0,
  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Notifications (simple store for sent emails/notifications)
CREATE TABLE IF NOT EXISTS notifications (
  notification_id INT AUTO_INCREMENT PRIMARY KEY,
  to_email VARCHAR(150),
  subject VARCHAR(255),
  body TEXT,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index for faster lookups on donor blood group & availability
CREATE INDEX idx_donor_group ON donors(blood_group);
CREATE INDEX idx_donor_avail ON donors(availability_status);

-- -- Insert a default admin (password will be "admin123" hashed by the PHP script; see README)
-- INSERT INTO admins (username, email, password)
-- VALUES ('superadmin', 'admin@bloodbank.local', ''); -- fill password later via script

-- Insert a default admin (plain text password for sample project)
INSERT INTO admins (username, email, password)
VALUES ('admin', 'admin@bloodbank.local', 'admin123');
