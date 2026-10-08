<?php

namespace App\Models;

use CodeIgniter\Model;

class PageAccessModel extends Model
{
    protected $table            = 'page_access';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'page_name', 'permission_type'];
}
