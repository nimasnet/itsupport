<?php
$db = mysqli_connect('localhost', 'root', '', 'db_cctv');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
$query = "CREATE TABLE IF NOT EXISTS ups_checklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ups_location VARCHAR(255),
    ups_conditions VARCHAR(50),
    date_update DATE,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
if (mysqli_query($db, $query)) {
    echo "Table ups_checklist created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($db) . "\n";
}
mysqli_close($db);
