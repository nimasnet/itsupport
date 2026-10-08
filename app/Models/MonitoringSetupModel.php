<?php

namespace App\Models;

use CodeIgniter\Model;

class MonitoringSetupModel extends Model
{
    protected $table = 'monitoring_setup';
    protected $primaryKey = 'id';
    
    // As per legacy DB, it usually doesn't have an ID, or might just have 1 row.
    // Assuming standard CodeIgniter practices, we'll map fields:
    protected $allowedFields = ['interval_minutes', 'vlan_ids', 'last_run'];
    protected $useTimestamps = false;
}
