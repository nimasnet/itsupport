<?php

namespace App\Models;

use CodeIgniter\Model;

class HardiskManageModel extends Model
{
    protected $table = 'master_hardisk';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nvr_name', 'hdd_label', 'brand', 'capacity', 'serial_number', 'status', 'remark'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
