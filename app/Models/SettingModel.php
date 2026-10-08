<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['default_homepage', 'top_color', 'side_color', 'ping_method', 'excel_header_color', 'user_homepages', 'schedule_bypass'];
    protected $returnType = 'array';
    protected $useTimestamps = false; 
}
