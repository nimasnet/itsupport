<?php

namespace App\Models;

use CodeIgniter\Model;

class VlanModel extends Model
{
    protected $table            = 'master_vlan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_vlan', 'network_ip'];
}
