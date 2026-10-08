<?php

namespace App\Controllers;

use App\Models\StbModel;

class StbController extends BaseController
{
    protected $stbModel;

    public function __construct()
    {
        $this->stbModel = new StbModel();
    }

    public function index()
    {
        if (!has_access('stb_mess.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $search = $this->request->getGet('search') ?? '';
        $sort_by = $this->request->getGet('sort_by') ?? 'lokasi';
        $sort_dir = $this->request->getGet('sort_dir') ?? 'ASC';

        if (!empty($search)) {
            $this->stbModel->like('kamar_no', $search)
                           ->orLike('nama_user', $search)
                           ->orLike('lokasi', $search)
                           ->orLike('ip_address', $search)
                           ->orLike('keterangan', $search);
        }
        
        $allowed_sort = ['kamar_no', 'nama_user', 'lokasi', 'ip_address', 'id'];
        if (!in_array($sort_by, $allowed_sort)) $sort_by = 'lokasi';
        $sort_dir = strtoupper($sort_dir) === 'DESC' ? 'DESC' : 'ASC';
        
        $dataStb = $this->stbModel->orderBy($sort_by, $sort_dir)->findAll();
        
        $data = [
            'title' => 'Data STB MESS',
            'stbList' => $dataStb,
            'search' => $search,
            'sort_by' => $sort_by,
            'sort_dir' => $sort_dir,
            'page_perm' => get_permission('stb_mess.php')
        ];

        return view('stb/index', $data);
    }
    
    public function exportExcel()
    {
        if (!has_access('stb_mess.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $search = $this->request->getGet('search') ?? '';
        $sort_by = $this->request->getGet('sort_by') ?? 'lokasi';
        $sort_dir = $this->request->getGet('sort_dir') ?? 'ASC';

        if (!empty($search)) {
            $this->stbModel->like('kamar_no', $search)
                           ->orLike('nama_user', $search)
                           ->orLike('lokasi', $search)
                           ->orLike('ip_address', $search)
                           ->orLike('keterangan', $search);
        }
        $allowed_sort = ['kamar_no', 'nama_user', 'lokasi', 'ip_address', 'id'];
        if (!in_array($sort_by, $allowed_sort)) $sort_by = 'lokasi';
        $sort_dir = strtoupper($sort_dir) === 'DESC' ? 'DESC' : 'ASC';
        
        $dataStb = $this->stbModel->orderBy($sort_by, $sort_dir)->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Kamar No.');
        $sheet->setCellValue('B1', 'Nama User');
        $sheet->setCellValue('C1', 'Lokasi');
        $sheet->setCellValue('D1', 'IP Address');
        $sheet->setCellValue('E1', 'Keterangan');
        
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(35);
        
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        
        $rowNum = 2;
        foreach ($dataStb as $row) {
            $sheet->setCellValue('A' . $rowNum, $row['kamar_no']);
            $sheet->setCellValue('B' . $rowNum, $row['nama_user']);
            $sheet->setCellValue('C' . $rowNum, $row['lokasi']);
            $sheet->setCellValue('D' . $rowNum, $row['ip_address']);
            $sheet->setCellValue('E' . $rowNum, $row['keterangan']);
            $rowNum++;
        }

        if ($rowNum > 2) {
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A2:E' . ($rowNum - 1))->applyFromArray($dataStyle);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Data_STB_MESS_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function create()
    {
        if (get_permission('kelola_stb.php') === 'R') {
            return redirect()->to('/stb/mess')->with('error', 'Akses ditolak.');
        }
        
        $data = [
            'title' => 'Tambah Data STB',
            'edit_data' => null
        ];
        return view('stb/kelola', $data);
    }
    
    public function edit($id)
    {
        if (get_permission('kelola_stb.php') === 'R') {
            return redirect()->to('/stb/mess')->with('error', 'Akses ditolak.');
        }
        
        $stb = $this->stbModel->find($id);
        if (!$stb) {
            return redirect()->to('/stb/mess')->with('error', 'Data tidak ditemukan.');
        }
        
        $data = [
            'title' => 'Edit Data STB',
            'edit_data' => $stb
        ];
        return view('stb/kelola', $data);
    }

    public function store()
    {
        if (get_permission('kelola_stb.php') === 'R') {
            return redirect()->to('/stb/mess')->with('error', 'Akses ditolak.');
        }
        
        $id = $this->request->getPost('id');
        $saveData = [
            'kamar_no' => $this->request->getPost('kamar_no'),
            'nama_user' => $this->request->getPost('nama_user'),
            'lokasi' => $this->request->getPost('lokasi'),
            'ip_address' => $this->request->getPost('ip_address'),
            'keterangan' => $this->request->getPost('keterangan')
        ];
        
        if ($id) {
            $this->stbModel->update($id, $saveData);
        } else {
            $this->stbModel->insert($saveData);
        }
        
        return redirect()->to('/stb/mess')->with('success', 'Data berhasil disimpan.');
    }
    
    public function delete($id)
    {
        if (get_permission('kelola_stb.php') === 'R') {
            return redirect()->to('/stb/mess')->with('error', 'Akses ditolak.');
        }
        
        $this->stbModel->delete($id);
        return redirect()->to('/stb/mess')->with('success', 'Data berhasil dihapus.');
    }

    public function importExcel()
    {
        if (get_permission('stb_mess.php') === 'R') {
            return redirect()->to('/stb/mess')->with('error', 'Akses ditolak.');
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
                            $row = $sheetData[$i];
                            // Pastikan IP atau Kamar tidak kosong
                            if (!empty($row[0]) || !empty($row[3])) {
                                $data = [
                                    'kamar_no'   => $row[0] ?? '',
                                    'nama_user'  => $row[1] ?? '',
                                    'lokasi'     => $row[2] ?? '',
                                    'ip_address' => $row[3] ?? '',
                                    'keterangan' => $row[4] ?? '',
                                ];
                                
                                $exist = $this->stbModel->where('ip_address', $data['ip_address'])->first();
                                if ($exist && !empty($data['ip_address']) && $data['ip_address'] !== '-') {
                                    $this->stbModel->update($exist['id'], $data);
                                } else {
                                    $this->stbModel->insert($data);
                                }
                                $added++;
                            }
                        }
                        session()->setFlashdata('success', "$added Data STB berhasil diimport dari Excel.");
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
        return redirect()->to('/stb/mess');
    }

    public function downloadTemplate()
    {
        if (get_permission('stb_mess.php') === 'R') {
            return redirect()->to('/stb/mess')->with('error', 'Akses ditolak.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Kamar No.');
        $sheet->setCellValue('B1', 'Nama User');
        $sheet->setCellValue('C1', 'Lokasi');
        $sheet->setCellValue('D1', 'IP Address');
        $sheet->setCellValue('E1', 'Keterangan');
        
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(35);
        
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        
        $sheet->setCellValue('A2', '101');
        $sheet->setCellValue('B2', 'Budi Santoso');
        $sheet->setCellValue('C2', 'Lantai 1');
        $sheet->setCellValue('D2', '192.168.46.101');
        $sheet->setCellValue('E2', 'STB Normal');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Template_Import_STB.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }
}
