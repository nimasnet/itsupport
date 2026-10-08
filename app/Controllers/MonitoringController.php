<?php

namespace App\Controllers;

use App\Models\CctvModel;
use App\Models\VlanModel;
use App\Models\NvrModel;
use App\Models\PrintHistoryModel;
use App\Models\PrintLoggerClientModel;
use App\Models\NetworkLogModel;
use App\Models\SettingModel;
use App\Models\MonitoringSetupModel;

class MonitoringController extends BaseController
{
    protected $cctvModel;
    protected $vlanModel;
    protected $nvrModel;
    protected $printHistoryModel;
    protected $printClientModel;
    protected $networkLogModel;
    protected $settingModel;
    protected $monitoringSetupModel;

    public function __construct()
    {
        $this->cctvModel = new CctvModel();
        $this->vlanModel = new VlanModel();
        $this->nvrModel = new NvrModel();
        $this->printHistoryModel = new PrintHistoryModel();
        $this->printClientModel = new PrintLoggerClientModel();
        $this->networkLogModel = new NetworkLogModel();
        $this->settingModel = new SettingModel();
        $this->monitoringSetupModel = new MonitoringSetupModel();
    }

    public function cctv()
    {
        if (!has_access('monitoring.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $session = session();
        $role = strtolower($session->get('role') ?? '');
        $username = $session->get('user');
        $isAdmin = ($role === 'read-write');
        
        $db = \Config\Database::connect();
        
        // Allowed VLANs
        $allowedVlanIds = [];
        if (!$isAdmin && $username) {
            $vlanAccess = $db->table('vlan_access')->where('username', $username)->get()->getResultArray();
            foreach ($vlanAccess as $va) {
                $allowedVlanIds[] = $va['vlan_id'];
            }
        }
        
        $allVlans = $this->vlanModel->orderBy('nama_vlan', 'ASC')->findAll();
        $vlanList = [];
        foreach ($allVlans as $v) {
            if ($isAdmin || in_array($v['id'], $allowedVlanIds)) {
                $vlanList[] = $v;
            }
        }
        
        $activeNetwork = $this->request->getGet('vlan');
        if (!$activeNetwork && count($vlanList) > 0) {
            $activeNetwork = $vlanList[0]['network_ip'];
        }
        
        $activeVlanName = '';
        foreach ($vlanList as $v) {
            if ($v['network_ip'] == $activeNetwork) {
                $activeVlanName = $v['nama_vlan'];
                break;
            }
        }
        
        $settings = $this->settingModel->find(1);
        $pingMethod = $settings['ping_method'] ?? 'php';
        
        $allCctvIps = [];
        $cctvList = [];
        $dataCctv = $this->cctvModel->findAll();
        
        foreach ($dataCctv as $row) {
            $ip = $row['ip_address'];
            if (strpos($ip, $activeNetwork) === 0) {
                $cctvList[$ip] = $row;
            }
            $parts = explode('.', $ip);
            if (count($parts) == 4) {
                $net = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                if (!isset($allCctvIps[$net])) {
                    $allCctvIps[$net] = [];
                }
                $allCctvIps[$net][] = $ip;
            }
        }
        
        $activeNvr = $this->request->getGet('nvr');
        $nvrList = $this->nvrModel->orderBy('nama_nvr', 'ASC')->findAll();

        $data = [
            'title' => 'Monitoring CCTV',
            'vlanList' => $vlanList,
            'activeNetwork' => $activeNetwork,
            'activeVlanName' => $activeVlanName,
            'pingMethod' => $pingMethod,
            'allCctvIps' => $allCctvIps,
            'cctvList' => $cctvList,
            'activeNvr' => $activeNvr,
            'nvrList' => $nvrList
        ];
        
        return view('monitoring/cctv', $data);
    }
    
    public function cctvLogs()
    {
        if (!has_access('monitoring_logs.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();
        
        $filter_ip = trim($this->request->getGet('ip') ?? '');
        $filter_status = trim($this->request->getGet('status') ?? '');
        $filter_date = trim($this->request->getGet('date') ?? '');
        
        $per_page = 100;
        $page = $this->request->getGet('page') ? max(1, (int)$this->request->getGet('page')) : 1;
        $offset = ($page - 1) * $per_page;
        
        $builder = $db->table('network_logs n');
        $builder->select('n.*, c.nama_cctv, c.posisi, c.channel, c.nvr');
        $builder->join('cctv c', 'c.ip_address = n.ip_address', 'left');
        
        if ($filter_ip !== '') {
            $builder->like('n.ip_address', $filter_ip);
        }
        if ($filter_status !== '' && in_array($filter_status, ['UP', 'DOWN'])) {
            $builder->where('n.status', $filter_status);
        }
        if ($filter_date !== '') {
            $builder->where('DATE(n.created_at)', $filter_date);
        }
        
        $total_rows = $builder->countAllResults(false);
        $total_pages = max(1, ceil($total_rows / $per_page));
        if ($page > $total_pages) $page = $total_pages;
        
        $builder->orderBy('n.created_at', 'DESC');
        $builder->limit($per_page, $offset);
        $logs = $builder->get()->getResultArray();
        
        // Summary for today
        $today = date('Y-m-d');
        $summary = $db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status='UP' THEN 1 ELSE 0 END) as up_count,
            SUM(CASE WHEN status='DOWN' THEN 1 ELSE 0 END) as down_count
            FROM network_logs WHERE DATE(created_at) = '$today'")->getRowArray();
            
        // IP list
        $ip_list_res = $db->query("SELECT DISTINCT ip_address FROM network_logs ORDER BY ip_address ASC")->getResultArray();
        $ip_list = array_column($ip_list_res, 'ip_address');
        
        // Recent sessions
        $recent_sessions = $db->query("SELECT created_at, COUNT(*) as total,
            SUM(CASE WHEN status='UP' THEN 1 ELSE 0 END) as up,
            SUM(CASE WHEN status='DOWN' THEN 1 ELSE 0 END) as down
            FROM network_logs 
            GROUP BY created_at
            ORDER BY created_at DESC
            LIMIT 5")->getResultArray();
            
        // VLAN reset list
        $vlan_reset_list = $db->query("SELECT mv.nama_vlan, mv.network_ip,
            (SELECT COUNT(*) FROM network_logs nl WHERE nl.ip_address LIKE CONCAT(mv.network_ip, '.%')) as jumlah_log
            FROM master_vlan mv
            ORDER BY mv.nama_vlan ASC")->getResultArray();
            
        $data = [
            'title' => 'Log Monitoring CCTV',
            'logs' => $logs,
            'total_rows' => $total_rows,
            'total_pages' => $total_pages,
            'page' => $page,
            'summary' => $summary,
            'ip_list' => $ip_list,
            'recent_sessions' => $recent_sessions,
            'vlan_reset_list' => $vlan_reset_list,
            'filter_ip' => $filter_ip,
            'filter_status' => $filter_status,
            'filter_date' => $filter_date
        ];
        
        return view('monitoring/cctv_logs', $data);
    }
    
    public function cctvLogsStats()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        
        $summary = $db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status='UP' THEN 1 ELSE 0 END) as up_count,
            SUM(CASE WHEN status='DOWN' THEN 1 ELSE 0 END) as down_count
            FROM network_logs WHERE DATE(created_at) = '$today'")->getRowArray();
            
        $filter_ip = trim($this->request->getGet('ip') ?? '');
        $filter_status = trim($this->request->getGet('status') ?? '');
        $filter_date = trim($this->request->getGet('date') ?? '');
        
        $builder = $db->table('network_logs n');
        if ($filter_ip !== '') {
            $builder->like('n.ip_address', $filter_ip);
        }
        if ($filter_status !== '' && in_array($filter_status, ['UP', 'DOWN'])) {
            $builder->where('n.status', $filter_status);
        }
        if ($filter_date !== '') {
            $builder->where('DATE(n.created_at)', $filter_date);
        }
        
        $total_rows = $builder->countAllResults();
        
        $recent_sessions = $db->query("SELECT created_at, COUNT(*) as total,
            SUM(CASE WHEN status='UP' THEN 1 ELSE 0 END) as up,
            SUM(CASE WHEN status='DOWN' THEN 1 ELSE 0 END) as down
            FROM network_logs 
            GROUP BY created_at
            ORDER BY created_at DESC
            LIMIT 5")->getResultArray();
            
        $recent = [];
        foreach ($recent_sessions as $sr) {
            $recent[] = [
                'created_at' => $sr['created_at'],
                'date_formatted' => date('d/m H:i', strtotime($sr['created_at'])),
                'date_filter' => date('Y-m-d', strtotime($sr['created_at'])),
                'up' => (int)$sr['up'],
                'down' => (int)$sr['down']
            ];
        }
        
        return $this->response->setJSON([
            'total_today' => (int)$summary['total'],
            'up_today' => (int)$summary['up_count'],
            'down_today' => (int)$summary['down_count'],
            'total_filtered' => (int)$total_rows,
            'recent_sessions' => $recent
        ]);
    }
    
    public function cctvLogsReset()
    {
        $vlans = $this->request->getPost('vlans');
        if (empty($vlans)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada VLAN yang dipilih.']);
        }
        
        $db = \Config\Database::connect();
        $deleted_total = 0;
        
        foreach ($vlans as $network) {
            $network = trim($network);
            if (empty($network)) continue;
            
            $builder = $db->table('network_logs');
            $builder->like('ip_address', $network . '.%', 'after');
            $builder->delete();
            $deleted_total += $db->affectedRows();
        }
        
        return $this->response->setJSON(['status' => 'success', 'deleted' => $deleted_total]);
    }
    
    public function cctvLogsExport()
    {
        $ex_start = trim($this->request->getGet('ex_start') ?? '');
        $ex_end   = trim($this->request->getGet('ex_end') ?? '');
        $ex_vlans = $this->request->getGet('ex_vlans');
        
        $db = \Config\Database::connect();
        $builder = $db->table('network_logs n');
        $builder->select('n.ip_address, n.status, n.response_time, n.info_text, n.created_at, c.nama_cctv, c.posisi, c.channel, c.nvr, mv.nama_vlan');
        $builder->join('cctv c', 'c.ip_address = n.ip_address', 'left');
        $builder->join('master_vlan mv', "n.ip_address LIKE CONCAT(mv.network_ip, '.%')", 'left');
        
        if (!empty($ex_vlans) && is_array($ex_vlans)) {
            $builder->groupStart();
            foreach ($ex_vlans as $vnet) {
                $builder->orLike('n.ip_address', trim($vnet) . '.%', 'after');
            }
            $builder->groupEnd();
        }
        
        if ($ex_start !== '') {
            $builder->where('n.created_at >=', str_replace('T', ' ', $ex_start) . ':00');
        }
        if ($ex_end !== '') {
            $builder->where('n.created_at <=', str_replace('T', ' ', $ex_end) . ':59');
        }
        
        $builder->orderBy('n.created_at', 'DESC');
        $rows = $builder->get()->getResultArray();
        
        $filename = 'monitoring_log_' . date('Ymd_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo ' xmlns:x="urn:schemas-microsoft-com:office:excel">' . "\n";
        echo '<Styles>' . "\n";
        echo '  <Style ss:ID="hdr"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#217346" ss:Pattern="Solid"/></Style>' . "\n";
        echo '  <Style ss:ID="up"><Interior ss:Color="#D4F5E9" ss:Pattern="Solid"/></Style>' . "\n";
        echo '  <Style ss:ID="dn"><Interior ss:Color="#FDE8E8" ss:Pattern="Solid"/></Style>' . "\n";
        echo '</Styles>' . "\n";
        echo '<Worksheet ss:Name="Log Monitoring">' . "\n";
        echo '<Table>' . "\n";
        echo '<Column ss:Width="40"/><Column ss:Width="110"/><Column ss:Width="130"/>';
        echo '<Column ss:Width="130"/><Column ss:Width="120"/><Column ss:Width="70"/>';
        echo '<Column ss:Width="80"/><Column ss:Width="70"/><Column ss:Width="90"/>';
        echo '<Column ss:Width="160"/><Column ss:Width="140"/>' . "\n";

        $headers = ['No','VLAN','IP Address','Nama CCTV','Posisi/Lokasi','Channel','NVR','Status','Response Time','Keterangan','Waktu Ping'];
        echo '<Row>' . "\n";
        foreach ($headers as $h) {
            echo '  <Cell ss:StyleID="hdr"><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
        }
        echo '</Row>' . "\n";

        $num = 1;
        foreach ($rows as $row) {
            $sty = ($row['status'] === 'UP') ? 'up' : 'dn';
            echo '<Row>' . "\n";
            $cells = [
                ['Number', $num++],
                ['String', $row['nama_vlan'] ?? ''],
                ['String', $row['ip_address']],
                ['String', $row['nama_cctv'] ?? ''],
                ['String', $row['posisi'] ?? ''],
                ['String', $row['channel'] ?? ''],
                ['String', $row['nvr'] ?? ''],
                ['String', $row['status']],
                ['String', $row['response_time'] ?? 'Timeout'],
                ['String', $row['info_text'] ?? ''],
                ['String', $row['created_at']],
            ];
            foreach ($cells as $cell) {
                echo '  <Cell ss:StyleID="' . $sty . '"><Data ss:Type="' . $cell[0] . '">' . htmlspecialchars((string)$cell[1]) . '</Data></Cell>' . "\n";
            }
            echo '</Row>' . "\n";
        }
        echo '</Table></Worksheet></Workbook>';
        exit;
    }
    
    public function cctvSetup()
    {
        if (!has_access('setup_monitoring.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }
        
        $setup = $this->monitoringSetupModel->first();
        if (!$setup) {
            $setup = ['interval_minutes' => 60, 'vlan_ids' => '[]', 'last_run' => ''];
        }
        
        $selected_vlans = json_decode($setup['vlan_ids'], true) ?: [];
        $vlan_list = $this->vlanModel->orderBy('nama_vlan', 'ASC')->findAll();
        
        $data = [
            'title' => 'Setup Monitoring IP',
            'setup' => $setup,
            'selected_vlans' => $selected_vlans,
            'vlan_list' => $vlan_list,
            'page_perm' => get_permission('setup_monitoring.php')
        ];
        
        return view('monitoring/cctv_setup', $data);
    }
    
    public function cctvSetupSave()
    {
        if (get_permission('setup_monitoring.php') === 'R') {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menyimpan konfigurasi.');
        }
        
        $interval = $this->request->getPost('interval_minutes') ? (int)$this->request->getPost('interval_minutes') : 60;
        if ($interval < 1) $interval = 1;
        
        $vlans = $this->request->getPost('vlan_ids') ?: [];
        $vlans_json = json_encode($vlans);
        
        $setup = $this->monitoringSetupModel->first();
        if ($setup) {
            $this->monitoringSetupModel->update($setup['id'], [
                'interval_minutes' => $interval,
                'vlan_ids' => $vlans_json
            ]);
        } else {
            $this->monitoringSetupModel->insert([
                'interval_minutes' => $interval,
                'vlan_ids' => $vlans_json
            ]);
        }
        
        return redirect()->to('monitoring/cctv/setup')->with('message', 'Setup berhasil disimpan!');
    }
    
    public function cctvSetupRun()
    {
        set_time_limit(300);
        
        $is_manual = $this->request->getGet('manual') == '1' || $this->request->getPost('manual') == '1';
        $start_time = microtime(true);
        
        $setup = $this->monitoringSetupModel->first();
        if (!$setup) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Konfigurasi monitoring belum diatur.']);
        }
        
        $interval_minutes = (int)$setup['interval_minutes'];
        $selected_vlan_ids = json_decode($setup['vlan_ids'], true);
        if (empty($selected_vlan_ids)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada VLAN yang dipilih di Setup Monitoring.']);
        }
        
        if (!$is_manual) {
            $last_run = $setup['last_run'];
            if ($last_run) {
                $last_run_ts = strtotime($last_run);
                $next_run_ts = $last_run_ts + ($interval_minutes * 60);
                if (time() < $next_run_ts) {
                    $remaining = $next_run_ts - time();
                    return $this->response->setJSON([
                        'status' => 'skipped',
                        'message' => 'Belum waktunya. Monitoring berikutnya dalam ' . round($remaining / 60, 1) . ' menit.',
                        'next_run_in_seconds' => $remaining
                    ]);
                }
            }
        }
        
        $db = \Config\Database::connect();
        $target_networks = [];
        $vlans = $db->table('master_vlan')->whereIn('id', $selected_vlan_ids)->get()->getResultArray();
        foreach ($vlans as $vrow) {
            $target_networks[] = $vrow['network_ip'];
        }
        
        if (empty($target_networks)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data VLAN tidak ditemukan di database.']);
        }
        
        $cctv_ips = [];
        $data_res = $db->query("SELECT ip_address FROM cctv")->getResultArray();
        foreach ($data_res as $row) {
            $ip = $row['ip_address'];
            $parts = explode('.', $ip);
            if (count($parts) == 4) {
                $net = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                if (in_array($net, $target_networks)) {
                    $cctv_ips[] = $ip;
                }
            }
        }
        
        if (empty($cctv_ips)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada IP CCTV yang terdaftar pada VLAN yang dipilih.']);
        }
        
        $total_pinged = 0;
        $total_online = 0;
        $total_offline = 0;
        $timestamp_now = date('Y-m-d H:i:s');
        
        $is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        
        foreach ($cctv_ips as $ip) {
            if ($is_windows) {
                $cmd = "ping -n 1 -w 2000 " . escapeshellarg($ip);
            } else {
                $cmd = "ping -c 1 -W 2 " . escapeshellarg($ip);
            }
            $output = [];
            exec($cmd, $output, $status_code);
            
            $is_online = false;
            $response_time = null;
            foreach ($output as $line) {
                if (stripos($line, 'TTL=') !== false || stripos($line, 'TTL =') !== false || stripos($line, 'time=') !== false) {
                    $is_online = true;
                    if (preg_match('/time[<=](\d+(?:\.\d+)?)ms/i', $line, $matches)) {
                        $response_time = $matches[1] . 'ms';
                    } elseif (preg_match('/time[=<]\s*(\d+(?:\.\d+)?)\s*ms/i', $line, $matches)) {
                        $response_time = $matches[1] . 'ms';
                    }
                    break;
                }
            }
            
            $status_str = $is_online ? 'UP' : 'DOWN';
            $r_time = $response_time ?? ($is_online ? 'N/A' : 'Timeout');
            $info_text = $is_online ? "Ping OK - Response: " . $r_time : "Tidak merespon (Timeout)";
            
            $this->networkLogModel->insert([
                'ip_address' => $ip,
                'status' => $status_str,
                'response_time' => $r_time,
                'info_text' => $info_text,
                'created_at' => $timestamp_now
            ]);
            
            $total_pinged++;
            if ($is_online) $total_online++;
            else $total_offline++;
        }
        
        $this->monitoringSetupModel->update($setup['id'], ['last_run' => $timestamp_now]);
        $execution_time = round(microtime(true) - $start_time, 2);
        
        return $this->response->setJSON([
            'status'         => 'success',
            'message'        => 'Ping monitoring selesai dijalankan.',
            'total_pinged'   => $total_pinged,
            'total_online'   => $total_online,
            'total_offline'  => $total_offline,
            'execution_time' => $execution_time,
            'timestamp'      => $timestamp_now
        ]);
    }
    
    public function device()
    {
        $data['top_color'] = '#17a2b8';
        $data['page_perm'] = get_permission('monitoring_device.php');
        
        $data['logs'] = $this->networkLogModel->orderBy('id', 'DESC')->findAll(500);
        
        return view('monitoring/device', $data);
    }
    
    public function cekPing()
    {
        $ip = $this->request->getGet('ip');
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $ping_cmd = "ping -n 1 -w 1000 " . escapeshellarg($ip);
            } else {
                $ping_cmd = "ping -c 1 -W 1 " . escapeshellarg($ip);
            }

            exec($ping_cmd, $output, $status);
            
            $online = false;
            $response_time = 'Timeout';
            
            foreach ($output as $line) {
                if (stripos($line, 'TTL=') !== false || stripos($line, 'TTL =') !== false || stripos($line, 'time=') !== false) {
                    $online = true;
                    if (preg_match('/time[<=](\d+(?:\.\d+)?)ms/i', $line, $matches)) {
                        $response_time = $matches[1] . 'ms';
                    } elseif (preg_match('/time[=<]\s*(\d+(?:\.\d+)?)\s*ms/i', $line, $matches)) {
                        $response_time = $matches[1] . 'ms';
                    } else {
                        $response_time = 'N/A';
                    }
                    break;
                }
            }
            
            $status_str = $online ? 'UP' : 'DOWN';
            $info_text = $online ? "Ping OK - Response: " . $response_time : "Tidak merespon (Timeout)";
            
            $this->networkLogModel->insert([
                'ip_address' => $ip,
                'status' => $status_str,
                'response_time' => $response_time,
                'info_text' => $info_text,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return $this->response->setJSON([
                'status' => $online ? 'online' : 'offline',
                'response_time' => $response_time
            ]);
        }
        return $this->response->setJSON(['status' => 'invalid_ip']);
    }
    
    public function cekStatusDb()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT n1.ip_address, n1.status, n1.response_time 
                FROM network_logs n1
                INNER JOIN (
                    SELECT ip_address, MAX(id) as max_id
                    FROM network_logs
                    GROUP BY ip_address
                ) n2 ON n1.ip_address = n2.ip_address AND n1.id = n2.max_id";

        $result = $db->query($sql)->getResultArray();
        $data = [];
        
        foreach ($result as $row) {
            $data[$row['ip_address']] = [
                'status' => ($row['status'] === 'UP') ? 'online' : 'offline',
                'response_time' => $row['response_time']
            ];
        }
        
        return $this->response->setJSON($data);
    }
    
    public function printer()
    {
        if (!has_access('printer_monitoring.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }
        
        $clients = $this->printClientModel->orderBy('last_seen', 'DESC')->findAll();
        $history = $this->printHistoryModel->orderBy('print_time', 'DESC')->limit(100)->findAll();
        
        $data = [
            'title' => 'Printer Monitoring',
            'clients' => $clients,
            'history' => $history
        ];
        
        return view('monitoring/printer', $data);
    }
    
    public function getSetupIps()
    {
        $setup = $this->monitoringSetupModel->first();
        if (!$setup) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Konfigurasi monitoring belum diatur.']);
        }
        
        $selected_vlan_ids = json_decode($setup['vlan_ids'], true);
        if (empty($selected_vlan_ids)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada VLAN yang dipilih di Setup Monitoring.']);
        }
        
        $db = \Config\Database::connect();
        $target_networks = [];
        $vlans = $db->table('master_vlan')->whereIn('id', $selected_vlan_ids)->get()->getResultArray();
        foreach ($vlans as $vrow) {
            $target_networks[] = $vrow['network_ip'];
        }
        
        if (empty($target_networks)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data VLAN tidak ditemukan di database.']);
        }
        
        $cctv_ips = [];
        $data_res = $db->query("SELECT ip_address FROM cctv")->getResultArray();
        foreach ($data_res as $row) {
            $ip = $row['ip_address'];
            $parts = explode('.', $ip);
            if (count($parts) == 4) {
                $net = $parts[0] . '.' . $parts[1] . '.' . $parts[2];
                if (in_array($net, $target_networks)) {
                    $cctv_ips[] = $ip;
                }
            }
        }
        
        return $this->response->setJSON(['status' => 'success', 'ips' => $cctv_ips]);
    }
    
    public function updateLastRun()
    {
        $setup = $this->monitoringSetupModel->first();
        if ($setup) {
            $this->monitoringSetupModel->update($setup['id'], ['last_run' => date('Y-m-d H:i:s')]);
        }
        return $this->response->setJSON(['status' => 'success']);
    }

    public function cctvStatsChart()
    {
        if (!has_access('cctv_stats.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'CCTV Status Statistik',
            'top_color' => $this->settingModel->find(1)['top_color'] ?? '#0077b6',
            'side_color' => $this->settingModel->find(1)['side_color'] ?? '#f0f2f5',
            'current_user' => session()->get('user'),
            'current_role' => session()->get('role')
        ];

        return view('monitoring/cctv_stats_chart', $data);
    }

    public function getCctvStats()
    {
        $db = \Config\Database::connect();
        $interval = $this->request->getPost('interval') ?? 1; // Default 1 day
        $limit = (int)($this->request->getPost('limit') ?? 10); // Default 10 data
        if ($limit < 1 || $limit > 100) $limit = 10;
        
        $dateFrom = date('Y-m-d H:i:s', strtotime("-$interval days"));
        if ($interval == 1) {
            // Default update data 1 hari pada jam 08:00
            $today = date('Y-m-d');
            if (date('H') >= 8) {
                $dateFrom = $today . ' 08:00:00';
            } else {
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $dateFrom = $yesterday . ' 08:00:00';
            }
        }

        $sql = "
            SELECT 
                c.nama_cctv, 
                n.ip_address, 
                COUNT(*) as offline_count
            FROM network_logs n
            LEFT JOIN cctv c ON c.ip_address = n.ip_address
            WHERE n.status = 'DOWN' 
              AND n.created_at >= ?
              AND (n.ip_address LIKE '%.48.%' OR n.ip_address LIKE '%.57.%')
            GROUP BY n.ip_address
            ORDER BY offline_count DESC
            LIMIT ?
        ";

        $results = $db->query($sql, [$dateFrom, $limit])->getResultArray();
        
        $labels = [];
        $data = [];
        
        foreach ($results as $row) {
            $name = !empty($row['nama_cctv']) ? $row['nama_cctv'] : $row['ip_address'];
            $labels[] = $name;
            $data[] = (int)$row['offline_count'];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'labels' => $labels,
            'data' => $data,
            'date_from' => $dateFrom,
            'interval' => $interval,
            'limit' => $limit
        ]);
    }
}
