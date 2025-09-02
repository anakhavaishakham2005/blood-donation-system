CREATE DATABASE blood_bank_db;
USE blood_bank_db;

-- Donor table
CREATE TABLE donors (
    donor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    blood_group VARCHAR(5),
    dob DATE,
    last_donation DATE,
    availability_status TINYINT DEFAULT 1
);

-- Hospital table
CREATE TABLE hospitals (
    hospital_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    address TEXT
);

-- Admin table
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);

-- Requests
CREATE TABLE blood_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    blood_group VARCHAR(5),
    quantity INT,
    status ENUM('pending','fulfilled','cancelled') DEFAULT 'pending',
    FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id)
);
