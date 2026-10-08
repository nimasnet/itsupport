<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil Pengaturan Refresh
        $setup = $db->table('monitoring_setup')->get(1)->getRowArray();
        $interval_minutes = 1;
        if ($setup && isset($setup['interval_minutes']) && $setup['interval_minutes'] > 0) {
            $interval_minutes = (int)$setup['interval_minutes'];
        }
        $interval_seconds = $interval_minutes * 60;
        
        // 2. Ambil Data Statistik Keseluruhan (Query Dioptimasi)
        $q_stats = "SELECT c.ip_address, 
                           (SELECT status FROM network_logs WHERE ip_address = c.ip_address ORDER BY id DESC LIMIT 1) as status 
                    FROM cctv c";
        $res_stats = $db->query($q_stats)->getResultArray();
        
        $total_devices = 0;
        $up_count = 0;
        $down_count = 0;
        $unknown_count = 0;
        $down_devices = [];
        
        foreach($res_stats as $r) {
            $total_devices++;
            if($r['status'] == 'UP') {
                $up_count++;
            } elseif($r['status'] == 'DOWN') {
                $down_count++;
                $down_devices[] = $r['ip_address'];
            } else {
                $unknown_count++;
            }
        }
        
        // 3. Detail Device Offline
        $offline_details = [];
        if (count($down_devices) > 0) {
            $offline_details = $db->table('cctv')
                                  ->select('ip_address, channel, nama_cctv, posisi, nvr')
                                  ->whereIn('ip_address', $down_devices)
                                  ->limit(10)
                                  ->get()->getResultArray();
        }
        
        // 4. Statistik per VLAN
        $vlan_stats = [];
        $res_vlan = $db->table('master_vlan')->orderBy('nama_vlan', 'ASC')->get()->getResultArray();
        
        foreach($res_vlan as $v) {
            $net = $v['network_ip'];
            $v_total = 0; $v_up = 0; $v_down = 0; $v_unknown = 0;
            
            foreach($res_stats as $r) {
                if (strpos($r['ip_address'], $net . '.') === 0) {
                    $v_total++;
                    if($r['status'] == 'UP') $v_up++;
                    elseif($r['status'] == 'DOWN') $v_down++;
                    else $v_unknown++;
                }
            }
            
            if ($v_total > 0) {
                $vlan_stats[] = [
                    'nama_vlan' => $v['nama_vlan'],
                    'network_ip' => $v['network_ip'],
                    'total' => $v_total,
                    'up' => $v_up,
                    'down' => $v_down,
                    'unknown' => $v_unknown
                ];
            }
        }
        
        // 5. Recent Logs
        $recent_logs = $db->table('network_logs nl')
                          ->select('nl.ip_address, nl.status, nl.created_at, c.nama_cctv')
                          ->join('cctv c', 'nl.ip_address = c.ip_address', 'left')
                          ->orderBy('nl.id', 'DESC')
                          ->limit(8)
                          ->get()->getResultArray();
                          
        $data = [
            'title' => 'Dashboard PRTG',
            'interval_seconds' => $interval_seconds,
            'total_devices' => $total_devices,
            'up_count' => $up_count,
            'down_count' => $down_count,
            'unknown_count' => $unknown_count,
            'offline_details' => $offline_details,
            'vlan_stats' => $vlan_stats,
            'recent_logs' => $recent_logs
        ];
        
        return view('dashboard/index', $data);
    }
}
