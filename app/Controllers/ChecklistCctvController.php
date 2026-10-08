<?php

namespace App\Controllers;

use App\Models\ChecklistCctvModel;
use App\Models\CctvModel;

class ChecklistCctvController extends BaseController
{
    protected $checklistModel;
    protected $cctvModel;

    public function __construct()
    {
        $this->checklistModel = new ChecklistCctvModel();
        $this->cctvModel = new CctvModel();
    }

    private function _getPermission($page)
    {
        $session = session();
        $role = strtolower($session->get('role') ?? '');
        $username = $session->get('user');

        if ($role === 'read-write') {
            return 'RW';
        }

        $db = \Config\Database::connect();
        $access = $db->table('page_access')
            ->where('username', $username)
            ->where('page_name', $page)
            ->get()
            ->getRow();

        if ($access) {
            return $access->access_level;
        }
        
        return null;
    }

    public function index()
    {
        if (!has_access('checklist_status.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $session = session();
        $role = strtolower($session->get('role') ?? '');
        
        $active_nvr = $this->request->getGet('nvr') ?? '';
        
        if ($active_nvr) {
            $data['checklists'] = $this->checklistModel->where('nvr', $active_nvr)->orderBy('id', 'DESC')->findAll();
        } else {
            $data['checklists'] = $this->checklistModel->orderBy('id', 'DESC')->findAll();
        }
        
        $data['active_nvr'] = $active_nvr;
        $data['nvrList'] = $this->cctvModel->select('nvr as nama_nvr')->distinct()->orderBy('nvr', 'ASC')->findAll();

        $data['can_edit'] = ($role === 'read-write' || $role === 'write' || $this->_getPermission('checklist_status.php') === 'RW');
        $data['title'] = 'Checklist Status CCTV';

        return view('checklist_cctv/index', $data);
    }

    public function create()
    {
        if ($this->_getPermission('checklist_status.php') === 'R') {
            return redirect()->to('/checklist-cctv')->with('error', 'Akses ditolak. Anda hanya memiliki izin baca.');
        }

        $data['title'] = 'Tambah Checklist Status';


        // Get unique NVR and Channels for dropdowns
        $data['nvrs'] = $this->cctvModel->select('nvr')->distinct()->orderBy('nvr', 'ASC')->findAll();
        $data['channels'] = $this->cctvModel->select('channel')->distinct()->orderBy('channel', 'ASC')->findAll();
        $data['pic_check'] = session()->get('user');
        
        return view('checklist_cctv/create', $data);
    }

    public function store()
    {
        if ($this->_getPermission('checklist_status.php') === 'R') {
            return redirect()->to('/checklist-cctv')->with('error', 'Akses ditolak.');
        }

        $tanggal = $this->request->getPost('tanggal');
        $jam = $this->request->getPost('jam');
        $pic_check = $this->request->getPost('pic_check');
        $nvr = $this->request->getPost('nvr');
        
        $checklists = $this->request->getPost('checklists');

        if (is_array($checklists)) {
            $inserted = 0;
            foreach ($checklists as $item) {
                if (empty($item['status'])) {
                    continue; // Skip if status is not selected
                }
                $data = [
                    'tanggal' => $tanggal,
                    'jam' => $jam,
                    'pic_check' => $pic_check,
                    'nvr' => $nvr,
                    'channel' => $item['channel'],
                    'nama_cctv' => $item['nama_cctv'],
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'] ?? ''
                ];
                $this->checklistModel->insert($data);
                $inserted++;
            }
            return redirect()->to('/checklist-cctv')->with('success', "$inserted data Checklist berhasil ditambahkan.");
        } else {
            $data = [
                'tanggal' => $tanggal,
                'jam' => $jam,
                'pic_check' => $pic_check,
                'nvr' => $nvr,
                'channel' => $this->request->getPost('channel'),
                'nama_cctv' => $this->request->getPost('nama_cctv'),
                'status' => $this->request->getPost('status'),
                'keterangan' => $this->request->getPost('keterangan') ?? ''
            ];

            $this->checklistModel->insert($data);
            return redirect()->to('/checklist-cctv')->with('success', 'Data Checklist berhasil ditambahkan.');
        }
    }

    public function edit($id)
    {
        if ($this->_getPermission('checklist_status.php') === 'R') {
            return redirect()->to('/checklist-cctv')->with('error', 'Akses ditolak. Anda hanya memiliki izin baca.');
        }

        $data['checklist'] = $this->checklistModel->find($id);
        if (!$data['checklist']) {
            return redirect()->to('/checklist-cctv')->with('error', 'Data tidak ditemukan.');
        }

        $data['title'] = 'Edit Checklist Status';


        $data['nvrs'] = $this->cctvModel->select('nvr')->distinct()->orderBy('nvr', 'ASC')->findAll();
        $data['channels'] = $this->cctvModel->where('nvr', $data['checklist']['nvr'])->select('channel')->distinct()->orderBy('channel', 'ASC')->findAll();
        $data['cctvs'] = $this->cctvModel->where('nvr', $data['checklist']['nvr'])->orderBy('channel', 'ASC')->findAll();
        
        return view('checklist_cctv/edit', $data);
    }

    public function update($id)
    {
        if ($this->_getPermission('checklist_status.php') === 'R') {
            return redirect()->to('/checklist-cctv')->with('error', 'Akses ditolak.');
        }

        $data = [
            'tanggal' => $this->request->getPost('tanggal'),
            'jam' => $this->request->getPost('jam'),
            'pic_check' => $this->request->getPost('pic_check'),
            'nvr' => $this->request->getPost('nvr'),
            'channel' => $this->request->getPost('channel'),
            'nama_cctv' => $this->request->getPost('nama_cctv'),
            'status' => $this->request->getPost('status'),
            'keterangan' => $this->request->getPost('keterangan') ?? ''
        ];

        $this->checklistModel->update($id, $data);
        return redirect()->to('/checklist-cctv')->with('success', 'Data Checklist berhasil diupdate.');
    }

    public function delete($id)
    {
        if ($this->_getPermission('checklist_status.php') === 'R') {
            return redirect()->to('/checklist-cctv')->with('error', 'Akses ditolak.');
        }

        $this->checklistModel->delete($id);
        return redirect()->to('/checklist-cctv')->with('success', 'Data berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if ($this->_getPermission('checklist_status.php') === 'R') {
            return redirect()->to('/checklist-cctv')->with('error', 'Akses ditolak.');
        }

        $ids = $this->request->getPost('ids');
        if (!empty($ids) && is_array($ids)) {
            $this->checklistModel->whereIn('id', $ids)->delete();
            return redirect()->to('/checklist-cctv')->with('success', count($ids) . ' data berhasil dihapus.');
        }
        return redirect()->to('/checklist-cctv')->with('error', 'Tidak ada data yang dipilih.');
    }

    public function getNamaCctv()
    {
        $nvr = $this->request->getPost('nvr');
        $channel = $this->request->getPost('channel');
        
        if ($nvr && $channel) {
            $cctv = $this->cctvModel->where('nvr', $nvr)->where('channel', $channel)->first();
            if ($cctv) {
                return $this->response->setJSON(['status' => 'success', 'nama_cctv' => $cctv['nama_cctv']]);
            }
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Tanggal (YYYY-MM-DD)');
        $sheet->setCellValue('B1', 'Jam (HH:MM)');
        $sheet->setCellValue('C1', 'PIC Check');
        $sheet->setCellValue('D1', 'NVR');
        $sheet->setCellValue('E1', 'Channel');
        $sheet->setCellValue('F1', 'Nama CCTV');
        $sheet->setCellValue('G1', 'Status');
        $sheet->setCellValue('H1', 'Notes');
        
        // Add styling
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(30);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Template_Checklist_Status.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function exportExcel()
    {
        $nvr = $this->request->getGet('nvr') ?? $this->request->getPost('nvr');
        $ids = $this->request->getPost('ids'); 
        
        $builder = $this->checklistModel->builder();
        
        if (!empty($ids)) {
            $idArray = is_array($ids) ? $ids : explode(',', $ids);
            $builder->whereIn('id', $idArray);
        } else if ($nvr) {
            $builder->where('nvr', urldecode($nvr));
        }
        
        $builder->orderBy('id', 'DESC');
        $checklists = $builder->get()->getResultArray();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Jam');
        $sheet->setCellValue('D1', 'PIC Check');
        $sheet->setCellValue('E1', 'NVR');
        $sheet->setCellValue('F1', 'Channel');
        $sheet->setCellValue('G1', 'Nama CCTV');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Notes');
        
        $row = 2;
        $no = 1;
        foreach ($checklists as $c) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $c['tanggal']);
            $sheet->setCellValue('C' . $row, $c['jam']);
            $sheet->setCellValue('D' . $row, $c['pic_check']);
            $sheet->setCellValue('E' . $row, $c['nvr']);
            $sheet->setCellValue('F' . $row, $c['channel']);
            $sheet->setCellValue('G' . $row, $c['nama_cctv']);
            $sheet->setCellValue('H' . $row, $c['status']);
            $sheet->setCellValue('I' . $row, $c['keterangan']);
            $row++;
        }
        
        // Add styling
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        if ($row > 2) {
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A2:I' . ($row - 1))->applyFromArray($dataStyle);
        }
        
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(30);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'Data_Checklist_Status_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function importExcel()
    {
        if ($this->_getPermission('checklist_status.php') === 'R') {
            return redirect()->to('/checklist-cctv')->with('error', 'Akses ditolak.');
        }

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('file_excel');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $extension = $file->getClientExtension();
                if (in_array($extension, ['xls', 'xlsx', 'csv'])) {
                    try {
                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getTempName());
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($file->getTempName());
                        $sheetData = $spreadsheet->getActiveSheet()->toArray();

                        $added = 0;
                        for ($i = 1; $i < count($sheetData); $i++) {
                            $rowData = $sheetData[$i];
                            if (!empty($rowData[0])) { // Ensure date is not empty
                                $data = [
                                    'tanggal' => $rowData[0] ?? date('Y-m-d'),
                                    'jam' => $rowData[1] ?? date('H:i'),
                                    'pic_check' => $rowData[2] ?? session()->get('user'),
                                    'nvr' => $rowData[3] ?? '',
                                    'channel' => $rowData[4] ?? '',
                                    'nama_cctv' => $rowData[5] ?? '',
                                    'status' => $rowData[6] ?? '',
                                    'keterangan' => $rowData[7] ?? ''
                                ];
                                $this->checklistModel->insert($data);
                                $added++;
                            }
                        }
                        return redirect()->to('/checklist-cctv')->with('success', "$added data berhasil diimport.");
                    } catch (\Exception $e) {
                        return redirect()->to('/checklist-cctv')->with('error', 'Gagal memproses file: ' . $e->getMessage());
                    }
                }
                return redirect()->to('/checklist-cctv')->with('error', 'Format file tidak didukung.');
            }
            return redirect()->to('/checklist-cctv')->with('error', 'Pilih file yang valid.');
        }
        return redirect()->to('/checklist-cctv');
    }

    public function getCctvsByNvr()
    {
        $nvr = $this->request->getPost('nvr');
        if ($nvr) {
            $cctvs = $this->cctvModel->where('nvr', $nvr)->orderBy('channel', 'ASC')->findAll();
            return $this->response->setJSON(['status' => 'success', 'cctvs' => $cctvs]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'NVR tidak ditemukan']);
    }
    public function updateItRespon()
    {
        // Hanya admin (username) atau read-write yang boleh menggunakan fitur ini
        $role = strtolower(session()->get('role') ?? '');
        $username = session()->get('user') ?? '';
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

        // Bangun catatan baru yang akan di-append
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
            'it_respon'   => $it_respon,
            'keterangan'  => $combined,
            'image'       => $imageName
        ]);

        return $this->response->setJSON([
            'status'      => 'success',
            'message'     => 'IT Respon berhasil diperbarui.',
            'it_respon'   => $it_respon,
            'keterangan'  => $combined,
            'image'       => $imageName
        ]);
    }

    public function search_api()
    {
        $keyword = $this->request->getGet('keyword') ?? '';
        $by = $this->request->getGet('by') ?? 'all';
        $nvr = $this->request->getGet('nvr') ?? '';

        $builder = $this->checklistModel->builder();

        if ($nvr) {
            $builder->where('nvr', $nvr);
        }

        if ($keyword) {
            $builder->groupStart();
            if ($by === 'all') {
                $builder->like('nama_cctv', $keyword);
                $builder->orLike('pic_check', $keyword);
                $builder->orLike('nvr', $keyword);
                $builder->orLike('status', $keyword);
                $builder->orLike('keterangan', $keyword);
            } else {
                $builder->like($by, $keyword);
            }
            $builder->groupEnd();
        }

        $builder->orderBy('id', 'DESC');
        $data = $builder->get()->getResultArray();

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }
}
