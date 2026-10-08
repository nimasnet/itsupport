<?php

namespace App\Controllers;

use App\Models\VlanModel;
use App\Models\NvrModel;
use App\Models\NamaModel;

class MasterController extends BaseController
{
    protected $vlanModel;
    protected $nvrModel;
    protected $namaModel;

    public function __construct()
    {
        $this->vlanModel = new VlanModel();
        $this->nvrModel = new NvrModel();
        $this->namaModel = new NamaModel();
    }

    // ==========================================
    // MASTER VLAN
    // ==========================================
    public function vlan()
    {
        // Cek akses
        if (!has_access('master_vlan.php')) {
            return redirect()->to('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $data = [
            'title' => 'Master Jaringan VLAN',
            'vlanList' => $this->vlanModel->orderBy('network_ip', 'ASC')->findAll(),
            'page_perm' => $this->_getPermission('master_vlan.php'),
            'id_edit' => $this->request->getGet('edit_vlan'),
        ];
        
        if ($data['id_edit']) {
            $data['editData'] = $this->vlanModel->find($data['id_edit']);
        }

        return view('master/vlan', $data);
    }

    public function storeVlan()
    {
        if ($this->_getPermission('master_vlan.php') === 'R') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $id = $this->request->getPost('id_vlan');
        $nama_vlan = $this->request->getPost('nama_vlan');
        $network_ip = rtrim($this->request->getPost('network_ip'), '.');

        if (!empty($nama_vlan) && !empty($network_ip)) {
            $saveData = [
                'nama_vlan' => $nama_vlan,
                'network_ip' => $network_ip
            ];
            
            if (!empty($id)) {
                $this->vlanModel->update($id, $saveData);
            } else {
                $this->vlanModel->insert($saveData);
            }
        }
        return redirect()->to('/master/vlan');
    }

    public function deleteVlan($id)
    {
        if ($this->_getPermission('master_vlan.php') === 'R') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $this->vlanModel->delete($id);
        return redirect()->to('/master/vlan');
    }

    // ==========================================
    // MASTER NVR
    // ==========================================
    public function nvr()
    {
        if (!has_access('master_nvr.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Master NVR',
            'nvrList' => $this->nvrModel->orderBy('nama_nvr', 'ASC')->findAll(),
            'page_perm' => $this->_getPermission('master_nvr.php'),
            'id_edit' => $this->request->getGet('edit_nvr'),
        ];
        
        if ($data['id_edit']) {
            $data['editData'] = $this->nvrModel->find($data['id_edit']);
        }

        return view('master/nvr', $data);
    }

    public function storeNvr()
    {
        if ($this->_getPermission('master_nvr.php') === 'R') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $id = $this->request->getPost('id_nvr');
        $nama_nvr = $this->request->getPost('nama_nvr');

        if (!empty($nama_nvr)) {
            $saveData = [
                'nama_nvr' => $nama_nvr,
                'ip_address' => $this->request->getPost('ip_address'),
                'stream_url' => $this->request->getPost('stream_url'),
                'stream_user' => $this->request->getPost('stream_user'),
                'stream_pass' => $this->request->getPost('stream_pass'),
                'stream_type' => $this->request->getPost('stream_type') ?? 'mjpeg',
            ];
            if (!empty($id)) {
                $this->nvrModel->update($id, $saveData);
            } else {
                $this->nvrModel->insert($saveData);
            }
        }
        return redirect()->to('/master/nvr');
    }

    public function deleteNvr($id)
    {
        if ($this->_getPermission('master_nvr.php') === 'R') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $this->nvrModel->delete($id);
        return redirect()->to('/master/nvr');
    }

    // ==========================================
    // MASTER NAMA CCTV
    // ==========================================
    public function nama()
    {
        if (!has_access('master_nama.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Master Nama CCTV',
            'namaList' => $this->namaModel->orderBy('nama_cctv', 'ASC')->findAll(),
            'page_perm' => $this->_getPermission('master_nama.php'),
            'id_edit' => $this->request->getGet('edit_nama'),
        ];
        
        if ($data['id_edit']) {
            $data['editData'] = $this->namaModel->find($data['id_edit']);
        }

        return view('master/nama', $data);
    }

    public function storeNama()
    {
        if ($this->_getPermission('master_nama.php') === 'R') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $id = $this->request->getPost('id_nama');
        $nama_cctv = $this->request->getPost('nama_cctv');

        if (!empty($nama_cctv)) {
            $saveData = ['nama_cctv' => $nama_cctv];
            if (!empty($id)) {
                $this->namaModel->update($id, $saveData);
            } else {
                $this->namaModel->insert($saveData);
            }
        }
        return redirect()->to('/master/nama');
    }

    public function deleteNama($id)
    {
        if ($this->_getPermission('master_nama.php') === 'R') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $this->namaModel->delete($id);
        return redirect()->to('/master/nama');
    }
    
    // Helper untuk permission R/W berdasarkan session
    private function _getPermission($page)
    {
        return get_permission($page);
    }
}
