<?php

namespace App\Controllers;

use App\Models\CctvModel;
use App\Models\VlanModel;
use App\Models\IpManagementModel;

class ToolsController extends BaseController
{
    protected $cctvModel;
    protected $vlanModel;
    protected $ipModel;

    public function __construct()
    {
        $this->cctvModel = new CctvModel();
        $this->vlanModel = new VlanModel();
        $this->ipModel   = new IpManagementModel();
    }

    public function scan()
    {
        if ($this->request->getMethod() === 'POST' && $this->request->getPost('add_to_list')) {
            $selected_ips  = $this->request->getPost('selected_ips');
            $identities    = $this->request->getPost('ip_identities') ?? [];
            if (!empty($selected_ips) && is_array($selected_ips)) {
                $added   = 0;
                $skipped = 0;
                foreach ($selected_ips as $ip) {
                    $ip = trim($ip);
                    if (empty($ip)) continue;

                    // Cek apakah IP sudah ada di ip_management atau cctv
                    $existIpMan = $this->ipModel->where('ip_address', $ip)->first();
                    $existCctv  = $this->cctvModel->where('ip_address', $ip)->first();

                    if (!$existIpMan || !$existCctv) {
                        // Ambil identity/pc-name jika tersedia
                        $pcName = isset($identities[$ip]) && !empty(trim($identities[$ip]))
                            ? trim($identities[$ip])
                            : 'Device Baru (Hasil Scan)';

                        if (!$existIpMan) {
                            $this->ipModel->insert([
                                'ip_address'    => $ip,
                                'mac_address'   => '',
                                'device_type'   => 'PC',
                                'user_assigned' => $pcName,
                                'department'    => 'Hasil Scan',
                                'status'        => 'Active',
                                'description'   => 'Ditambahkan otomatis dari fitur Scan Network',
                            ]);
                        }

                        if (!$existCctv) {
                            $this->cctvModel->insert([
                                'ip_address' => $ip,
                                'nama_cctv'  => $pcName,
                                'posisi'     => 'Belum Diset',
                                'keterangan' => 'Ditambahkan otomatis dari fitur Scan Network',
                            ]);
                        }

                        $added++;
                    } else {
                        $skipped++;
                    }
                }
                $msg = "$added IP baru berhasil ditambahkan ke daftar IP List & CCTV.";
                if ($skipped > 0) $msg .= " ($skipped IP dilewati karena sudah terdaftar)";
                session()->setFlashdata('success', $msg);
            } else {
                session()->setFlashdata('error', 'Pilih minimal 1 IP yang ONLINE untuk ditambahkan.');
            }
            return redirect()->to(site_url('tools/scan'));
        }

        $data['top_color'] = '#f39c12'; // Orange color for tools
        $data['vlan_list'] = $this->vlanModel->orderBy('nama_vlan', 'ASC')->findAll();
        
        // Get registered IPs from IP Management & CCTV tables
        $registered_ips = [];

        $ipManData = $this->ipModel->findAll();
        foreach ($ipManData as $row) {
            if (!empty($row['ip_address'])) {
                $registered_ips[] = trim($row['ip_address']);
            }
        }

        $cctvs = $this->cctvModel->findAll();
        foreach ($cctvs as $cctv) {
            if (!empty($cctv['ip_address'])) {
                $registered_ips[] = trim($cctv['ip_address']);
            }
        }
        
        $data['registered_ips'] = array_values(array_unique($registered_ips));
        
        $data['page_perm'] = get_permission('scan.php');

        return view('tools/scan', $data);
    }

    public function pingScan()
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
            foreach ($output as $line) {
                if (stripos($line, 'TTL=') !== false || stripos($line, 'TTL =') !== false || stripos($line, 'time=') !== false) {
                    $online = true;
                    break;
                }
            }

            // Resolve hostname / identity jika online
            $identity = '';
            if ($online) {
                // Coba gethostbyaddr (DNS reverse lookup)
                $hostname = @gethostbyaddr($ip);
                if ($hostname && $hostname !== $ip) {
                    // Ambil bagian hostname tanpa domain suffix
                    $identity = strtoupper(explode('.', $hostname)[0]);
                } else {
                    // Fallback: nbtstat (Windows NetBIOS)
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        exec("nbtstat -A " . escapeshellarg($ip) . " 2>nul", $nbt_out);
                        foreach ($nbt_out as $nbLine) {
                            if (preg_match('/^\s+([\w-]+)\s+<00>\s+UNIQUE/', $nbLine, $m)) {
                                $identity = strtoupper(trim($m[1]));
                                break;
                            }
                        }
                    }
                }
            }
            
            return $this->response->setJSON([
                'status'   => $online ? 'online' : 'offline',
                'identity' => $identity
            ]);
        }
        
        return $this->response->setJSON(['status' => 'invalid_ip', 'identity' => '']);
    }

    public function graphs()
    {
        $data['target_ip'] = $this->request->getGet('ip') ?? '8.8.8.8';
        $data['top_color'] = '#0077b6';
        $data['page_perm'] = get_permission('graphs.php');
        
        return view('tools/graphs', $data);
    }
}
