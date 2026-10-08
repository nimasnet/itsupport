<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleBellModel extends Model
{
    protected $table            = 'schedule_bell';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    
    protected $allowedFields    = [
        'no', 'conten', 'building', 'announcement_text', 'audio_file', 'highlight',
        'day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6',
        'spk_in', 'spk_out',
        't_001', 't_100', 't_600', 't_650', 't_700', 't_705', 't_710', 't_715',
        't_730', 't_800', 't_810', 't_830', 't_858', 't_900', 't_910', 't_930',
        't_940', 't_945', 't_950', 't_1000', 't_1010', 't_1030', 't_1058',
        't_1100', 't_1110', 't_1115', 't_1130', 't_1145', 't_1200', 't_1201',
        't_1210', 't_1230',
        't_1240', 't_1245', 't_1246', 't_1247', 't_1300', 't_1301', 't_1305',
        't_1310', 't_1315', 't_1316', 't_1325', 't_1330', 't_1335', 't_1345',
        't_1358', 't_1410', 't_1430', 't_1500', 't_1530', 't_1505', 't_1555',
        't_1558', 't_1600', 't_1615', 't_1625', 't_1630', 't_1645'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
