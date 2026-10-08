<?php

namespace App\Models;

use CodeIgniter\Model;

class AlarmPanelModel extends Model
{
    protected $table = 'master_alarm_panel';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'name', 'ip_address', 'port', 'username', 'password', 
        'endpoint_arm', 'endpoint_disarm', 'status', 'last_online'
    ];
}
