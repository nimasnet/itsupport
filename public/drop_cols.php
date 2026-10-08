<?php
$mysqli = new mysqli("localhost", "root", "", "db_cctv");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SHOW COLUMNS FROM schedule_bell");
$columnsToDrop = [];
while ($row = $result->fetch_assoc()) {
    if (strpos($row['Field'], 't_') === 0) {
        $columnsToDrop[] = $row['Field'];
    }
}

if (!empty($columnsToDrop)) {
    foreach ($columnsToDrop as $col) {
        $mysqli->query("ALTER TABLE schedule_bell DROP COLUMN `$col`");
        echo "Dropped column: $col\n";
    }
    echo "All time columns dropped successfully.\n";
} else {
    echo "No time columns found.\n";
}
$mysqli->close();
?>
