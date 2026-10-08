<?php

namespace App\Models;

use CodeIgniter\Model;

class SwitchNetworkModel extends Model
{
    protected $table            = 'switch_network';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Dates
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields    = [
        'lokasi',
        'name_switch',
        'ip_address',
        'type_switch',
        'notes',
    ];
}
