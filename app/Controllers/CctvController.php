<?php

namespace App\Controllers;

use App\Models\CctvModel;
use App\Models\VlanModel;
use App\Models\NvrModel;
use App\Models\NamaModel;

class CctvController extends BaseController
{
    protected $cctvModel;
    protected $vlanModel;
    protected $nvrModel;
    protected $namaModel;

    public function __construct()
    {
        $this->cctvModel = new CctvModel();
        $this->vlanModel = new VlanModel();
        $this->nvrModel = new NvrModel();
        $this->namaModel = new NamaModel();
    }

    public function index()
    {
        if (!has_access('list_cctv.php')) {
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
        
        $ipPattern = $activeNetwork . '.%';
        $cctvData = $this->cctvModel->like('ip_address', $ipPattern, 'after')->orderBy('id', 'DESC')->findAll();

        $data = [
            'title' => 'Daftar CCTV',
            'vlanList' => $vlanList,
            'activeNetwork' => $activeNetwork,
            'activeVlanName' => $activeVlanName,
            'cctvData' => $cctvData,
            'page_perm' => $this->_getPermission('list_cctv.php'),
        ];
        
        return view('cctv/index', $data);
    }
    
    public function create()
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $data = [
            'title' => 'Tambah CCTV',
            'vlanList' => $this->vlanModel->orderBy('nama_vlan', 'ASC')->findAll(),
            'nvrList' => $this->nvrModel->orderBy('nama_nvr', 'ASC')->findAll(),
            'namaCctvList' => $this->namaModel->orderBy('nama_cctv', 'ASC')->findAll(),
        ];
        
        return view('cctv/create', $data);
    }
    
    public function store()
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $ip = $this->request->getPost('ip_address');
        
        // Validasi Duplikat IP
        $cek = $this->cctvModel->where('ip_address', $ip)->first();
        if ($cek) {
            return redirect()->back()->with('error', 'IP Address sudah digunakan!');
        }
        
        // Handle Upload Image View Pointing
        $imageName = null;
        $file = $this->request->getFile('pointing_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/cctv_pointing/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
            $imageName = $file->getRandomName();
            $file->move($uploadPath, $imageName);
        }

        $this->cctvModel->insert([
            'ip_address' => $ip,
            'channel' => $this->request->getPost('channel'),
            'nvr' => $this->request->getPost('nvr'),
            'nama_cctv' => $this->request->getPost('nama_cctv'),
            'posisi' => $this->request->getPost('posisi'),
            'keterangan' => $this->request->getPost('keterangan'),
            'stream_url' => $this->request->getPost('stream_url'),
            'stream_user' => $this->request->getPost('stream_user'),
            'stream_pass' => $this->request->getPost('stream_pass'),
            'stream_type' => $this->request->getPost('stream_type') ?? 'mjpeg',
            'view_pointing_image' => $imageName,
        ]);
        
        $returnUrl = $this->request->getPost('return_url');
        if (!empty($returnUrl)) {
            return redirect()->to($returnUrl)->with('success', 'Data CCTV berhasil ditambahkan.');
        }
        return redirect()->to('/cctv')->with('success', 'Data CCTV berhasil ditambahkan.');
    }
    
    public function edit($id)
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $cctv = $this->cctvModel->find($id);
        if (!$cctv) return redirect()->to('/cctv');
        
        $data = [
            'title' => 'Edit CCTV',
            'cctv' => $cctv,
            'vlanList' => $this->vlanModel->orderBy('nama_vlan', 'ASC')->findAll(),
            'nvrList' => $this->nvrModel->orderBy('nama_nvr', 'ASC')->findAll(),
            'namaCctvList' => $this->namaModel->orderBy('nama_cctv', 'ASC')->findAll(),
        ];
        
        return view('cctv/edit', $data);
    }
    
    public function update($id)
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $ip = $this->request->getPost('ip_address');
        
        // Validasi duplikat
        $cek = $this->cctvModel->where('ip_address', $ip)->where('id !=', $id)->first();
        if ($cek) {
            return redirect()->back()->with('error', 'IP Address sudah digunakan!');
        }
        
        // Handle Upload Image View Pointing
        $cctv = $this->cctvModel->find($id);
        $imageName = $cctv['view_pointing_image']; // Simpan nama lama secara default
        $file = $this->request->getFile('pointing_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/cctv_pointing/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
            
            // Hapus file lama jika ada
            if ($imageName && file_exists($uploadPath . $imageName)) {
                unlink($uploadPath . $imageName);
            }
            
            $imageName = $file->getRandomName();
            $file->move($uploadPath, $imageName);
        }

        $this->cctvModel->update($id, [
            'ip_address' => $ip,
            'channel' => $this->request->getPost('channel'),
            'nvr' => $this->request->getPost('nvr'),
            'nama_cctv' => $this->request->getPost('nama_cctv'),
            'posisi' => $this->request->getPost('posisi'),
            'keterangan' => $this->request->getPost('keterangan'),
            'stream_url' => $this->request->getPost('stream_url'),
            'stream_user' => $this->request->getPost('stream_user'),
            'stream_pass' => $this->request->getPost('stream_pass'),
            'stream_type' => $this->request->getPost('stream_type') ?? 'mjpeg',
            'view_pointing_image' => $imageName,
        ]);
        
        $returnUrl = $this->request->getPost('return_url');
        if (!empty($returnUrl)) {
            return redirect()->to($returnUrl)->with('success', 'Data CCTV berhasil diupdate.');
        }
        return redirect()->to('/cctv')->with('success', 'Data CCTV berhasil diupdate.');
    }
    
    public function delete($id)
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $cctv = $this->cctvModel->find($id);
        if ($cctv && $cctv['view_pointing_image']) {
            $imgPath = FCPATH . 'uploads/cctv_pointing/' . $cctv['view_pointing_image'];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }
        $this->cctvModel->delete($id);
        return redirect()->back()->with('success', 'Data CCTV berhasil dihapus.');
    }
    
    public function bulkDelete()
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $ids = $this->request->getPost('bulk_ids');
        if (!empty($ids)) {
            $idArray = explode(',', $ids);
            if (count($idArray) > 0) {
                // Hapus file gambar dari server sebelum data dihapus dari db
                $cctvs = $this->cctvModel->whereIn('id', $idArray)->findAll();
                foreach ($cctvs as $cctv) {
                    if ($cctv['view_pointing_image']) {
                        $imgPath = FCPATH . 'uploads/cctv_pointing/' . $cctv['view_pointing_image'];
                        if (file_exists($imgPath)) unlink($imgPath);
                    }
                }
                $this->cctvModel->whereIn('id', $idArray)->delete();
            }
        }
        
        return redirect()->back()->with('success', 'Data CCTV terpilih berhasil dihapus.');
    }

    public function importExcel()
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
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
                        // Skip header, start from index 1
                        for ($i = 1; $i < count($sheetData); $i++) {
                            $row = $sheetData[$i];
                            // Pastikan IP tidak kosong
                            if (!empty($row[0])) {
                                $data = [
                                    'ip_address' => $row[0] ?? '',
                                    'channel'    => $row[1] ?? '',
                                    'nvr'        => $row[2] ?? '',
                                    'nama_cctv'  => $row[3] ?? '',
                                    'posisi'     => $row[4] ?? '',
                                    'keterangan' => $row[5] ?? '',
                                ];
                                
                                $exist = $this->cctvModel->where('ip_address', $data['ip_address'])->first();
                                if ($exist) {
                                    $this->cctvModel->update($exist['id'], $data);
                                } else {
                                    $this->cctvModel->insert($data);
                                }
                                $added++;
                            }
                        }
                        session()->setFlashdata('success', "$added Data CCTV berhasil diimport dari Excel.");
                    } catch (\Exception $e) {
                        session()->setFlashdata('error', 'Gagal memproses file Excel: ' . $e->getMessage());
                    }
                } else {
                    session()->setFlashdata('error', 'Format file harus xls, xlsx, atau csv.');
                }
            } else {
                session()->setFlashdata('error', 'Gagal mengunggah file.');
            }
        }
        return redirect()->to('/cctv');
    }

    public function downloadTemplate()
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Header
        $sheet->setCellValue('A1', 'IP Address');
        $sheet->setCellValue('B1', 'Channel');
        $sheet->setCellValue('C1', 'NVR');
        $sheet->setCellValue('D1', 'Nama CCTV');
        $sheet->setCellValue('E1', 'Posisi');
        $sheet->setCellValue('F1', 'Keterangan');
        
        // Set Column Width explicitly since GD extension is missing for AutoSize
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        
        // Styling Header
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        
        // Sample data
        $sheet->setCellValue('A2', '192.168.1.10');
        $sheet->setCellValue('B2', '1');
        $sheet->setCellValue('C2', 'NVR-Lantai-1');
        $sheet->setCellValue('D2', 'Hikvision 2MP');
        $sheet->setCellValue('E2', 'Lobby Depan');
        $sheet->setCellValue('F2', 'Menghadap Jalan');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Template_Import_CCTV.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function exportExcel()
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }

        $cctvData = $this->cctvModel->orderBy('ip_address', 'ASC')->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Header
        $sheet->setCellValue('A1', 'IP Address');
        $sheet->setCellValue('B1', 'Channel');
        $sheet->setCellValue('C1', 'NVR');
        $sheet->setCellValue('D1', 'Nama CCTV');
        $sheet->setCellValue('E1', 'Posisi');
        $sheet->setCellValue('F1', 'Keterangan');
        
        // Set Column Width explicitly
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        
        // Styling Header
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        
        // Fill data
        $rowNum = 2;
        foreach ($cctvData as $row) {
            $sheet->setCellValue('A' . $rowNum, $row['ip_address']);
            $sheet->setCellValue('B' . $rowNum, $row['channel']);
            $sheet->setCellValue('C' . $rowNum, $row['nvr']);
            $sheet->setCellValue('D' . $rowNum, $row['nama_cctv']);
            $sheet->setCellValue('E' . $rowNum, $row['posisi']);
            $sheet->setCellValue('F' . $rowNum, $row['keterangan']);
            $rowNum++;
        }

        // Add border to data
        if ($rowNum > 2) {
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A2:F' . ($rowNum - 1))->applyFromArray($dataStyle);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Data_CCTV_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function exportExcelByNvr($nvr = '')
    {
        if ($this->_getPermission('list_cctv.php') === 'R') {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }

        // Decode NVR name from URL
        $nvrName = urldecode($nvr);

        // Build query — filter by NVR if provided
        $builder = $this->cctvModel->orderBy('channel', 'ASC');
        if (!empty($nvrName)) {
            $builder = $builder->where('nvr', $nvrName);
        }
        $cctvData = $builder->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title row
        $labelNvr = !empty($nvrName) ? $nvrName : 'Semua NVR';
        $sheet->setCellValue('A1', 'Data CCTV — ' . $labelNvr);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Header row
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Channel');
        $sheet->setCellValue('C2', 'NVR');
        $sheet->setCellValue('D2', 'IP Address');
        $sheet->setCellValue('E2', 'Nama CCTV');
        $sheet->setCellValue('F2', 'Posisi');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(28);
        $sheet->getColumnDimension('F')->setWidth(32);

        // Header styling
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A2:F2')->applyFromArray($headerStyle);

        // Fill data
        $rowNum = 3;
        foreach ($cctvData as $i => $row) {
            $sheet->setCellValue('A' . $rowNum, $i + 1);
            $sheet->setCellValue('B' . $rowNum, $row['channel']);
            $sheet->setCellValue('C' . $rowNum, $row['nvr']);
            $sheet->setCellValue('D' . $rowNum, $row['ip_address']);
            $sheet->setCellValue('E' . $rowNum, $row['nama_cctv']);
            $sheet->setCellValue('F' . $rowNum, $row['posisi']);
            $rowNum++;
        }

        // Data border
        if ($rowNum > 3) {
            $sheet->getStyle('A3:F' . ($rowNum - 1))->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            ]);
        }

        $writer   = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $safeNvr  = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $labelNvr);
        $fileName = 'Data_CCTV_' . $safeNvr . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    private function _getPermission($page)
    {
        return get_permission($page);
    }

    public function cctvByNvr()
    {
        if (!has_access('cctv_by_nvr.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $active_nvr = $this->request->getGet('nvr') ?? '';
        
        if ($active_nvr != '') {
            $cctvData = $this->cctvModel->where('nvr', $active_nvr)->orderBy('ip_address', 'ASC')->findAll();
        } else {
            $cctvData = $this->cctvModel->orderBy('nvr', 'ASC')->orderBy('ip_address', 'ASC')->findAll();
        }

        $nvrList = $this->nvrModel->orderBy('nama_nvr', 'ASC')->findAll();

        $data = [
            'title' => 'Data CCTV Berdasarkan NVR',
            'active_nvr' => $active_nvr,
            'cctvData' => $cctvData,
            'nvrList' => $nvrList,
            'page_perm' => $this->_getPermission('cctv_by_nvr.php'),
        ];
        
        return view('cctv/by_nvr', $data);
    }

    public function searchApi()
    {
        if (!has_access('cctv_by_nvr.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $keyword = $this->request->getGet('keyword');
        $by = $this->request->getGet('by') ?? 'all';
        
        if ($keyword !== null && $keyword !== '') {
            if ($by === 'ip_address') {
                $this->cctvModel->like('ip_address', $keyword);
            } elseif ($by === 'nama_cctv') {
                $this->cctvModel->like('nama_cctv', $keyword);
            } elseif ($by === 'posisi') {
                $this->cctvModel->like('posisi', $keyword);
            } elseif ($by === 'nvr') {
                $this->cctvModel->like('nvr', $keyword);
            } else {
                $this->cctvModel->groupStart()
                                ->like('ip_address', $keyword)
                                ->orLike('nama_cctv', $keyword)
                                ->orLike('posisi', $keyword)
                                ->orLike('nvr', $keyword)
                                ->groupEnd();
            }
        }
        
        $data = $this->cctvModel->orderBy('id', 'DESC')->findAll(50);
        
        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }
    public function viewer()
    {
        if (!has_access('cctv_viewer.php')) {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $cctvData = $this->cctvModel->orderBy('nama_cctv', 'ASC')->findAll();
        
        $data = [
            'title' => 'CCTV Viewer',
            'cctvList' => $cctvData
        ];
        
        return view('cctv/viewer', $data);
    }

    public function streamProxy($id)
    {
        $cctv = $this->cctvModel->find($id);
        if (!$cctv) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        $url = $cctv['stream_url'] ?? '';
        if (empty($url) || !preg_match('/^http/', $url)) {
            // Asumsikan NVR Hikvision endpoint
            $ip = $cctv['ip_address'];
            $channel = !empty($cctv['channel']) ? $cctv['channel'] : 1;
            $url = "http://{$ip}/ISAPI/Streaming/channels/{$channel}02/httppreview";
        }

        $user = !empty($cctv['stream_user']) ? $cctv['stream_user'] : 'admin';
        $pass = !empty($cctv['stream_pass']) ? $cctv['stream_pass'] : '';

        // Penting: Tutup session agar tidak memblokir tab lain yang digunakan user
        session_write_close();
        
        while (ob_get_level()) {
            ob_end_clean();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        
        if (!empty($pass)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST | CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
        }
        
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) {
            // HANYA teruskan header Content-Type (karena header WWW-Authenticate menyebabkan popup login di browser)
            if (stripos($header, 'Content-Type:') === 0) {
                header($header, true);
            }
            return strlen($header);
        });
        
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
            if (connection_aborted()) {
                return 0; // Abort curl if client disconnects
            }
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) {
                echo $data;
                flush();
            }
            return strlen($data);
        });
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        curl_exec($ch);
        curl_close($ch);
        exit;
    }

    public function nvrViewer()
    {
        if (!has_access('nvr_viewer.php')) {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }
        
        $nvrModel = new \App\Models\NvrModel();
        $nvrData = $nvrModel->orderBy('nama_nvr', 'ASC')->findAll();
        
        $cctvModel = new \App\Models\CctvModel();
        $cctvList = $cctvModel->findAll();
        
        $cctvByNvr = [];
        foreach($cctvList as $c) {
            $nvrName = $c['nvr'];
            if(!empty($nvrName) && !empty($c['channel'])) {
                if(!isset($cctvByNvr[$nvrName])) {
                    $cctvByNvr[$nvrName] = [];
                }
                $cctvByNvr[$nvrName][] = [
                    'id' => $c['id'],
                    'channel' => $c['channel'],
                    'nama' => $c['nama_cctv'],
                    'type' => $c['stream_type'] ?? 'mjpeg'
                ];
            }
        }
        
        // Sort channels
        foreach($cctvByNvr as $nvr => &$cctvs) {
            usort($cctvs, function($a, $b) {
                return $a['channel'] <=> $b['channel'];
            });
        }
        
        $data = [
            'title' => 'NVR Viewer',
            'nvrList' => $nvrData,
            'cctvByNvr' => $cctvByNvr
        ];
        
        return view('cctv/nvr_viewer', $data);
    }

    public function nvrStreamProxy($id, $channel = 1)
    {
        $nvrModel = new \App\Models\NvrModel();
        $nvr = $nvrModel->find($id);
        if (!$nvr) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        $url = $nvr['stream_url'] ?? '';
        if (empty($url) || !preg_match('/^http/', $url)) {
            // Asumsikan NVR Hikvision endpoint
            $ip = $nvr['ip_address'] ?? '';
            // Gunakan $channel yang diminta dari parameter
            $url = "http://{$ip}/ISAPI/Streaming/channels/{$channel}02/httppreview";
        } else {
            // Ganti {channel} dengan parameter jika user mengisi URL custom (misal: Dahua)
            $url = str_replace('{channel}', $channel, $url);
        }

        $user = !empty($nvr['stream_user']) ? $nvr['stream_user'] : 'admin';
        $pass = !empty($nvr['stream_pass']) ? $nvr['stream_pass'] : '';

        // Penting: Tutup session agar tidak memblokir tab lain yang digunakan user
        session_write_close();
        
        while (ob_get_level()) {
            ob_end_clean();
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        
        if (!empty($pass)) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST | CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "{$user}:{$pass}");
        }
        
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) {
            // HANYA teruskan header Content-Type
            if (stripos($header, 'Content-Type:') === 0) {
                header($header, true);
            }
            return strlen($header);
        });
        
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
            if (connection_aborted()) {
                return 0; // Abort curl if client disconnects
            }
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) {
                echo $data;
                flush();
            }
            return strlen($data);
        });
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        curl_exec($ch);
        curl_close($ch);
        exit;
    }

    public function nvrConfig()
    {
        if (!has_access('nvr_config.php')) {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }

        $nvrModel = new \App\Models\NvrModel();
        $data = [
            'title' => 'NVR Configuration',
            'nvrList' => $nvrModel->orderBy('nama_nvr', 'ASC')->findAll(),
            'editData' => null
        ];

        $edit_id = $this->request->getGet('edit');
        if ($edit_id) {
            $data['editData'] = $nvrModel->find($edit_id);
        }

        return view('cctv/nvr_config', $data);
    }

    public function nvrConfigUpdate()
    {
        if (!has_access('nvr_config.php')) {
            return redirect()->to('/cctv')->with('error', 'Akses ditolak.');
        }

        $id = $this->request->getPost('id_nvr');
        if (empty($id)) {
            return redirect()->to('/cctv/nvr-config')->with('error', 'ID NVR tidak valid.');
        }

        $nvrModel = new \App\Models\NvrModel();
        $saveData = [
            'nama_nvr' => $this->request->getPost('nama_nvr'),
            'ip_address' => $this->request->getPost('ip_address'),
            'stream_url' => $this->request->getPost('stream_url'),
            'stream_user' => $this->request->getPost('stream_user'),
            'stream_pass' => $this->request->getPost('stream_pass'),
            'stream_type' => $this->request->getPost('stream_type') ?? 'mjpeg',
        ];

        $nvrModel->update($id, $saveData);
        return redirect()->to('/cctv/nvr-config')->with('success', 'Konfigurasi NVR berhasil diperbarui.');
    }
}
