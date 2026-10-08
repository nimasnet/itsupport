<?php

namespace App\Models;

use CodeIgniter\Model;

class VlanAccessModel extends Model
{
    protected $table            = 'vlan_access';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'vlan_id'];
}
