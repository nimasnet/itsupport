<?php

if (!function_exists('has_access')) {
    function has_access($page)
    {
        $session = session();
        $username = $session->get('user');
        $role = strtolower($session->get('role') ?? '');
        if (!$username) return false;

        // Admin user always has access to admin_management.php
        if ($username === 'admin' && $page === 'admin_management.php') {
            return true;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('page_access');
        $builder->where('username', $username);
        $builder->where('page_name', $page);
        
        return $builder->countAllResults() > 0;
    }
}

if (!function_exists('has_any_access')) {
    function has_any_access($pages)
    {
        $session = session();
        $username = $session->get('user');
        $role = strtolower($session->get('role') ?? '');
        if (!$username) return false;

        // Admin user always has access to admin_management.php
        if ($username === 'admin' && in_array('admin_management.php', $pages)) {
            return true;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('page_access');
        $builder->where('username', $username);
        $builder->whereIn('page_name', $pages);
        
        return $builder->countAllResults() > 0;
    }
}

if (!function_exists('get_permission')) {
    function get_permission($page)
    {
        $session = session();
        $role = strtolower($session->get('role') ?? '');
        
        if ($role === 'read-write') return 'RW';
        if ($role === 'write') return 'W';
        return 'R';
    }
}
