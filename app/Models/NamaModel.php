<?php

namespace App\Models;

use CodeIgniter\Model;

class NamaModel extends Model
{
    protected $table            = 'master_nama_cctv';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_cctv'];
}
