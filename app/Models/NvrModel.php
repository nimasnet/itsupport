<?php

namespace App\Models;

use CodeIgniter\Model;

class NvrModel extends Model
{
    protected $table            = 'master_nvr';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_nvr', 'ip_address', 'stream_url', 'stream_user', 'stream_pass', 'stream_type'];
}
