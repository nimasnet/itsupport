<?php

namespace App\Models;

use CodeIgniter\Model;

class IpManagementModel extends Model
{
    protected $table            = 'ip_management';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Dates
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields    = [
        'ip_address',
        'mac_address',
        'device_type',
        'user_assigned',
        'department',
        'status',
        'description',
    ];
}
