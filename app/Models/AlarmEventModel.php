<?php

namespace App\Models;

use CodeIgniter\Model;

class AlarmEventModel extends Model
{
    protected $table = 'alarm_events';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'panel_id', 'event_time', 'raw_data', 'event_type', 'description', 'zone'
    ];
}
