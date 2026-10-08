<?php

namespace App\Controllers;

use App\Models\ChecklistCctvModel;
use App\Models\CctvModel;

class ItResponActionsController extends BaseController
{
    protected $checklistModel;
    protected $cctvModel;

    public function __construct()
    {
        $this->checklistModel = new ChecklistCctvModel();
        $this->cctvModel      = new CctvModel();
    }

    public function index()
    {
        if (!has_access('it_respon_actions.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $session  = session();
        $role     = strtolower($session->get('role') ?? '');
        $username = $session->get('user') ?? '';
        // Tampilkan opsi IT Respon untuk user admin (username) atau role read-write
        $is_admin = ($username === 'admin' || $role === 'read-write');

        $active_nvr = $this->request->getGet('nvr') ?? '';

        $db = \Config\Database::connect();
        
        // Dapatkan max_id untuk setiap NVR + Channel (data update terbaru)
        $subqueryStr = $db->table('checklist_cctv')
                          ->select('MAX(id) as max_id')
                          ->groupBy('nvr, channel')
                          ->getCompiledSelect();

        $builder = $this->checklistModel->builder();
        $builder->where("id IN ($subqueryStr)", null, false);

        // Filter hanya status yang bermasalah (tidak mengandung kata 'Aktif')
        // atau jika it_respon telah di-set menjadi 'Solved' pada record terakhir tersebut
        $builder->groupStart()
                ->like('status', 'Offline')
                ->orLike('status', "Un'Record")
                ->orLike('status', 'Error')
                ->orLike('status', 'Maintenance')
                ->orLike('status', 'Bermasalah')
                ->orLike('status', 'Mati')
                ->orLike('status', 'Blank')
                ->orLike('status', 'Trouble')
                ->orWhere('it_respon', 'Solved')
                ->groupEnd();

        if ($active_nvr) {
            $builder->where('nvr', $active_nvr);
        }

        $builder->orderBy('id', 'DESC');
        $checklists = $builder->get()->getResultArray();

        $data['checklists']  = $checklists;
        $data['active_nvr']  = $active_nvr;
        $data['nvrList']     = $this->cctvModel->select('nvr as nama_nvr')->distinct()->orderBy('nvr', 'ASC')->findAll();
        $data['is_admin']    = $is_admin;
        $data['title']       = 'IT Respon Actions';

        return view('checklist_cctv/it_respon_actions', $data);
    }

    public function updateItRespon()
    {
        $role = strtolower(session()->get('role') ?? '');
        $username = session()->get('user') ?? '';
        // Hanya admin (username) atau read-write yang boleh update
        if ($username !== 'admin' && $role !== 'read-write') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $id          = $this->request->getPost('id');
        $it_respon   = $this->request->getPost('it_respon');
        $respon_date = $this->request->getPost('respon_date');
        $respon_time = $this->request->getPost('respon_time');
        $respon_note = $this->request->getPost('respon_note');

        if (!$id || !$it_respon) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }

        $existing = $this->checklistModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        $pic       = session()->get('user');
        $datetime  = $respon_date . ' ' . $respon_time;
        $separator = "\n--- IT Respon Update ---";
        $new_note  = "**[{$it_respon}]** {$datetime} (by {$pic}): {$respon_note}";

        $old_keterangan = $existing['keterangan'] ?? '';
        $combined = $old_keterangan ? $old_keterangan . "\n" . $separator . "\n" . $new_note : $new_note;

        // --- IMAGE HANDLING ---
        $uploadDir = FCPATH . 'uploads/it_respon';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = $existing['image']; // retain old image if not updated

        // Priority 1: camera base64 image
        $camera_image = $this->request->getPost('camera_image');
        if (!empty($camera_image) && strpos($camera_image, 'base64,') !== false) {
            $parts      = explode(";base64,", $camera_image);
            $imageBase64 = base64_decode($parts[1]);
            $imageName  = uniqid('ir_') . '.png';
            file_put_contents($uploadDir . DIRECTORY_SEPARATOR . $imageName, $imageBase64);
        }

        // Priority 2: file upload
        if (empty($camera_image)) {
            $imageFile = $this->request->getFile('image_file');
            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $imageName = $imageFile->getRandomName();
                $imageFile->move($uploadDir, $imageName);
            }
        }
        // --- END IMAGE HANDLING ---

        $this->checklistModel->update($id, [
            'it_respon'  => $it_respon,
            'keterangan' => $combined,
            'image'      => $imageName
        ]);

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'IT Respon berhasil diperbarui.',
            'it_respon'  => $it_respon,
            'keterangan' => $combined,
            'image'      => $imageName
        ]);
    }
}
