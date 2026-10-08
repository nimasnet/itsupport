<?php
$db = mysqli_connect('localhost', 'root', '', 'db_cctv');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
$query = "CREATE TABLE IF NOT EXISTS checklist_cctv (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE,
    jam TIME,
    pic_check VARCHAR(100),
    nvr VARCHAR(100),
    channel VARCHAR(50),
    nama_cctv VARCHAR(255),
    status VARCHAR(100),
    keterangan TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
if (mysqli_query($db, $query)) {
    echo "Table created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($db) . "\n";
}
mysqli_close($db);
