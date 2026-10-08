<?php

namespace App\Models;

use CodeIgniter\Model;

class NetworkLogModel extends Model
{
    protected $table            = 'network_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ip_address', 'status', 'response_time', 'info_text', 'created_at'];
}
