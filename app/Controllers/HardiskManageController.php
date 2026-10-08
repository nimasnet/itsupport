<?php

namespace App\Controllers;

use App\Models\HardiskManageModel;
use App\Models\NvrModel;

class HardiskManageController extends BaseController
{
    protected $hddModel;
    protected $nvrModel;

    public function __construct()
    {
        $this->hddModel = new HardiskManageModel();
        $this->nvrModel = new NvrModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'read-write' && !has_any_access(['hardisk_manage.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }
        $perm = get_permission('hardisk_manage.php');

        $nvrs = $this->nvrModel->orderBy('nama_nvr', 'ASC')->findAll();
        $hdds = $this->hddModel->findAll();
        
        // Map HDDs by NVR and Label
        $hddMap = [];
        foreach ($hdds as $h) {
            $hddMap[$h['nvr_name']][$h['hdd_label']] = $h;
        }

        $data = [
            'title' => 'Hardisk Manage',
            'nvrs'  => $nvrs,
            'hddMap'=> $hddMap,
            'page_perm' => $perm
        ];

        return view('cctv/hardisk_manage', $data);
    }

    public function save()
    {
        if (session()->get('role') !== 'read-write' && !has_any_access(['hardisk_manage.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }
        $perm = get_permission('hardisk_manage.php');
        if ($perm === 'R') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $nvr_name = $this->request->getPost('nvr_name');
        $hdd_label = $this->request->getPost('hdd_label');
        
        $capacity_num = $this->request->getPost('capacity_num');
        $capacity_unit = $this->request->getPost('capacity_unit');
        
        $data = [
            'nvr_name' => $nvr_name,
            'hdd_label' => $hdd_label,
            'brand' => $this->request->getPost('brand'),
            'capacity' => $capacity_num . ' ' . $capacity_unit,
            'serial_number' => $this->request->getPost('serial_number'),
            'status' => $this->request->getPost('status'),
            'remark' => $this->request->getPost('remark')
        ];

        // Check if exists
        $existing = $this->hddModel->where('nvr_name', $nvr_name)->where('hdd_label', $hdd_label)->first();
        
        if ($existing) {
            $this->hddModel->update($existing['id'], $data);
            return redirect()->back()->with('message', 'Data Hardisk berhasil diperbarui.');
        } else {
            $this->hddModel->insert($data);
            return redirect()->back()->with('message', 'Data Hardisk berhasil disimpan.');
        }
    }
}
