<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SettingModel;

class AuthController extends BaseController
{
    public function login()
    {
        $session = session();
        if ($session->get('user')) {
            return redirect()->to($this->getDefaultHomepage());
        }

        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user) {
            $db_pass = $user['password'];
            // Mendukung password lama dengan md5() atau password baru (hash)
            if (md5($password) === $db_pass || password_verify($password, $db_pass)) {
                $session->set([
                    'user' => $user['username'],
                    'role' => $user['role'],
                    'logged_in' => true
                ]);
                
                // Record login log
                $loginLogModel = new \App\Models\LoginLogModel();
                $loginLogModel->insert([
                    'username' => $user['username'],
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getAgentString(),
                    'login_time' => date('Y-m-d H:i:s')
                ]);

                return redirect()->to($this->getDefaultHomepage($user['role']));
            }
        }

        $session->setFlashdata('error', 'Username atau Password salah!');
        return redirect()->to('login');
    }

    public function logout()
    {
        $session = session();
        $session->remove(['user', 'role', 'logged_in']);
        return redirect()->to('login');
    }

    private function getDefaultHomepage($role = null)
    {
        if (!$role) {
            $role = session()->get('role') ?? 'read';
        }
        $username = session()->get('user');
        
        $settingModel = new SettingModel();
        $setting = $settingModel->find(1);
        
        $redirect_to = '/'; // Default fallback (home)
        $target = null;
        
        if ($setting) {
            if (!empty($setting['user_homepages']) && $username) {
                $user_hp_map = json_decode($setting['user_homepages'], true);
                if (isset($user_hp_map[$username]) && !empty($user_hp_map[$username])) {
                    $target = $user_hp_map[$username];
                }
            }

            if (!$target && !empty($setting['default_homepage'])) {
                $hp_map = json_decode($setting['default_homepage'], true);
                $role_hp = strtolower($role);
                if (isset($hp_map[$role_hp])) {
                    $target = $hp_map[$role_hp];
                }
            }
            
            if ($target) {
                $route_map = [
                    'index.php' => '/',
                    'monitoring.php' => '/monitoring/cctv',
                    'monitoring_logs.php' => '/monitoring/cctv/logs',
                    'monitoring_device.php' => '/monitoring/device',
                    'stb_mess.php' => '/stb/mess',
                    'kelola_stb.php' => '/stb/kelola',
                    'graphs.php' => '/tools/graphs',
                    'scan.php' => '/tools/scan',
                    'tambah.php' => '/cctv/create',
                    'master_vlan.php' => '/master/vlan',
                    'admin_management.php' => '/admin'
                ];
                
                if (isset($route_map[$target])) {
                    $redirect_to = $route_map[$target];
                } else {
                    $redirect_to = '/' . str_replace('.php', '', $target);
                }
            }
        }
        
        return ltrim($redirect_to, '/');
    }
}
