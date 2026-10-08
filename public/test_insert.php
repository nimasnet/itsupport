<?php
require '../vendor/autoload.php';
// Boot CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();
// test DB
$db = \Config\Database::connect();
$model = new \App\Models\ScheduleBellModel();

$baseData = [
    'conten' => 'Test',
    'building' => 'F1',
    'day_1' => 1
];

$timesList = ['07:00', '08:00', '09:00'];
$fieldsInTable = $db->getFieldNames('schedule_bell');

$created = [];
foreach ($timesList as $time) {
    $parts = explode(':', trim($time));
    $numH = (int)$parts[0];
    $numM = (int)$parts[1];
    $colKey = 't_' . ($numH == 0 ? sprintf('%02d', $numM) : ($numH . sprintf('%02d', $numM)));
    
    $rowPayload = $baseData;
    foreach ($fieldsInTable as $f) {
        if (strpos($f, 't_') === 0) {
            $rowPayload[$f] = ($f === $colKey) ? 1 : 0;
        }
    }
    
    $cleanPayload = [];
    foreach ($rowPayload as $k => $v) {
        if (in_array($k, $fieldsInTable)) {
            $cleanPayload[$k] = $v;
        }
    }
    
    $id = $model->insert($cleanPayload, true);
    $created[] = $id;
}
echo json_encode($created);
