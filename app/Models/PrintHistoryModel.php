<?php

namespace App\Models;

use CodeIgniter\Model;

class PrintHistoryModel extends Model
{
    protected $table            = 'print_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['print_time', 'printer_name', 'document_name', 'user_name', 'hostname', 'client_ip', 'print_source', 'pages', 'job_status', 'logger_pc'];
}
