<?php

namespace App\Controllers;

use App\Models\IpManagementModel;
use App\Models\SwitchNetworkModel;
use App\Models\CekroolModel;

class IpManagementController extends BaseController
{
    protected $ipModel;
    protected $switchModel;
    protected $cekroolModel;

    public function __construct()
    {
        $this->ipModel = new IpManagementModel();
        $this->switchModel = new SwitchNetworkModel();
        $this->cekroolModel = new CekroolModel();
    }

    public function index()
    {
        // Permission Check
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'IP List Hub & Management';
        return view('ip_management/portal', $data);
    }

    public function userPc()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $vlan = $this->request->getGet('vlan');
        $sort = $this->request->getGet('sort') ?? 'ip_address';
        $dir  = $this->request->getGet('dir') ?? 'ASC';

        $allowedSort = ['ip_address', 'mac_address', 'device_type', 'user_assigned', 'department', 'status'];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'ip_address';
        }
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

        if ($vlan) {
            $this->ipModel->like('ip_address', '192.168.' . $vlan . '.', 'after');
        }

        $data['ips'] = $this->ipModel->orderBy($sort, $dir)->findAll();
        $data['active_vlan'] = $vlan;
        $data['sort'] = $sort;
        $data['dir']  = $dir;
        $data['title'] = 'IP List Management - User PC';
        $data['page_perm'] = $perm;

        return view('ip_management/index', $data);
    }

    public function switchNetwork()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'Switch Network Management';
        $data['page_perm'] = $perm;
        $data['switches'] = $this->switchModel->orderBy('name_switch', 'ASC')->findAll();

        return view('ip_management/switch_network', $data);
    }

    public function cekrool()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'Cekrool Management';
        $data['page_perm'] = $perm;
        $data['devices'] = $this->cekroolModel->orderBy('device_id', 'ASC')->findAll();
        return view('ip_management/cekrool', $data);
    }

    public function store()
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'ip_address'    => $this->request->getPost('ip_address'),
                'mac_address'   => $this->request->getPost('mac_address'),
                'device_type'   => $this->request->getPost('device_type'),
                'user_assigned' => $this->request->getPost('user_assigned'),
                'department'    => $this->request->getPost('department'),
                'status'        => $this->request->getPost('status') ?? 'Active',
                'description'   => $this->request->getPost('description'),
            ];

            if ($this->ipModel->insert($data)) {
                session()->setFlashdata('message', 'Data IP berhasil ditambahkan.');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan Data IP.');
            }
        }
        return redirect()->to(site_url('ip-management/user-pc'));
    }

    public function update($id)
    {
        if ($this->request->getMethod() === 'post') {
            $data = [
                'ip_address'    => $this->request->getPost('ip_address'),
                'mac_address'   => $this->request->getPost('mac_address'),
                'device_type'   => $this->request->getPost('device_type'),
                'user_assigned' => $this->request->getPost('user_assigned'),
                'department'    => $this->request->getPost('department'),
                'status'        => $this->request->getPost('status') ?? 'Active',
                'description'   => $this->request->getPost('description'),
            ];

            if ($this->ipModel->update($id, $data)) {
                session()->setFlashdata('message', 'Data IP berhasil diperbarui.');
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui Data IP.');
            }
        }
        return redirect()->to(site_url('ip-management/user-pc'));
    }

    public function delete($id)
    {
        if ($this->ipModel->delete($id)) {
            session()->setFlashdata('message', 'Data IP berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus Data IP.');
        }
        return redirect()->to(site_url('ip-management/user-pc'));
    }

    public function bulkDelete()
    {
        if ($this->request->getMethod() === 'post') {
            $ids = $this->request->getPost('ids');
            if (!empty($ids) && is_array($ids)) {
                $this->ipModel->whereIn('id', $ids)->delete();
                session()->setFlashdata('message', count($ids) . ' Data IP berhasil dihapus.');
            } else {
                session()->setFlashdata('error', 'Tidak ada data yang dipilih untuk dihapus.');
            }
        }
        return redirect()->to(site_url('ip-management/user-pc'));
    }

    public function importExcel()
    {
        if (strtoupper($this->request->getMethod()) === 'POST') {
            $file = $this->request->getFile('file_excel');
            
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $extension = strtolower($file->getClientExtension());
                if (in_array($extension, ['xls', 'xlsx', 'csv'])) {
                    try {
                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getTempName());
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($file->getTempName());
                        $sheetData = $spreadsheet->getSheet(0)->toArray();

                        $added = 0;
                        $updated = 0;
                        $failed = 0;
                        $errors = [];

                        // Skip header, start from index 1
                        for ($i = 1; $i < count($sheetData); $i++) {
                            $row = $sheetData[$i];
                            
                            // Skip completely empty rows
                            if (empty(array_filter($row))) {
                                continue;
                            }

                            $ip_address = isset($row[0]) ? trim((string)$row[0]) : '';
                            
                            if ($ip_address !== '') {
                                $data = [
                                    'ip_address'    => $ip_address,
                                    'mac_address'   => isset($row[1]) && trim((string)$row[1]) !== '' ? trim((string)$row[1]) : '',
                                    'device_type'   => isset($row[2]) && trim((string)$row[2]) !== '' ? trim((string)$row[2]) : 'Lainnya',
                                    'user_assigned' => isset($row[3]) && trim((string)$row[3]) !== '' ? trim((string)$row[3]) : '',
                                    'department'    => isset($row[4]) && trim((string)$row[4]) !== '' ? trim((string)$row[4]) : '',
                                    'status'        => isset($row[5]) && trim((string)$row[5]) !== '' ? trim((string)$row[5]) : 'Active',
                                    'description'   => isset($row[6]) && trim((string)$row[6]) !== '' ? trim((string)$row[6]) : '',
                                ];
                                
                                $lineNum = $i + 1;
                                
                                try {
                                    $exist = $this->ipModel->where('ip_address', $data['ip_address'])->first();
                                    if ($exist) {
                                        $this->ipModel->update($exist['id'], $data);
                                        $updated++;
                                    } else {
                                        $this->ipModel->insert($data);
                                        $added++;
                                    }
                                } catch (\Exception $e) {
                                    $failed++;
                                    $errors[] = "Baris $lineNum: Gagal menyimpan. Error: " . $e->getMessage();
                                }
                            }
                        }
                        
                        $msg = "Proses import selesai. ";
                        if ($added > 0 || $updated > 0) {
                            $msg .= "Berhasil: $added baris baru ditambahkan, $updated baris diperbarui.";
                        } else {
                            $msg .= "Tidak ada data yang berhasil diimport.";
                        }
                        if ($failed > 0) {
                            $msg .= " Gagal: $failed baris.";
                        }
                        
                        session()->setFlashdata('message', $msg);
                        
                        if (!empty($errors)) {
                            $errorHtml = "<strong>Beberapa baris gagal diimport:</strong><br><ul style='margin-bottom: 0; padding-left: 20px; text-align: left;'>";
                            foreach ($errors as $err) {
                                $errorHtml .= "<li>" . esc($err) . "</li>";
                            }
                            $errorHtml .= "</ul>";
                            session()->setFlashdata('error', $errorHtml);
                        }
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
        return redirect()->to(site_url('ip-management/user-pc'));
    }

    public function downloadTemplate()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/user-pc'))->with('error', 'Akses ditolak.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Header
        $sheet->setCellValue('A1', 'IP Address');
        $sheet->setCellValue('B1', 'MAC Address');
        $sheet->setCellValue('C1', 'Device Type');
        $sheet->setCellValue('D1', 'User Assigned');
        $sheet->setCellValue('E1', 'Department');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Description');
        
        // Set Column Width
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(30);
        
        // Bold header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        
        // Sample data
        $sheet->setCellValue('A2', '192.168.10.15');
        $sheet->setCellValue('B2', 'AA:BB:CC:DD:EE:FF');
        $sheet->setCellValue('C2', 'Laptop');
        $sheet->setCellValue('D2', 'John Doe');
        $sheet->setCellValue('E2', 'IT Support');
        $sheet->setCellValue('F2', 'Active');
        $sheet->setCellValue('G2', 'Laptop operasional');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Template_Import_IP_Management.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit();
    }

    public function switchNetworkStore()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/switch-network'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $data = [
                'lokasi'      => $this->request->getPost('lokasi'),
                'name_switch' => $this->request->getPost('name_switch'),
                'ip_address'  => $this->request->getPost('ip_address'),
                'type_switch' => $this->request->getPost('type_switch'),
                'notes'       => $this->request->getPost('notes'),
            ];

            if ($this->switchModel->insert($data)) {
                session()->setFlashdata('message', 'Data Switch berhasil ditambahkan.');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan Data Switch.');
            }
        }
        return redirect()->to(site_url('ip-management/switch-network'));
    }

    public function switchNetworkUpdate($id)
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/switch-network'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $data = [
                'lokasi'      => $this->request->getPost('lokasi'),
                'name_switch' => $this->request->getPost('name_switch'),
                'ip_address'  => $this->request->getPost('ip_address'),
                'type_switch' => $this->request->getPost('type_switch'),
                'notes'       => $this->request->getPost('notes'),
            ];

            if ($this->switchModel->update($id, $data)) {
                session()->setFlashdata('message', 'Data Switch berhasil diperbarui.');
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui Data Switch.');
            }
        }
        return redirect()->to(site_url('ip-management/switch-network'));
    }

    public function switchNetworkDelete($id)
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/switch-network'))->with('error', 'Akses ditolak.');
        }

        if ($this->switchModel->delete($id)) {
            session()->setFlashdata('message', 'Data Switch berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus Data Switch.');
        }
        return redirect()->to(site_url('ip-management/switch-network'));
    }

    public function switchNetworkBulkDelete()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/switch-network'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $ids = $this->request->getPost('ids');
            if (!empty($ids) && is_array($ids)) {
                $this->switchModel->whereIn('id', $ids)->delete();
                session()->setFlashdata('message', count($ids) . ' Data Switch berhasil dihapus.');
            } else {
                session()->setFlashdata('error', 'Tidak ada data yang dipilih untuk dihapus.');
            }
        }
        return redirect()->to(site_url('ip-management/switch-network'));
    }

    public function switchNetworkImportExcel()
    {
        log_message('debug', 'Switch Network Import: Method = ' . $this->request->getMethod());
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            log_message('debug', 'Switch Network Import: Access Denied');
            return redirect()->to(site_url('ip-management/switch-network'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $file = $this->request->getFile('file_excel');
            if (!$file) {
                log_message('debug', 'Switch Network Import: No file uploaded');
            } else {
                log_message('debug', 'Switch Network Import: File name = ' . $file->getName() . ', Valid = ' . ($file->isValid() ? 'yes' : 'no') . ', Error = ' . $file->getErrorString());
            }

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $extension = strtolower($file->getClientExtension());
                log_message('debug', 'Switch Network Import: File extension = ' . $extension);
                if (in_array($extension, ['xls', 'xlsx', 'csv'])) {
                    try {
                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getTempName());
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($file->getTempName());
                        $sheetData = $spreadsheet->getSheet(0)->toArray();

                        log_message('debug', 'Switch Network Import: Row count = ' . count($sheetData));

                        $added = 0;
                        $updated = 0;
                        $failed = 0;
                        $errors = [];

                        // Skip header, start from index 1
                        for ($i = 1; $i < count($sheetData); $i++) {
                            $row = $sheetData[$i];
                            
                            // Skip completely empty rows
                            if (empty(array_filter($row))) {
                                continue;
                            }

                            $lokasi = isset($row[0]) && trim((string)$row[0]) !== '' ? trim((string)$row[0]) : '-';
                            $name_switch = isset($row[1]) && trim((string)$row[1]) !== '' ? trim((string)$row[1]) : '-';
                            $ip_address = isset($row[2]) ? trim((string)$row[2]) : '';
                            $type_switch = isset($row[3]) && trim((string)$row[3]) !== '' ? trim((string)$row[3]) : '-';
                            $notes = isset($row[4]) && trim((string)$row[4]) !== '' ? trim((string)$row[4]) : '-';

                            $lineNum = $i + 1;
                            $ip_val = $ip_address !== '' ? $ip_address : '-';

                            $data = [
                                'lokasi'      => $lokasi,
                                'name_switch' => $name_switch,
                                'ip_address'  => $ip_val,
                                'type_switch' => $type_switch,
                                'notes'       => $notes,
                            ];

                            try {
                                $exist = null;
                                if ($ip_address !== '' && $ip_address !== '-') {
                                    $exist = $this->switchModel->where('ip_address', $ip_address)->first();
                                }
                                
                                if (!$exist && $name_switch !== '-' && $lokasi !== '-') {
                                    $exist = $this->switchModel->where([
                                        'name_switch' => $name_switch,
                                        'lokasi'      => $lokasi
                                    ])->first();
                                }

                                if ($exist) {
                                    $this->switchModel->update($exist['id'], $data);
                                    $updated++;
                                } else {
                                    $this->switchModel->insert($data);
                                    $added++;
                                }
                            } catch (\Exception $e) {
                                $failed++;
                                $errors[] = "Baris $lineNum: Gagal menyimpan ke database. Error: " . $e->getMessage();
                                log_message('error', "Switch Network Import: DB error on row $lineNum - " . $e->getMessage());
                            }
                        }

                        $msg = "Proses import selesai. ";
                        if ($added > 0 || $updated > 0) {
                            $msg .= "Berhasil: $added baris baru ditambahkan, $updated baris diperbarui.";
                        } else {
                            $msg .= "Tidak ada data yang berhasil diimport.";
                        }
                        if ($failed > 0) {
                            $msg .= " Gagal: $failed baris.";
                        }

                        session()->setFlashdata('message', $msg);
                        log_message('debug', 'Switch Network Import: Completed. Message = ' . $msg);
                        
                        if (!empty($errors)) {
                            $errorHtml = "<strong>Beberapa baris gagal diimport:</strong><br><ul style='margin-bottom: 0; padding-left: 20px; text-align: left;'>";
                            foreach ($errors as $err) {
                                $errorHtml .= "<li>" . esc($err) . "</li>";
                            }
                            $errorHtml .= "</ul>";
                            session()->setFlashdata('error', $errorHtml);
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Switch Network Import: Exception = ' . $e->getMessage());
                        session()->setFlashdata('error', 'Gagal memproses file: ' . $e->getMessage());
                    }
                } else {
                    log_message('debug', 'Switch Network Import: Invalid file extension');
                    session()->setFlashdata('error', 'Format file harus xls, xlsx, atau csv.');
                }
            } else {
                log_message('debug', 'Switch Network Import: Invalid file object');
                session()->setFlashdata('error', 'Gagal mengunggah file.');
            }
        }
        return redirect()->to(site_url('ip-management/switch-network'));
    }

    public function switchNetworkDownloadTemplate()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/switch-network'))->with('error', 'Akses ditolak.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Header
        $sheet->setCellValue('A1', 'Lokasi');
        $sheet->setCellValue('B1', 'Name Switch');
        $sheet->setCellValue('C1', 'IP Address');
        $sheet->setCellValue('D1', 'Type Switch');
        $sheet->setCellValue('E1', 'Notes');
        
        // Set Column Width
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(30);
        
        // Bold header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        
        // Sample data
        $sheet->setCellValue('A2', 'Lantai 2 Server Room');
        $sheet->setCellValue('B2', 'Switch-Core-01');
        $sheet->setCellValue('C2', '192.168.10.2');
        $sheet->setCellValue('D2', 'Cisco Catalyst 2960');
        $sheet->setCellValue('E2', 'Switch core utama lantai 2');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Template_Import_Switch_Network.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        $writer->save('php://output');
        exit();
    }

    public function switchNetworkExportExcel()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $switches = $this->switchModel->orderBy('name_switch', 'ASC')->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Lokasi');
        $sheet->setCellValue('C1', 'Name Switch');
        $sheet->setCellValue('D1', 'IP Address');
        $sheet->setCellValue('E1', 'Type Switch');
        $sheet->setCellValue('F1', 'Notes');

        // Styles
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);

        $rowNum = 2;
        $no = 1;
        foreach ($switches as $row) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $row['lokasi']);
            $sheet->setCellValue('C' . $rowNum, $row['name_switch']);
            $sheet->setCellValue('D' . $rowNum, $row['ip_address']);
            $sheet->setCellValue('E' . $rowNum, $row['type_switch']);
            $sheet->setCellValue('F' . $rowNum, $row['notes'] ?? '-');
            $rowNum++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Data_Switch_Network_Export_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');
        exit();
    }

    public function cekroolStore()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/cekrool'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $data = [
                'building'    => $this->request->getPost('building'),
                'location'    => $this->request->getPost('location'),
                'device_id'   => $this->request->getPost('device_id'),
                'ip_address'  => $this->request->getPost('ip_address'),
                'type_device' => $this->request->getPost('type_device'),
                'notes'       => $this->request->getPost('notes'),
            ];

            if ($this->cekroolModel->insert($data)) {
                session()->setFlashdata('message', 'Data Device Cekrool berhasil ditambahkan.');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan Data Device Cekrool.');
            }
        }
        return redirect()->to(site_url('ip-management/cekrool'));
    }

    public function cekroolUpdate($id)
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/cekrool'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $data = [
                'building'    => $this->request->getPost('building'),
                'location'    => $this->request->getPost('location'),
                'device_id'   => $this->request->getPost('device_id'),
                'ip_address'  => $this->request->getPost('ip_address'),
                'type_device' => $this->request->getPost('type_device'),
                'notes'       => $this->request->getPost('notes'),
            ];

            if ($this->cekroolModel->update($id, $data)) {
                session()->setFlashdata('message', 'Data Device Cekrool berhasil diperbarui.');
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui Data Device Cekrool.');
            }
        }
        return redirect()->to(site_url('ip-management/cekrool'));
    }

    public function cekroolDelete($id)
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/cekrool'))->with('error', 'Akses ditolak.');
        }

        if ($this->cekroolModel->delete($id)) {
            session()->setFlashdata('message', 'Data Device Cekrool berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus Data Device Cekrool.');
        }
        return redirect()->to(site_url('ip-management/cekrool'));
    }

    public function cekroolBulkDelete()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/cekrool'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $ids = $this->request->getPost('ids');
            if (!empty($ids) && is_array($ids)) {
                $this->cekroolModel->whereIn('id', $ids)->delete();
                session()->setFlashdata('message', count($ids) . ' Data Device Cekrool berhasil dihapus.');
            } else {
                session()->setFlashdata('error', 'Tidak ada data yang dipilih untuk dihapus.');
            }
        }
        return redirect()->to(site_url('ip-management/cekrool'));
    }

    public function cekroolImportExcel()
    {
        log_message('debug', 'Cekrool Import: Method = ' . $this->request->getMethod());
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            log_message('debug', 'Cekrool Import: Access Denied');
            return redirect()->to(site_url('ip-management/cekrool'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $file = $this->request->getFile('file_excel');
            if (!$file) {
                log_message('debug', 'Cekrool Import: No file uploaded');
            } else {
                log_message('debug', 'Cekrool Import: File name = ' . $file->getName() . ', Valid = ' . ($file->isValid() ? 'yes' : 'no') . ', Error = ' . $file->getErrorString());
            }

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $extension = strtolower($file->getClientExtension());
                log_message('debug', 'Cekrool Import: File extension = ' . $extension);
                if (in_array($extension, ['xls', 'xlsx', 'csv'])) {
                    try {
                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getTempName());
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($file->getTempName());
                        $sheetData = $spreadsheet->getSheet(0)->toArray();

                        log_message('debug', 'Cekrool Import: Row count = ' . count($sheetData));

                        $added = 0;
                        $updated = 0;
                        $failed = 0;
                        $errors = [];

                        // Skip header, start from index 1
                        for ($i = 1; $i < count($sheetData); $i++) {
                            $row = $sheetData[$i];
                            
                            // Skip completely empty rows
                            if (empty(array_filter($row))) {
                                continue;
                            }

                            $building = isset($row[0]) && trim((string)$row[0]) !== '' ? trim((string)$row[0]) : '-';
                            $location = isset($row[1]) && trim((string)$row[1]) !== '' ? trim((string)$row[1]) : '-';
                            $device_id = isset($row[2]) && trim((string)$row[2]) !== '' ? trim((string)$row[2]) : '-';
                            $ip_address = isset($row[3]) && trim((string)$row[3]) !== '' ? trim((string)$row[3]) : '-';
                            $type_device = isset($row[4]) && trim((string)$row[4]) !== '' ? trim((string)$row[4]) : '-';
                            $notes = isset($row[5]) && trim((string)$row[5]) !== '' ? trim((string)$row[5]) : '-';

                            $lineNum = $i + 1;

                            $data = [
                                'building'    => $building,
                                'location'    => $location,
                                'device_id'   => $device_id,
                                'ip_address'  => $ip_address,
                                'type_device' => $type_device,
                                'notes'       => $notes,
                            ];

                            try {
                                $exist = null;
                                if ($device_id !== '' && $device_id !== '-' && $location !== '' && $location !== '-') {
                                    $exist = $this->cekroolModel->where([
                                        'device_id' => $device_id,
                                        'location'  => $location
                                    ])->first();
                                }

                                if ($exist) {
                                    $this->cekroolModel->update($exist['id'], $data);
                                    $updated++;
                                } else {
                                    $this->cekroolModel->insert($data);
                                    $added++;
                                }
                            } catch (\Exception $e) {
                                $failed++;
                                $errors[] = "Baris $lineNum: Gagal menyimpan ke database. Error: " . $e->getMessage();
                                log_message('error', "Cekrool Import: DB error on row $lineNum - " . $e->getMessage());
                            }
                        }

                        $msg = "Proses import selesai. ";
                        if ($added > 0 || $updated > 0) {
                            $msg .= "Berhasil: $added baris baru ditambahkan, $updated baris diperbarui.";
                        } else {
                            $msg .= "Tidak ada data yang berhasil diimport.";
                        }
                        if ($failed > 0) {
                            $msg .= " Gagal: $failed baris.";
                        }

                        session()->setFlashdata('message', $msg);
                        log_message('debug', 'Cekrool Import: Completed. Message = ' . $msg);
                        
                        if (!empty($errors)) {
                            $errorHtml = "<strong>Beberapa baris gagal diimport:</strong><br><ul style='margin-bottom: 0; padding-left: 20px; text-align: left;'>";
                            foreach ($errors as $err) {
                                $errorHtml .= "<li>" . esc($err) . "</li>";
                            }
                            $errorHtml .= "</ul>";
                            session()->setFlashdata('error', $errorHtml);
                        }
                    } catch (\Exception $e) {
                        log_message('error', 'Cekrool Import: Exception = ' . $e->getMessage());
                        session()->setFlashdata('error', 'Gagal memproses file: ' . $e->getMessage());
                    }
                } else {
                    log_message('debug', 'Cekrool Import: Invalid file extension');
                    session()->setFlashdata('error', 'Format file harus xls, xlsx, atau csv.');
                }
            } else {
                log_message('debug', 'Cekrool Import: Invalid file object');
                session()->setFlashdata('error', 'Gagal mengunggah file.');
            }
        }
        return redirect()->to(site_url('ip-management/cekrool'));
    }

    public function cekroolExportExcel()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $devices = $this->cekroolModel->orderBy('device_id', 'ASC')->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Building');
        $sheet->setCellValue('C1', 'Location');
        $sheet->setCellValue('D1', 'Device ID');
        $sheet->setCellValue('E1', 'IP Address');
        $sheet->setCellValue('F1', 'Type Device');
        $sheet->setCellValue('G1', 'Notes');

        // Styles
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);

        $rowNum = 2;
        $no = 1;
        foreach ($devices as $row) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $row['building'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $row['location']);
            $sheet->setCellValue('D' . $rowNum, $row['device_id']);
            $sheet->setCellValue('E' . $rowNum, $row['ip_address']);
            $sheet->setCellValue('F' . $rowNum, $row['type_device']);
            $sheet->setCellValue('G' . $rowNum, $row['notes'] ?? '-');
            $rowNum++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Data_Cekrool_Export_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');
        exit();
    }

    public function cekroolDownloadTemplate()
    {
        $perm = get_permission('ip_management.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['ip_management.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('ip-management/cekrool'))->with('error', 'Akses ditolak.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set Header
        $sheet->setCellValue('A1', 'Building');
        $sheet->setCellValue('B1', 'Location');
        $sheet->setCellValue('C1', 'Device ID');
        $sheet->setCellValue('D1', 'IP Address');
        $sheet->setCellValue('E1', 'Type Device');
        $sheet->setCellValue('F1', 'Notes');
        
        // Set Column Width
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(30);
        
        // Bold header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        
        // Sample data
        $sheet->setCellValue('A2', 'F1');
        $sheet->setCellValue('B2', 'Lantai 1 Pos Security');
        $sheet->setCellValue('C2', 'DEV-CKR-01');
        $sheet->setCellValue('D2', '192.168.10.100');
        $sheet->setCellValue('E2', 'Fingerprint Reader');
        $sheet->setCellValue('F2', 'Device cek absensi');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Template_Import_Cekrool.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        $writer->save('php://output');
        exit();
    }
}
