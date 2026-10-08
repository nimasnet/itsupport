<?php

namespace App\Models;

use CodeIgniter\Model;

class WemosLogModel extends Model
{
    protected $table            = 'wemos_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['event_type', 'message', 'created_at'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false; // We set created_at manually via db default, but CI4 can handle it
    protected $dateFormat    = 'datetime';
}
