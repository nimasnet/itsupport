<?php
$db = new mysqli('localhost', 'root', '', 'db_cctv');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$columns = [
    'ip_address' => "VARCHAR(50) NULL",
    'stream_url' => "VARCHAR(255) NULL",
    'stream_user' => "VARCHAR(100) NULL",
    'stream_pass' => "VARCHAR(100) NULL",
    'stream_type' => "VARCHAR(20) DEFAULT 'mjpeg'"
];

foreach ($columns as $col => $def) {
    $check = $db->query("SHOW COLUMNS FROM master_nvr LIKE '$col'");
    if ($check->num_rows == 0) {
        $db->query("ALTER TABLE master_nvr ADD COLUMN $col $def");
        echo "Added $col\n";
    } else {
        echo "$col already exists\n";
    }
}
echo "Database update complete.\n";
