<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistCctvModel extends Model
{
    protected $table            = 'checklist_cctv';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['tanggal', 'jam', 'pic_check', 'nvr', 'channel', 'nama_cctv', 'status', 'it_respon', 'keterangan', 'image'];
}
