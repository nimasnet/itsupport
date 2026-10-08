<?php

namespace App\Models;

use CodeIgniter\Model;

class PrintLoggerClientModel extends Model
{
    protected $table            = 'print_logger_clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['pc_name', 'ip_address', 'last_seen'];
}
