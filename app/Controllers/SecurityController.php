<?php

namespace App\Controllers;

use App\Models\AlarmPanelModel;
use App\Models\AlarmEventModel;

class SecurityController extends BaseController
{
    public function index()
    {
        if (!has_access('security_monitoring.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $panelModel = new AlarmPanelModel();
        $data = [
            'title'  => 'Security Monitoring',
            'panels' => $panelModel->findAll()
        ];
        return view('security/index', $data);
    }

    public function getEvents()
    {
        $eventModel = new AlarmEventModel();
        $panelModel = new AlarmPanelModel();

        $events = $eventModel->orderBy('event_time', 'DESC')->limit(100)->findAll();

        $panelMap = [];
        foreach ($panelModel->findAll() as $p) {
            $panelMap[$p['id']] = $p['name'];
        }

        foreach ($events as &$e) {
            $e['panel_name'] = $e['panel_id'] ? ($panelMap[$e['panel_id']] ?? 'Unknown') : 'Unregistered IP';
        }

        return $this->response->setJSON($events);
    }

    public function savePanel()
    {
        if (!has_access('security_monitoring.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $panelModel = new AlarmPanelModel();
        $id = $this->request->getPost('id');
        $data = [
            'name'             => $this->request->getPost('name'),
            'ip_address'       => $this->request->getPost('ip_address'),
            'port'             => $this->request->getPost('port') ?: 80,
            'username'         => $this->request->getPost('username'),
            'password'         => $this->request->getPost('password'),
            'endpoint_arm'     => $this->request->getPost('endpoint_arm'),
            'endpoint_disarm'  => $this->request->getPost('endpoint_disarm')
        ];

        if ($id) {
            $panelModel->update($id, $data);
        } else {
            $panelModel->insert($data);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function deletePanel($id)
    {
        if (!has_access('security_monitoring.php')) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $panelModel = new AlarmPanelModel();
        $panelModel->delete($id);
        return redirect()->back()->with('success', 'Panel berhasil dihapus.');
    }

    public function remoteControl()
    {
        if (!has_access('security_monitoring.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $panelModel = new AlarmPanelModel();
        $id     = $this->request->getPost('id');
        $action = $this->request->getPost('action'); // 'arm' or 'disarm'

        $panel = $panelModel->find($id);
        if (!$panel) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Panel tidak ditemukan.']);
        }

        $endpoint = ($action === 'arm') ? $panel['endpoint_arm'] : $panel['endpoint_disarm'];
        if (empty($endpoint)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Endpoint URL belum dikonfigurasi untuk aksi ini.']);
        }

        $url = "http://" . $panel['ip_address'] . ":" . $panel['port'] . "/" . ltrim($endpoint, '/');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        if (!empty($panel['username'])) {
            curl_setopt($ch, CURLOPT_USERPWD, $panel['username'] . ":" . $panel['password']);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Perintah berhasil dikirim!', 'response' => $response]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => "Gagal mengirim perintah. HTTP Code: $httpCode. Error: $error"]);
        }
    }
}
