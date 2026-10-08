<?php

namespace App\Controllers;

use App\Models\HardiskLogModel;
use App\Models\NvrModel;

class HardiskLogController extends BaseController
{
    protected $model;
    protected $nvrModel;

    public function __construct()
    {
        $this->model = new HardiskLogModel();
        $this->nvrModel = new NvrModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'read-write' && !has_any_access(['hardisk_log.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $perm = get_permission('hardisk_log.php');
        
        $data['show_time'] = $this->request->getGet('show_time') === '1';
        $data['show_hdd'] = $this->request->getGet('show_hdd') === '1';

        $data['title'] = 'Hardisk Log Replacement';
        $data['page_perm'] = $perm;
        $data['nvrs'] = $this->nvrModel->orderBy('nama_nvr', 'ASC')->findAll();
        
        $logs = $this->model->orderBy('device_name', 'ASC')->findAll();
        
        $maxSlots = 0;
        foreach ($logs as &$row) {
            $row['slots'] = json_decode($row['slots_json'] ?? '[]', true) ?: [];
            
            // Cari index tertinggi yang memiliki data
            $highestUsedIdx = -1;
            foreach ($row['slots'] as $idx => $slotVal) {
                if (!empty($slotVal) && $slotVal !== '-') {
                    $highestUsedIdx = $idx;
                }
            }
            
            $maxSlots = max($maxSlots, $highestUsedIdx + 1);
        }
        
        // Pastikan minimal ada 1 slot yang ditampilkan (atau bisa dibiarkan minimal 1)
        if ($maxSlots < 1) $maxSlots = 1;
        
        $data['logs'] = $logs;
        $data['maxSlots'] = $maxSlots;

        return view('cctv/hardisk_log', $data);
    }

    public function store()
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $slots = $this->request->getPost('slots') ?: [];
            
            // Clean trailing empty slots
            while (count($slots) > 0 && (empty(end($slots)) || trim(end($slots)) === '-')) {
                array_pop($slots);
            }
            
            $slots_json = json_encode($slots);

            $data = [
                'device_name'       => $this->request->getPost('device_name'),
                'slots_json'        => $slots_json,
                'start_record'      => $this->formatInputDate($this->request->getPost('start_record')),
                'start_time_record' => $this->request->getPost('start_time_record') ?: null,
                'last_record'       => $this->formatInputDate($this->request->getPost('last_record')),
                'last_time_record'  => $this->request->getPost('last_time_record') ?: null,
                'remark'            => $this->request->getPost('remark') ?: null,
            ];

            $exist = $this->model->where('device_name', $data['device_name'])->first();
            $mergedSlots = $slots;
            
            if ($exist) {
                $existingSlots = json_decode($exist['slots_json'], true) ?: [];
                
                $maxIndex = max(
                    empty($existingSlots) ? -1 : max(array_keys($existingSlots)),
                    empty($slots) ? -1 : max(array_keys($slots))
                );
                
                $merged = [];
                for ($i = 0; $i <= $maxIndex; $i++) {
                    $newVal = isset($slots[$i]) && $slots[$i] !== '-' ? $slots[$i] : null;
                    $oldVal = isset($existingSlots[$i]) ? $existingSlots[$i] : null;
                    
                    if (!empty($newVal)) {
                        $merged[$i] = $newVal;
                    } else if (!empty($oldVal)) {
                        $merged[$i] = $oldVal;
                    } else {
                        $merged[$i] = '-';
                    }
                }
                $mergedSlots = $merged;
                
                if (empty($data['remark'])) {
                    $data['remark'] = $exist['remark'];
                }
            }

            // Clean trailing empty slots from merged array
            while (count($mergedSlots) > 0 && (empty(end($mergedSlots)) || trim(end($mergedSlots)) === '-')) {
                array_pop($mergedSlots);
            }
            
            $data['slots_json'] = json_encode($mergedSlots);

            // Auto-calculate start_record & last_record if empty, using mergedSlots
            if (empty($data['start_record']) && empty($data['start_time_record'])) {
                foreach ($mergedSlots as $slotVal) {
                    if (!empty($slotVal) && $slotVal !== '-') {
                        $records = explode('||', $slotVal);
                        $firstRec = $records[0];
                        
                        $mainStr = $firstRec;
                        if (strpos($mainStr, '|HDD:') !== false) $mainStr = explode('|HDD:', $mainStr)[0];
                        if (strpos($mainStr, '|OW:') !== false) $mainStr = explode('|OW:', $mainStr)[0];
                        
                        $parts = explode('-', $mainStr);
                        if (!empty($parts[0])) {
                            $sp = explode(' ', trim($parts[0]));
                            $data['start_record'] = $sp[0] ?? null;
                            $data['start_time_record'] = $sp[1] ?? null;
                            break;
                        }
                    }
                }
            }

            if (empty($data['last_record']) && empty($data['last_time_record'])) {
                for ($i = count($mergedSlots) - 1; $i >= 0; $i--) {
                    $slotVal = $mergedSlots[$i];
                    if (!empty($slotVal) && $slotVal !== '-') {
                        $records = explode('||', $slotVal);
                        $lastRec = end($records);
                        
                        $mainStr = $lastRec;
                        if (strpos($mainStr, '|HDD:') !== false) $mainStr = explode('|HDD:', $mainStr)[0];
                        if (strpos($mainStr, '|OW:') !== false) $mainStr = explode('|OW:', $mainStr)[0];
                        
                        $parts = explode('-', $mainStr);
                        $targetPart = !empty($parts[1]) ? trim($parts[1]) : (!empty($parts[0]) ? trim($parts[0]) : '');
                        if ($targetPart) {
                            $sp = explode(' ', $targetPart);
                            $data['last_record'] = $sp[0] ?? null;
                            $data['last_time_record'] = $sp[1] ?? null;
                            break;
                        }
                    }
                }
            }

            if ($exist) {
                if ($this->model->update($exist['id'], $data)) {
                    session()->setFlashdata('message', 'Data Hardisk Log berhasil ditambahkan/digabungkan ke device yang sudah ada.');
                } else {
                    session()->setFlashdata('error', 'Gagal memperbarui Data Hardisk Log.');
                }
            } else {
                if ($this->model->insert($data)) {
                    session()->setFlashdata('message', 'Data Hardisk Log berhasil ditambahkan.');
                } else {
                    session()->setFlashdata('error', 'Gagal menambahkan Data Hardisk Log.');
                }
            }
        }
        return redirect()->to(site_url('cctv/hardisk-log'));
    }

    public function update($id)
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $slots = $this->request->getPost('slots') ?: [];
            
            // Clean trailing empty slots
            while (count($slots) > 0 && (empty(end($slots)) || trim(end($slots)) === '-')) {
                array_pop($slots);
            }
            
            $slots_json = json_encode($slots);

            $data = [
                'device_name'       => $this->request->getPost('device_name'),
                'slots_json'        => $slots_json,
                'start_record'      => $this->formatInputDate($this->request->getPost('start_record')),
                'start_time_record' => $this->request->getPost('start_time_record') ?: null,
                'last_record'       => $this->formatInputDate($this->request->getPost('last_record')),
                'last_time_record'  => $this->request->getPost('last_time_record') ?: null,
                'remark'            => $this->request->getPost('remark') ?: null,
            ];

            // Auto-calculate start_record & last_record if empty, using slots
            if (empty($data['start_record']) && empty($data['start_time_record'])) {
                foreach ($slots as $slotVal) {
                    if (!empty($slotVal) && $slotVal !== '-') {
                        $records = explode('||', $slotVal);
                        $firstRec = $records[0];
                        
                        $mainStr = $firstRec;
                        if (strpos($mainStr, '|HDD:') !== false) $mainStr = explode('|HDD:', $mainStr)[0];
                        if (strpos($mainStr, '|OW:') !== false) $mainStr = explode('|OW:', $mainStr)[0];
                        
                        $parts = explode('-', $mainStr);
                        if (!empty($parts[0])) {
                            $sp = explode(' ', trim($parts[0]));
                            $data['start_record'] = $sp[0] ?? null;
                            $data['start_time_record'] = $sp[1] ?? null;
                            break;
                        }
                    }
                }
            }

            if (empty($data['last_record']) && empty($data['last_time_record'])) {
                for ($i = count($slots) - 1; $i >= 0; $i--) {
                    $slotVal = $slots[$i];
                    if (!empty($slotVal) && $slotVal !== '-') {
                        $records = explode('||', $slotVal);
                        $lastRec = end($records);
                        
                        $mainStr = $lastRec;
                        if (strpos($mainStr, '|HDD:') !== false) $mainStr = explode('|HDD:', $mainStr)[0];
                        if (strpos($mainStr, '|OW:') !== false) $mainStr = explode('|OW:', $mainStr)[0];
                        
                        $parts = explode('-', $mainStr);
                        $targetPart = !empty($parts[1]) ? trim($parts[1]) : (!empty($parts[0]) ? trim($parts[0]) : '');
                        if ($targetPart) {
                            $sp = explode(' ', $targetPart);
                            $data['last_record'] = $sp[0] ?? null;
                            $data['last_time_record'] = $sp[1] ?? null;
                            break;
                        }
                    }
                }
            }

            if ($this->model->update($id, $data)) {
                session()->setFlashdata('message', 'Data Hardisk Log berhasil diperbarui.');
            } else {
                session()->setFlashdata('error', 'Gagal memperbarui Data Hardisk Log.');
            }
        }
        return redirect()->to(site_url('cctv/hardisk-log'));
    }

    public function delete($id)
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Akses ditolak.');
        }

        if ($this->model->delete($id)) {
            session()->setFlashdata('message', 'Data Hardisk Log berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus Data Hardisk Log.');
        }
        return redirect()->to(site_url('cctv/hardisk-log'));
    }

    public function deleteRecord($id, $slotIdx, $recordIdx)
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Akses ditolak.');
        }

        $log = $this->model->find($id);
        if (!$log) return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Data tidak ditemukan.');

        $slots = json_decode($log['slots_json'], true) ?: [];
        
        if (isset($slots[$slotIdx])) {
            $records = explode('||', $slots[$slotIdx]);
            if (isset($records[$recordIdx])) {
                unset($records[$recordIdx]);
                // Re-index array
                $records = array_values($records);
                
                if (empty($records)) {
                    $slots[$slotIdx] = '-';
                } else {
                    $slots[$slotIdx] = implode('||', $records);
                }
                
                // Clean trailing empty slots
                while (count($slots) > 0 && (empty(end($slots)) || trim(end($slots)) === '-')) {
                    array_pop($slots);
                }

                $this->model->update($id, [
                    'slots_json' => json_encode($slots)
                ]);
                
                session()->setFlashdata('message', 'Record pada kotak tersebut berhasil dihapus.');
            }
        }
        
        return redirect()->to(site_url('cctv/hardisk-log'));
    }

    public function bulkDelete()
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Akses ditolak.');
        }

        if (strtoupper($this->request->getMethod()) === 'POST') {
            $ids = $this->request->getPost('ids');
            if (!empty($ids) && is_array($ids)) {
                $this->model->whereIn('id', $ids)->delete();
                session()->setFlashdata('message', count($ids) . ' Data Hardisk Log berhasil dihapus.');
            } else {
                session()->setFlashdata('error', 'Tidak ada data yang dipilih untuk dihapus.');
            }
        }
        return redirect()->to(site_url('cctv/hardisk-log'));
    }

    public function importExcel()
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Akses ditolak.');
        }

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

                        // Find the header row by looking for 'DEVICE'
                        $headerRowIdx = 0;
                        $headerMap = [];
                        foreach ($sheetData as $i => $row) {
                            $rowUpper = array_map(function($v) { return trim(strtoupper((string)$v)); }, $row);
                            if (in_array('DEVICE', $rowUpper)) {
                                $headerRowIdx = $i;
                                foreach ($rowUpper as $colIdx => $colName) {
                                    if ($colName !== '') {
                                        $headerMap[$colName] = $colIdx;
                                    }
                                }
                                break;
                            }
                        }

                        // Process data rows
                        for ($i = $headerRowIdx + 1; $i < count($sheetData); $i++) {
                            $row = $sheetData[$i];
                            
                            // Skip completely empty rows
                            if (empty(array_filter($row))) {
                                continue;
                            }

                            // Device is mandatory
                            $deviceIdx = $headerMap['DEVICE'] ?? 0;
                            $device_name = isset($row[$deviceIdx]) ? trim((string)$row[$deviceIdx]) : '';
                            if (empty($device_name) || $device_name === '-' || $device_name === 'DEVICE') {
                                continue;
                            }

                            $remarkIdx = $headerMap['REMARK'] ?? null;
                            $remark = $remarkIdx !== null ? (isset($row[$remarkIdx]) ? trim((string)$row[$remarkIdx]) : null) : null;

                            $lastTimeRecIdx = $headerMap['LAST TIME RECORD'] ?? null;
                            $last_time_record = $lastTimeRecIdx !== null ? (isset($row[$lastTimeRecIdx]) ? trim((string)$row[$lastTimeRecIdx]) : null) : null;

                            $startTimeRecIdx = $headerMap['START TIME RECORD'] ?? null;
                            $start_time_record = $startTimeRecIdx !== null ? (isset($row[$startTimeRecIdx]) ? trim((string)$row[$startTimeRecIdx]) : null) : null;

                            $lastRecIdx = $headerMap['LAST RECORD'] ?? null;
                            $last_record = $lastRecIdx !== null ? (isset($row[$lastRecIdx]) ? trim((string)$row[$lastRecIdx]) : null) : null;

                            $startRecIdx = $headerMap['START RECORD'] ?? null;
                            $start_record = $startRecIdx !== null ? (isset($row[$startRecIdx]) ? trim((string)$row[$startRecIdx]) : null) : null;

                            // Slots: Find all headers that start with 'SLOT' or fall back to positional indices
                            $slots = [];
                            $slotIndices = [];
                            foreach ($headerMap as $hName => $hIdx) {
                                if (strpos($hName, 'SLOT') === 0 || strpos($hName, 'I') === 0 || strpos($hName, 'V') === 0 || strpos($hName, 'X') === 0) {
                                    $slotIndices[$hIdx] = $hName;
                                }
                            }
                            
                            if (!empty($slotIndices)) {
                                ksort($slotIndices);
                                foreach ($slotIndices as $hIdx => $hName) {
                                    $slots[] = isset($row[$hIdx]) && trim((string)$row[$hIdx]) !== '' ? trim((string)$row[$hIdx]) : null;
                                }
                            } else {
                                // Fallback positional: start after Device, end before Start Record
                                $startSlotIdx = $deviceIdx + 1;
                                $endSlotIdx = $startRecIdx !== null ? $startRecIdx - 1 : count($row) - 6;
                                for ($s = $startSlotIdx; $s <= $endSlotIdx; $s++) {
                                    $slots[] = isset($row[$s]) && trim((string)$row[$s]) !== '' ? trim((string)$row[$s]) : null;
                                }
                            }

                            // Trim trailing nulls
                            while (count($slots) > 0 && empty(end($slots))) {
                                array_pop($slots);
                            }

                            // Convert imported "(Overwrite X)" and "[HDD #X]" back to internal format
                            foreach ($slots as &$sVal) {
                                if ($sVal) {
                                    $rawRecords = explode("\n---\n", $sVal);
                                    $parsedRecords = [];
                                    
                                    foreach ($rawRecords as $recStr) {
                                        $recStr = trim($recStr);
                                        if (!$recStr) continue;
                                        
                                        $hddNum = '';
                                        if (preg_match('/(.*?)\s*\n?\s*\[\s*HDD\s+#(\d+)\s*\]/is', $recStr, $m)) {
                                            $recStr = trim($m[1]);
                                            $hddNum = '|HDD:' . trim($m[2]);
                                        }
                                        
                                        if (preg_match('/(.*?)\s*\n?\s*\(\s*Overwrite\s+(.*?)\s*\)/is', $recStr, $m)) {
                                            $recStr = trim($m[1]) . '|OW:' . rtrim(trim($m[2]), ')');
                                        }
                                        
                                        $recStr .= $hddNum;
                                        $parsedRecords[] = $recStr;
                                    }
                                    $sVal = implode('||', $parsedRecords);
                                }
                            }
                            unset($sVal);

                            $slots_json = json_encode($slots);

                            $data = [
                                'device_name'       => $device_name,
                                'slots_json'        => $slots_json,
                                'start_record'      => $this->formatInputDate($start_record),
                                'start_time_record' => $start_time_record,
                                'last_record'       => $this->formatInputDate($last_record),
                                'last_time_record'  => $last_time_record,
                                'remark'            => $remark,
                            ];

                            // Auto-calculate start & last records if empty
                            if (empty($data['start_record']) && empty($data['start_time_record'])) {
                                foreach ($slots as $slotVal) {
                                    if (!empty($slotVal) && $slotVal !== '-') {
                                        $parts = explode('-', $slotVal);
                                        if (!empty($parts[0])) {
                                            $sp = explode(' ', trim($parts[0]));
                                            $data['start_record'] = $sp[0] ?? null;
                                            $data['start_time_record'] = $sp[1] ?? null;
                                            break;
                                        }
                                    }
                                }
                            }

                            if (empty($data['last_record']) && empty($data['last_time_record'])) {
                                for ($s = count($slots) - 1; $s >= 0; $s--) {
                                    $slotVal = $slots[$s];
                                    if (!empty($slotVal) && $slotVal !== '-') {
                                        $parts = explode('-', $slotVal);
                                        $targetPart = !empty($parts[1]) ? trim($parts[1]) : (!empty($parts[0]) ? trim($parts[0]) : '');
                                        if ($targetPart) {
                                            $sp = explode(' ', $targetPart);
                                            $data['last_record'] = $sp[0] ?? null;
                                            $data['last_time_record'] = $sp[1] ?? null;
                                            break;
                                        }
                                    }
                                }
                            }

                            try {
                                $exist = $this->model->where('device_name', $device_name)->first();
                                if ($exist) {
                                    $this->model->update($exist['id'], $data);
                                    $updated++;
                                } else {
                                    $this->model->insert($data);
                                    $added++;
                                }
                            } catch (\Exception $e) {
                                $failed++;
                                $errors[] = "Baris " . ($i + 1) . ": Gagal menyimpan ke database. Error: " . $e->getMessage();
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
                        session()->setFlashdata('error', 'Gagal memproses file: ' . $e->getMessage());
                    }
                } else {
                    session()->setFlashdata('error', 'Format file harus xls, xlsx, atau csv.');
                }
            } else {
                session()->setFlashdata('error', 'Gagal mengunggah file.');
            }
        }
        return redirect()->to(site_url('cctv/hardisk-log'));
    }

    public function exportExcel()
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['hardisk_log.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }

        $show_time = $this->request->getGet('show_time') === '1';
        $show_hdd = $this->request->getGet('show_hdd') === '1';
        $logs = $this->model->orderBy('device_name', 'ASC')->findAll();

        $maxSlots = 0;
        foreach ($logs as &$row) {
            $row['slots'] = json_decode($row['slots_json'] ?? '[]', true) ?: [];
            
            // Cari index tertinggi yang memiliki data
            $highestUsedIdx = -1;
            foreach ($row['slots'] as $idx => $slotVal) {
                if (!empty($slotVal) && $slotVal !== '-') {
                    $highestUsedIdx = $idx;
                }
            }
            
            $maxSlots = max($maxSlots, $highestUsedIdx + 1);
        }
        
        if ($maxSlots < 1) $maxSlots = 1;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title block merged
        $lastSlotColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxSlots + 2);
        $sheet->mergeCells("C1:{$lastSlotColLetter}1");
        $sheet->setCellValue('C1', 'START ~ END HDD RECORD');
        $sheet->getStyle('C1')->getFont()->setBold(true);
        $sheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header definitions row 2
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Device');
        
        for ($col = 0; $col < $maxSlots; $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 3);
            $sheet->setCellValue($colLetter . '2', $this->getRomanNumeral($col + 1));
        }

        $startRecColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxSlots + 3);
        $lastRecColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxSlots + 4);
        $startTimeRecColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxSlots + 5);
        $lastTimeRecColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxSlots + 6);
        $remarkColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxSlots + 7);

        $sheet->setCellValue($startRecColLetter . '2', 'Start Record');
        $sheet->setCellValue($lastRecColLetter . '2', 'Last Record');
        $sheet->setCellValue($startTimeRecColLetter . '2', 'Start Time Record');
        $sheet->setCellValue($lastTimeRecColLetter . '2', 'Last Time Record');
        $sheet->setCellValue($remarkColLetter . '2', 'Remark');

        // Header Styling
        $excelColor = strtoupper(str_replace('#', 'FF', $this->excel_header_color ?? '#4E73DF'));
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $excelColor]],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ];
        $lastHeaderLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxSlots + 7);
        $sheet->getStyle("A1:{$lastHeaderLetter}2")->applyFromArray($headerStyle);
        
        // Auto sizing or explicit width
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        for ($c = 3; $c <= $maxSlots + 2; $c++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(22);
        }
        $sheet->getColumnDimension($startRecColLetter)->setWidth(18);
        $sheet->getColumnDimension($startTimeRecColLetter)->setWidth(18);
        $sheet->getColumnDimension($lastRecColLetter)->setWidth(18);
        $sheet->getColumnDimension($lastTimeRecColLetter)->setWidth(18);
        $sheet->getColumnDimension($remarkColLetter)->setWidth(25);

        $rowNum = 3;
        $no = 1;
        foreach ($logs as $row) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $row['device_name']);
            
            for ($col = 0; $col < $maxSlots; $col++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 3);
                $val = $row['slots'][$col] ?? '';
                if ($val !== '' && $val !== '-') {
                    $records = explode('||', $val);
                    $formattedRecords = [];
                    foreach ($records as $recStr) {
                        $hddStr = '';
                        if (strpos($recStr, '|HDD:') !== false) {
                            $parts = explode('|HDD:', $recStr);
                            $recStr = $parts[0];
                            if ($show_hdd) {
                                $hddStr = "\n[HDD #" . ($parts[1] ?? '') . "]";
                            }
                        }

                        if (strpos($recStr, '|OW:') !== false) {
                            $parts = explode('|OW:', $recStr);
                            $recStr = $parts[0];
                            if ($show_hdd) {
                                $recStr .= "\n(Overwrite " . ($parts[1] ?? '') . ")";
                            }
                        }
                        
                        if (!$show_time) {
                            $recStr = preg_replace('/\s+\d{1,2}:\d{2}(:\d{2})?/', '', $recStr);
                        }
                        $formattedRecords[] = $recStr . $hddStr;
                    }
                    $finalVal = implode("\n---\n", $formattedRecords);
                    $sheet->getStyle($colLetter . $rowNum)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue($colLetter . $rowNum, $finalVal);
                } else {
                    $sheet->setCellValue($colLetter . $rowNum, $val);
                }
            }
            
            $sheet->setCellValue($startRecColLetter . $rowNum, $row['start_record']);
            $sheet->setCellValue($lastRecColLetter . $rowNum, $row['last_record']);
            $sheet->setCellValue($startTimeRecColLetter . $rowNum, $row['start_time_record']);
            $sheet->setCellValue($lastTimeRecColLetter . $rowNum, $row['last_time_record']);
            $sheet->setCellValue($remarkColLetter . $rowNum, $row['remark'] ?? '');
            $rowNum++;
        }

        if ($rowNum > 3) {
            $dataStyle = [
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A3:' . $lastHeaderLetter . ($rowNum - 1))->applyFromArray($dataStyle);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Data_Hardisk_Log_Replacement_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer->save('php://output');
        exit();
    }

    public function downloadTemplate()
    {
        $perm = get_permission('hardisk_log.php');
        if (session()->get('role') !== 'read-write' && !has_any_access(['hardisk_log.php'])) {
            return redirect()->to(site_url('dashboard'))->with('error', 'Akses ditolak.');
        }
        if (session()->get('role') !== 'read-write' && $perm === 'R') {
            return redirect()->to(site_url('cctv/hardisk-log'))->with('error', 'Akses ditolak.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title block merged
        $sheet->mergeCells("C1:J1");
        $sheet->setCellValue('C1', 'START ~ END HDD RECORD');
        $sheet->getStyle('C1')->getFont()->setBold(true);
        $sheet->getStyle('C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Header definitions row 2
        $sheet->setCellValue('A2', 'No');
        $sheet->setCellValue('B2', 'Device');
        $maxSlots = 30;
        for ($i = 0; $i < $maxSlots; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 3);
            $sheet->setCellValue($colLetter . '2', $this->getRomanNumeral($i + 1));
        }
        
        $startRecCol = $maxSlots + 3;
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startRecCol) . '2', 'Start Record');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startRecCol + 1) . '2', 'Last Record');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startRecCol + 2) . '2', 'Start Time Record');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startRecCol + 3) . '2', 'Last Time Record');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startRecCol + 4) . '2', 'Remark');
        
        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4E73DF']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
        ];
        $sheet->getStyle("A1:O2")->applyFromArray($headerStyle);

        // Auto sizing or explicit width
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        for ($c = 3; $c <= 10; $c++) { // 10 is J
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(22);
        }
        $sheet->getColumnDimension('K')->setWidth(18);
        $sheet->getColumnDimension('L')->setWidth(18);
        $sheet->getColumnDimension('M')->setWidth(18);
        $sheet->getColumnDimension('N')->setWidth(18);
        $sheet->getColumnDimension('O')->setWidth(25);
        
        // Sample data
        $sheet->setCellValue('A3', '1');
        $sheet->setCellValue('B3', 'NVR - A');
        $sheet->setCellValue('C3', "16/12/2026 08:00:00-09/01/2026 12:00:00\n(Overwrite 3 Hari)\n[HDD #12]");
        $sheet->getStyle('C3')->getAlignment()->setWrapText(true);
        $sheet->setCellValue('D3', '09/01/2026 12:00:00-09/03/2026 14:00:00');
        $sheet->setCellValue('E3', '09/03/2026 14:00:00-06/05/2026 16:00:00');
        $sheet->setCellValue('K3', '16/12/2026');
        $sheet->setCellValue('L3', '06/05/2026');
        $sheet->setCellValue('M3', '08:00:00');
        $sheet->setCellValue('N3', '16:00:00');
        $sheet->setCellValue('O3', 'Ganti HDD 8TB');
        
        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A3:O3')->applyFromArray($dataStyle);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'Template_Import_Hardisk_Log.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        $writer->save('php://output');
        exit();
    }

    private function formatInputDate($dateStr)
    {
        if (empty($dateStr)) return null;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', trim($dateStr), $matches)) {
            return "{$matches[3]}/{$matches[2]}/{$matches[1]}"; // Converts YYYY-MM-DD to DD/MM/YYYY
        }
        return trim($dateStr);
    }

    private function getRomanNumeral($integer)
    {
        $table = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
        ];
        $return = '';
        while ($integer > 0) {
            foreach ($table as $rom => $arp) {
                if($integer >= $arp) {
                    $integer -= $arp;
                    $return .= $rom;
                    break;
                }
            }
        }
        return $return;
    }
}
