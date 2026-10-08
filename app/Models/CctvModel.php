<?php

namespace App\Models;

use CodeIgniter\Model;

class CctvModel extends Model
{
    protected $table            = 'cctv';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['ip_address', 'channel', 'nvr', 'nama_cctv', 'posisi', 'keterangan', 'stream_url', 'stream_user', 'stream_pass', 'stream_type', 'view_pointing_image'];
}
