<?php

namespace App\Controllers;

class EacChecklistController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Fetch EAC Checklist data
        $checklistData = $db->table('eac_checklist')
                            ->orderBy('created_at', 'DESC')
                            ->get()
                            ->getResultArray();

        // Fetch switch_network data for location dropdown
        $switchNetworks = $db->table('switch_network')
                             ->orderBy('name_switch', 'ASC')
                             ->get()
                             ->getResultArray();

        $data = [
            'title'          => 'EAC Checklist',
            'checklistData'  => $checklistData,
            'switchNetworks' => $switchNetworks
        ];
        return view('eac_checklist/index', $data);
    }
    
    public function store()
    {
        $db = \Config\Database::connect();

        // Resolve location: sent as 'eac_location' (hidden final field)
        $eac_location   = $this->request->getPost('eac_location');
        $eac_conditions = $this->request->getPost('eac_conditions');
        $date_update    = $this->request->getPost('date_update');

        $uploadDir = FCPATH . 'uploads/eac';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = null;

        // Priority 1: camera base64 image
        $camera_image = $this->request->getPost('camera_image');
        if (!empty($camera_image) && strpos($camera_image, 'base64,') !== false) {
            $parts      = explode(";base64,", $camera_image);
            $imageBase64 = base64_decode($parts[1]);
            $imageName  = uniqid('eac_') . '.png';
            file_put_contents($uploadDir . DIRECTORY_SEPARATOR . $imageName, $imageBase64);
        }

        // Priority 2: file upload
        if (empty($imageName)) {
            $image = $this->request->getFile('image');
            if ($image && $image->isValid() && !$image->hasMoved()) {
                $imageName = $image->getRandomName();
                $image->move($uploadDir, $imageName);
            }
        }

        $insertData = [
            'eac_location'   => $eac_location,
            'eac_conditions' => $eac_conditions,
            'date_update'    => $date_update,
            'image'          => $imageName
        ];

        $db->table('eac_checklist')->insert($insertData);

        return redirect()->to('monitoring-support/eac-checklist')
                         ->with('success', 'Data EAC Checklist berhasil disimpan!');
    }

    public function manageList()
    {
        $db = \Config\Database::connect();
        $locations = $db->table('eac_locations')
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();

        $data = [
            'title'     => 'EAC Manage List',
            'locations' => $locations
        ];
        return view('eac_checklist/manage_list', $data);
    }

    public function storeLocation()
    {
        $db = \Config\Database::connect();
        $location_name = $this->request->getPost('location_name');

        if (!empty($location_name)) {
            $db->table('eac_locations')->insert([
                'location_name' => $location_name
            ]);
            return redirect()->to('monitoring-support/eac-manage-list')
                             ->with('success', 'Lokasi EAC berhasil ditambahkan!');
        }

        return redirect()->to('monitoring-support/eac-manage-list')
                         ->with('error', 'Nama lokasi tidak boleh kosong!');
    }

    public function deleteLocation($id)
    {
        $db = \Config\Database::connect();
        $db->table('eac_locations')->where('id', $id)->delete();

        return redirect()->to('monitoring-support/eac-manage-list')
                         ->with('success', 'Lokasi EAC berhasil dihapus!');
    }
}
