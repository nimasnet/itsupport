<?php

namespace App\Models;

use CodeIgniter\Model;

class StbModel extends Model
{
    protected $table            = 'stb_mess';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kamar_no', 'nama_user', 'lokasi', 'ip_address', 'keterangan'];
}
