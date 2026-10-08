<?php

namespace App\Models;

use CodeIgniter\Model;

class HardiskLogModel extends Model
{
    protected $table            = 'hardisk_log_replacement';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Dates
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields    = [
        'device_name',
        'slots_json',
        'start_record',
        'start_time_record',
        'last_record',
        'last_time_record',
        'remark',
    ];
}
