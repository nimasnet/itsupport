<?php

namespace App\Models;

use CodeIgniter\Model;

class CekroolModel extends Model
{
    protected $table            = 'cekrool';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Dates
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields    = [
        'building',
        'location',
        'device_id',
        'ip_address',
        'type_device',
        'notes',
    ];
}
