<?php
$db = mysqli_connect('localhost', 'root', '', 'db_cctv');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
$query = "CREATE TABLE IF NOT EXISTS ups_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
if (mysqli_query($db, $query)) {
    echo "Table ups_locations created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($db) . "\n";
}
mysqli_close($db);
