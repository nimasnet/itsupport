<?php
$db = new mysqli('localhost', 'root', '', 'db_cctv');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
$res = $db->query('DESCRIBE settings');
while($row = $res->fetch_assoc()) {
    print_r($row);
}

// Add column if it doesn't exist
$db->query("ALTER TABLE settings ADD COLUMN excel_header_color VARCHAR(7) DEFAULT '#4e73df'");
echo "Column added or already exists.\n";

