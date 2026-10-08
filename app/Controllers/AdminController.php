<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SettingModel;
use App\Models\PageAccessModel;
use App\Models\VlanAccessModel;
use App\Models\VlanModel;
use App\Models\CctvModel;

class AdminController extends BaseController
{
    protected $userModel;
    protected $settingModel;
    protected $pageAccessModel;
    protected $vlanAccessModel;
    protected $vlanModel;
    protected $cctvModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->settingModel = new SettingModel();
        $this->pageAccessModel = new PageAccessModel();
        $this->vlanAccessModel = new VlanAccessModel();
        $this->vlanModel = new VlanModel();
        $this->cctvModel = new CctvModel();
    }

    public function index()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $view = $this->request->getGet('view') ?? 'admin';
        
        $db = \Config\Database::connect();
        $custom_menus = $db->table('custom_menus')->get()->getResultArray();

        $pageCategories = [
            'Menu Utama' => [
                'index.php' => 'Dashboard CCTV (index.php)',
                'monitoring_device.php' => 'Monitoring Status Device (monitoring_device.php)'
            ],
            'Pengelolaan CCTV' => [
                'list_cctv.php' => 'List CCTV (list_cctv.php)',
                'tambah.php' => 'Tambah CCTV (tambah.php)',
                'master_nama.php' => 'Master Nama CCTV (master_nama.php)',
                'master_nvr.php' => 'Master NVR (master_nvr.php)',
                'cctv_by_nvr.php' => 'CCTV BY NVR (cctv_by_nvr.php)',
                'hardisk_log.php' => 'Hardisk Log Replacement (hardisk_log.php)',
                'hardisk_manage.php' => 'Hardisk Manage (hardisk_manage.php)',
                'checklist_status.php' => 'Checklist Status CCTV (checklist_status.php)',
                'it_respon_actions.php' => 'IT Respon Actions (it_respon_actions.php)',
                'security_monitoring.php' => 'Security Monitoring (security_monitoring.php)',
                'cctv_viewer.php' => 'CCTV Viewer Live (cctv_viewer.php)',
                'nvr_viewer.php' => [
                    'label' => 'NVR Viewer Live (nvr_viewer.php)',
                    'sub_menus' => [
                        'nvr_config.php' => 'NVR Config (nvr_config.php)'
                    ]
                ],
                'monitoring.php' => [
                    'label' => 'Monitoring IP Live (monitoring.php)',
                    'sub_menus' => [
                        'setup_monitoring.php' => 'Setup Monitoring (setup_monitoring.php)',
                        'monitoring_logs.php' => 'Log Monitoring (monitoring_logs.php)',
                        'cctv_stats.php' => 'CCTV Status Statistik (cctv_stats.php)'
                    ]
                ]
            ],
            'Pengelolaan Jaringan' => [
                'master_vlan.php' => 'Master Jaringan VLAN (master_vlan.php)'
            ],
            'IP List Management' => [
                'ip_management.php' => 'IP List Management (ip_management.php)'
            ],
            'Device List' => [
                'stb_mess.php' => 'STB MESS (stb_mess.php)',
                'kelola_stb.php' => 'Kelola STB (kelola_stb.php)'
            ],
            'Schedule Bell' => [
                'schedule_bell.php' => 'Schedule Bell (schedule_bell.php)'
            ],
            'Tools' => [
                'scan.php' => 'Scan Jaringan IP (scan.php)',
                'graphs.php' => 'Graphs (graphs.php)',
                'printer_monitoring.php' => 'Printer Monitoring (printer_monitoring.php)',
                'script_inject.php' => 'Script Inject (script_inject.php)'
            ],
            'Admin Setup' => [
                'admin_management.php' => 'Admin Management (admin_management.php)',
                'login_log.php' => 'Loggin Log (login_log.php)'
            ],
            'Monitoring Support' => [
                'ups_checklist.php' => 'UPS Checklist (ups_checklist.php)',
                'ups_manage_list.php' => 'UPS Manage List (ups_manage_list.php)',
                'wemos.php' => 'Wemos Dashboard (wemos.php)',
                'mikrotik.php' => 'MikroTik Tool (mikrotik.php)',
                'grafanity.php' => 'Grafanity NMS (grafanity.php)'
            ]
        ];

        if (count($custom_menus) > 0) {
            $pageCategories['Menu Tambahan (Custom)'] = [];
            foreach ($custom_menus as $m) {
                $pageCategories['Menu Tambahan (Custom)'][$m['url_menu']] = $m['nama_menu'] . ' (' . $m['url_menu'] . ')';
            }
        }

        $data = [
            'title' => 'Admin Management',
            'view' => $view,
            'pageCategories' => $pageCategories,
            'settings' => $this->settingModel->find(1)
        ];

        if ($view === 'admin' || $view === 'default_homepage') {
            $data['users'] = $this->userModel->orderBy('role', 'ASC')->orderBy('username', 'ASC')->findAll();
        }

        if ($view === 'admin') {
            $data['editUser'] = null;
            if ($this->request->getGet('edit_user_id')) {
                $data['editUser'] = $this->userModel->find($this->request->getGet('edit_user_id'));
            }
            
            $data['vlanList'] = $this->vlanModel->orderBy('nama_vlan', 'ASC')->findAll();
            
            $user_update = $this->request->getPost('user_update') ?? $this->request->getGet('user_update');
            $data['user_update'] = $user_update;
            $data['grantedPages'] = [];
            $data['grantedVlans'] = [];
            
            if ($user_update) {
                $access = $this->pageAccessModel->where('username', $user_update)->findAll();
                $grantedPages = [];
                foreach ($access as $a) {
                    $grantedPages[] = $a['page_name'];
                }
                $data['grantedPages'] = $grantedPages;
                
                $vAccess = $this->vlanAccessModel->where('username', $user_update)->findAll();
                $grantedVlans = [];
                foreach ($vAccess as $v) {
                    $grantedVlans[] = $v['vlan_id'];
                }
                $data['grantedVlans'] = $grantedVlans;
            }
        }
        
        return view('admin/index', $data);
    }
    
    public function storeUser()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $id = $this->request->getPost('id_user');
        $username = $this->request->getPost('username');
        $role = $this->request->getPost('role');
        $password = $this->request->getPost('password');
        
        $data = [
            'username' => $username,
            'role' => $role
        ];
        
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        if ($id) {
            $this->userModel->update($id, $data);
            $msg = 'Data pengguna berhasil diperbarui!';
        } else {
            if (empty($password)) {
                return redirect()->to('/admin')->with('error', 'Password wajib diisi untuk pengguna baru!');
            }
            $existing = $this->userModel->where('username', $username)->first();
            if ($existing) {
                return redirect()->to('/admin')->with('error', 'Gagal menambahkan user! Username mungkin sudah digunakan.');
            }
            $this->userModel->insert($data);
            $msg = 'User baru berhasil ditambahkan!';
        }
        
        return redirect()->to('/admin')->with('success', $msg);
    }
    
    public function deleteUser($id)
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $user = $this->userModel->find($id);
        if ($user && $user['username'] === session()->get('user')) {
            return redirect()->to('/admin')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!');
        }
        
        $this->userModel->delete($id);
        return redirect()->to('/admin')->with('success', 'User berhasil dihapus!');
    }
    
    public function updateAccess()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $username = $this->request->getPost('user_update');
        $pages = $this->request->getPost('pages') ?? [];
        $vlans = $this->request->getPost('vlans') ?? [];
        
        $this->pageAccessModel->where('username', $username)->delete();
        foreach ($pages as $page) {
            $this->pageAccessModel->insert([
                'username' => $username,
                'page_name' => $page,
                'permission_type' => 'RW'
            ]);
        }
        
        $this->vlanAccessModel->where('username', $username)->delete();
        foreach ($vlans as $vlan_id) {
            $this->vlanAccessModel->insert([
                'username' => $username,
                'vlan_id' => $vlan_id
            ]);
        }
        
        return redirect()->to('/admin?user_update=' . urlencode($username))->with('success', "Hak akses untuk pengguna $username berhasil diperbarui!");
    }
    
    public function mainEdit()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $top_color = $this->request->getPost('top_color');
        $side_color = $this->request->getPost('side_color');
        $excel_header_color = $this->request->getPost('excel_header_color') ?? '#4e73df';
        
        $this->settingModel->update(1, [
            'top_color' => $top_color,
            'side_color' => $side_color,
            'excel_header_color' => $excel_header_color
        ]);
        
        return redirect()->to('/admin?view=main_edit')->with('success', 'Pengaturan utama berhasil disimpan!');
    }
    
    public function pingControl()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $method = $this->request->getPost('ping_method');
        $this->settingModel->update(1, ['ping_method' => $method]);
        
        return redirect()->to('/admin?view=ping_control')->with('success', 'Metode ping berhasil diubah menjadi ' . strtoupper($method) . '!');
    }
    
    public function scheduleBypass()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $val = $this->request->getPost('schedule_bypass') ? 1 : 0;
        $this->settingModel->update(1, ['schedule_bypass' => $val]);
        
        return redirect()->to('/admin?view=schedule_bypass')->with('success', 'Pengaturan Bypass Schedule Bell berhasil disimpan!');
    }
    
    public function defaultHomepage()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $hp = [
            'read-write' => $this->request->getPost('homepage_read-write') ?? 'index.php',
            'write'      => $this->request->getPost('homepage_write') ?? 'index.php',
            'read'       => $this->request->getPost('homepage_read') ?? 'index.php'
        ];
        
        $this->settingModel->update(1, ['default_homepage' => json_encode($hp)]);
        return redirect()->to('/admin?view=default_homepage')->with('success', 'Default homepage per role berhasil disimpan!');
    }
    
    public function userDefaultHomepage()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $user_homepages = [];
        $users = $this->userModel->findAll();
        foreach ($users as $u) {
            $hp = $this->request->getPost('homepage_user_' . $u['id']);
            if (!empty($hp)) {
                $user_homepages[$u['username']] = $hp;
            }
        }
        
        $this->settingModel->update(1, ['user_homepages' => json_encode($user_homepages)]);
        return redirect()->to('/admin?view=default_homepage')->with('success', 'Default homepage per user berhasil disimpan!');
    }
    
    public function generateBat()
    {
        if (!has_access('admin_management.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $bat_content = "@echo off\r\n:loop\r\necho Memulai Ping ke seluruh IP CCTV...\r\n";
        $bat_content .= "for /F \"tokens=*\" %%A in (ip_list.txt) do (\r\n";
        $bat_content .= "    ping -n 1 -w 1000 %%A > nul\r\n";
        $bat_content .= "    if errorlevel 1 (\r\n";
        $bat_content .= "        curl -s \"http://localhost/itsupport/public/api_save_ping.php?ip=%%A^&status=DOWN\" > nul\r\n";
        $bat_content .= "    ) else (\r\n";
        $bat_content .= "        curl -s \"http://localhost/itsupport/public/api_save_ping.php?ip=%%A^&status=UP\" > nul\r\n";
        $bat_content .= "    )\r\n";
        $bat_content .= ")\r\n";
        $bat_content .= "echo Selesai. Menunggu 5 detik...\r\n";
        $bat_content .= "timeout /t 5 > nul\r\n";
        $bat_content .= "goto loop\r\n";
        
        file_put_contents(FCPATH . 'run_ping.bat', $bat_content);
        
        $ips = [];
        $res = $this->cctvModel->findAll();
        foreach($res as $row) {
            $ips[] = $row['ip_address'];
        }
        file_put_contents(FCPATH . 'ip_list.txt', implode("\r\n", $ips));
        
        return redirect()->to('/admin?view=ping_control')->with('success', 'Berhasil men-generate file run_ping.bat dan ip_list.txt di folder public!');
    }

    public function loginLog()
    {
        if (!has_access('login_log.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $loginLogModel = new \App\Models\LoginLogModel();
        
        $data = [
            'title' => 'Login Log',
            'logs' => $loginLogModel->orderBy('login_time', 'DESC')->findAll(),
            'current_user' => session()->get('user'),
            'current_role' => session()->get('role'),
            'top_color' => $this->settingModel->find(1)['top_color'] ?? '#0077b6',
            'side_color' => $this->settingModel->find(1)['side_color'] ?? '#f0f2f5'
        ];

        return view('admin/login_log', $data);
    }
}
