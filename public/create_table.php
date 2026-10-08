<?php
$conn = new mysqli('localhost', 'root', '', 'itsupport');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE IF NOT EXISTS ip_management (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(50) NOT NULL,
    mac_address VARCHAR(100) DEFAULT NULL,
    device_type VARCHAR(100) DEFAULT NULL,
    user_assigned VARCHAR(255) DEFAULT NULL,
    department VARCHAR(150) DEFAULT NULL,
    status ENUM('Active', 'Inactive', 'Reserved') DEFAULT 'Active',
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table ip_management created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
