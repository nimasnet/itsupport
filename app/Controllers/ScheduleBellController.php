<?php

namespace App\Controllers;

use App\Models\ScheduleBellModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ScheduleBellController extends BaseController
{
    private function getDynamicTimeColumns()
    {
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('schedule_bell');
        $timeCols = [];

        foreach ($fields as $field) {
            if (strpos($field, 't_') === 0) {
                $clean = str_replace('t_', '', $field);
                if (strlen($clean) === 3) $clean = '0' . $clean;
                if (strlen($clean) === 4) {
                    $h = substr($clean, 0, 2);
                    $m = substr($clean, 2, 2);
                    $timeCols[] = [
                        'key' => $field,
                        'label' => $h . ':' . $m,
                        'sort_val' => ((int)$h * 3600) + ((int)$m * 60)
                    ];
                }
            }
        }

        // Sort chronologically
        usort($timeCols, function($a, $b) {
            return $a['sort_val'] <=> $b['sort_val'];
        });

        return $timeCols;
    }

    public function index()
    {
        if (!has_access('schedule_bell.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $topColor  = '#0077b6';
        $sideColor = '#f0f2f5';
        try {
            $settingModel = new \App\Models\SettingModel();
            $setting = $settingModel->first();
            if ($setting) {
                $topColor  = $setting['top_color']  ?? $topColor;
                $sideColor = $setting['side_color'] ?? $sideColor;
            }
        } catch (\Exception $e) {}

        // Auto-ensure announcement_text & audio_file column exists
        try {
            $db = \Config\Database::connect();
            if (!$db->fieldExists('announcement_text', 'schedule_bell')) {
                $forge = \Config\Database::forge();
                $forge->addColumn('schedule_bell', [
                    'announcement_text' => ['type' => 'TEXT', 'null' => true]
                ]);
            }
            if (!$db->fieldExists('audio_file', 'schedule_bell')) {
                $forge = \Config\Database::forge();
                $forge->addColumn('schedule_bell', [
                    'audio_file' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
                ]);
            }

            // Ensure upload directory exists
            $uploadDir = FCPATH . 'uploads/audio_bells/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }

            // Auto-ensure new time columns
            $newTimeCols = [
                't_925', 't_1240', 't_1245', 't_1246', 't_1247', 't_1300', 't_1301', 't_1305',
                't_1310', 't_1315', 't_1316', 't_1325', 't_1330', 't_1335', 't_1345',
                't_1358', 't_1410', 't_1430', 't_1500', 't_1505', 't_1530', 't_1555',
                't_1558', 't_1600', 't_1615', 't_1625', 't_1630', 't_1645'
            ];
            // Auto-clean any stray 'undefined' content values in database
            $db->table('schedule_bell')->where('conten', 'undefined')->update(['conten' => 'Jadwal Bel']);
        } catch (\Exception $e) {}

        $model = new ScheduleBellModel();
        $schedules = $model->orderBy('no', 'ASC')->findAll();

        $data = [
            'title'        => 'Schedule Bell',
            'top_color'    => $topColor,
            'side_color'   => $sideColor,
            'current_user' => session()->get('user'),
            'current_role' => session()->get('role'),
            'schedules'    => $schedules,
            'timeColumns'  => $this->getDynamicTimeColumns()
        ];

        return view('schedule_bell/index', $data);
    }

    public function store()
    {
        if (!has_access('schedule_bell.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $model = new ScheduleBellModel();
        $postData = $this->request->getPost();

        // 1. Collect all times from custom_time_input
        $timesList = [];
        $customTime = $this->request->getPost('custom_time_input');
        if (!empty($customTime)) {
            $rawList = is_array($customTime) ? $customTime : explode(',', $customTime);
            foreach ($rawList as $tRaw) {
                $tTrim = trim($tRaw);
                if (!empty($tTrim)) {
                    $timesList[] = $tTrim;
                }
            }
        }

        // Also check if any t_* fields were directly posted as 1
        foreach ($postData as $key => $val) {
            if (strpos($key, 't_') === 0 && (int)$val === 1) {
                $cleanKey = str_replace('t_', '', $key);
                if (strlen($cleanKey) === 3) $cleanKey = '0' . $cleanKey;
                if (strlen($cleanKey) === 4) {
                    $timeStr = substr($cleanKey, 0, 2) . ':' . substr($cleanKey, 2, 2) . ':00';
                    if (!in_array($timeStr, $timesList, true)) {
                        $timesList[] = $timeStr;
                    }
                }
            }
        }

        // Default fallback time if empty
        if (empty($timesList)) {
            $timesList = ['07:00:00'];
        }

        // Handle audio file upload if provided
        $audioFile = $postData['audio_file'] ?? '';
        $audioUpload = $this->request->getFile('audio_file_upload');
        if ($audioUpload && $audioUpload->isValid() && !$audioUpload->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/audio_bells/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $cleanName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $audioUpload->getClientName());
            if ($audioUpload->move($uploadDir, $cleanName)) {
                $audioFile = 'uploads/audio_bells/' . $cleanName;
            }
        }

        $contenVal = trim($postData['conten'] ?? '');
        if (empty($contenVal) || $contenVal === 'undefined') {
            $contenVal = 'Jadwal Bel';
        }

        $baseData = [
            'conten'            => $contenVal,
            'building'          => !empty($postData['building']) ? $postData['building'] : '',
            'day_1'             => (int)($postData['day_1'] ?? 0),
            'day_2'             => (int)($postData['day_2'] ?? 0),
            'day_3'             => (int)($postData['day_3'] ?? 0),
            'day_4'             => (int)($postData['day_4'] ?? 0),
            'day_5'             => (int)($postData['day_5'] ?? 0),
            'day_6'             => (int)($postData['day_6'] ?? 0),
            'spk_in'            => (int)($postData['spk_in'] ?? 0),
            'spk_out'           => (int)($postData['spk_out'] ?? 0),
            'highlight'         => (int)($postData['highlight'] ?? 0),
            'announcement_text' => $postData['announcement_text'] ?? '',
            'audio_file'        => $audioFile,
        ];

        $db = \Config\Database::connect();
        $fieldsInTable = $db->getFieldNames('schedule_bell');

        $createdCount = 0;

        foreach ($timesList as $tStr) {
            $colKey = $this->ensureCustomTimeColumn($tStr);
            if (!$colKey) continue;

            $rowPayload = $baseData;

            // Set all t_* fields in payload to 0 for this row
            foreach (array_keys($rowPayload) as $k) {
                if (strpos($k, 't_') === 0) {
                    $rowPayload[$k] = 0;
                }
            }
            // Explicitly set the active time column to 1 (handles newly created columns)
            $rowPayload[$colKey] = 1;

            $cleanPayload = $this->cleanDataForDatabase($rowPayload);
            if ($model->insert($cleanPayload)) {
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            $this->reorderSchedulesByTime();
            $this->logActivity('CREATE', "Menambahkan {$createdCount} jadwal baru: " . ($baseData['conten'] ?? ''));
            return redirect()->to('/schedule-bell')->with('success', "{$createdCount} Jadwal bel berhasil ditambahkan.");
        }

        return redirect()->to('/schedule-bell')->with('error', 'Gagal menambahkan jadwal.');
    }

    public function update($id)
    {
        if (!has_access('schedule_bell.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $model = new ScheduleBellModel();
        $postData = $this->request->getPost();

        $timesList = [];
        $customTime = $this->request->getPost('custom_time_input');
        if (!empty($customTime)) {
            $rawList = is_array($customTime) ? $customTime : explode(',', $customTime);
            foreach ($rawList as $tRaw) {
                $tTrim = trim($tRaw);
                if (!empty($tTrim)) {
                    $timesList[] = $tTrim;
                }
            }
        }

        $audioFile = $postData['audio_file'] ?? '';
        $audioUpload = $this->request->getFile('audio_file_upload');
        if ($audioUpload && $audioUpload->isValid() && !$audioUpload->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/audio_bells/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $cleanName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $audioUpload->getClientName());
            if ($audioUpload->move($uploadDir, $cleanName)) {
                $audioFile = 'uploads/audio_bells/' . $cleanName;
            }
        }

        $contenVal = trim($postData['conten'] ?? '');
        if (empty($contenVal) || $contenVal === 'undefined') {
            $contenVal = 'Jadwal Bel';
        }

        $baseData = [
            'conten'            => $contenVal,
            'building'          => !empty($postData['building']) ? $postData['building'] : '',
            'day_1'             => (int)($postData['day_1'] ?? 0),
            'day_2'             => (int)($postData['day_2'] ?? 0),
            'day_3'             => (int)($postData['day_3'] ?? 0),
            'day_4'             => (int)($postData['day_4'] ?? 0),
            'day_5'             => (int)($postData['day_5'] ?? 0),
            'day_6'             => (int)($postData['day_6'] ?? 0),
            'spk_in'            => (int)($postData['spk_in'] ?? 0),
            'spk_out'           => (int)($postData['spk_out'] ?? 0),
            'highlight'         => (int)($postData['highlight'] ?? 0),
            'announcement_text' => $postData['announcement_text'] ?? '',
        ];
        if (!empty($audioFile)) {
            $baseData['audio_file'] = $audioFile;
        }

        $db = \Config\Database::connect();
        $fieldsInTable = $db->getFieldNames('schedule_bell');

        // Reset all t_* fields to 0 for this row based on fields in table
        // (to clear out any existing times for this ID)
        foreach ($fieldsInTable as $f) {
            if (strpos($f, 't_') === 0) {
                $baseData[$f] = 0;
            }
        }

        // We will keep the first time for the current row
        $firstTime = array_shift($timesList);
        if ($firstTime) {
            $colKey = $this->ensureCustomTimeColumn($firstTime);
            if ($colKey) {
                $baseData[$colKey] = 1;
            }
        }

        $cleanData = $this->cleanDataForDatabase($baseData);

        if ($model->update($id, $cleanData)) {
            // If there are remaining times, insert them as new rows
            if (!empty($timesList)) {
                foreach ($timesList as $tStr) {
                    $colKey = $this->ensureCustomTimeColumn($tStr);
                    if (!$colKey) continue;

                    $rowPayload = $baseData;
                    // Reset all t_* fields in payload to 0 (to ensure isolation)
                    foreach (array_keys($rowPayload) as $k) {
                        if (strpos($k, 't_') === 0) {
                            $rowPayload[$k] = 0;
                        }
                    }
                    $rowPayload[$colKey] = 1;

                    $cleanNewData = $this->cleanDataForDatabase($rowPayload);
                    $model->insert($cleanNewData);
                }
            }

            $this->reorderSchedulesByTime();
            $this->logActivity('UPDATE', "Memperbarui jadwal ID #{$id} (" . ($cleanData['conten'] ?? '') . ")");
            return redirect()->to('/schedule-bell')->with('success', 'Jadwal berhasil diperbarui.');
        }

        return redirect()->to('/schedule-bell')->with('error', 'Gagal memperbarui jadwal.');
    }

    public function delete($id)
    {
        if (!has_access('schedule_bell.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $model = new ScheduleBellModel();
        
        if ($model->delete($id)) {
            $this->reorderSchedulesByTime();
            $this->logActivity('DELETE', "Menghapus jadwal ID #{$id}");
            return redirect()->to('/schedule-bell')->with('success', 'Jadwal berhasil dihapus.');
        }
        
        return redirect()->to('/schedule-bell')->with('error', 'Gagal menghapus jadwal.');
    }

    public function exportExcel()
    {
        if (!has_access('schedule_bell.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $model = new ScheduleBellModel();
        $schedules = $model->orderBy('no', 'ASC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Schedule Bell');

        // Set Base Headers
        $headers = [
            'A1' => 'NO',
            'B1' => 'CONTEN',
            'C1' => 'BUILDING',
            'D1' => 'DAYS RING (S)',
            'E1' => 'DAYS RING (S)',
            'F1' => 'DAYS RING (R)',
            'G1' => 'DAYS RING (K)',
            'H1' => 'DAYS RING (J)',
            'I1' => 'DAYS RING (S)',
            'J1' => 'SPEAKER IN',
            'K1' => 'SPEAKER OUT',
        ];

        // 60 Time Map Columns
        $timeColumns = $this->getDynamicTimeColumns();
        $timeMap = [];
        foreach ($timeColumns as $tc) {
            $timeMap[$tc['key']] = $tc['label'];
        }

        $colIndex = 12; // Column L
        foreach ($timeMap as $tk => $lbl) {
            $cellAddr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '1';
            $headers[$cellAddr] = $lbl;
            $colIndex++;
        }

        // Additional headers for audio, text, highlight
        $headers[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '1'] = 'ANNOUNCEMENT_TEXT';
        $colIndex++;
        $headers[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '1'] = 'AUDIO_FILE';
        $colIndex++;
        $headers[\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '1'] = 'HIGHLIGHT (KUNING)';
        $lastColIndex = $colIndex;

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Style Header
        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF3A5A99']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A1:' . $lastColLetter . '1')->applyFromArray($headerStyle);

        // Insert Data
        $rowNum = 2;
        foreach ($schedules as $row) {
            $sheet->setCellValue('A' . $rowNum, $row['no']);
            $sheet->setCellValue('B' . $rowNum, $row['conten']);
            $sheet->setCellValue('C' . $rowNum, $row['building']);
            $sheet->setCellValue('D' . $rowNum, !empty($row['day_1']) ? 'ON' : '');
            $sheet->setCellValue('E' . $rowNum, !empty($row['day_2']) ? 'ON' : '');
            $sheet->setCellValue('F' . $rowNum, !empty($row['day_3']) ? 'ON' : '');
            $sheet->setCellValue('G' . $rowNum, !empty($row['day_4']) ? 'ON' : '');
            $sheet->setCellValue('H' . $rowNum, !empty($row['day_5']) ? 'ON' : '');
            $sheet->setCellValue('I' . $rowNum, !empty($row['day_6']) ? 'ON' : '');
            $sheet->setCellValue('J' . $rowNum, !empty($row['spk_in']) ? 'ON' : '');
            $sheet->setCellValue('K' . $rowNum, !empty($row['spk_out']) ? 'ON' : '');
            
            $cIdx = 12;
            foreach (array_keys($timeMap) as $tk) {
                $cellAddr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx) . $rowNum;
                $sheet->setCellValue($cellAddr, !empty($row[$tk]) ? 'ON' : '');
                $cIdx++;
            }
            
            $cellTextAddr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx) . $rowNum;
            $sheet->setCellValue($cellTextAddr, $row['announcement_text'] ?? '');
            $cIdx++;

            $cellAudioAddr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx) . $rowNum;
            $sheet->setCellValue($cellAudioAddr, $row['audio_file'] ?? '');
            $cIdx++;

            $cellHighlightAddr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cIdx) . $rowNum;
            $sheet->setCellValue($cellHighlightAddr, !empty($row['highlight']) ? 'ON' : '');
            $rowNum++;
        }

        // Auto size columns for readability
        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Schedule_Bell_Template_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

    public function importExcel()
    {
        if (!has_access('schedule_bell.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $file = $this->request->getFile('excel_file');
        if (!$file || !$file->isValid()) {
            return redirect()->to('/schedule-bell')->with('error', 'Pilih file Excel yang valid.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return redirect()->to('/schedule-bell')->with('error', 'File Excel kosong atau tidak memiliki data.');
            }

            // Build header column map
            $headerMap = [];
            $dynamicTimeHeaders = [];
            if (!empty($rows[0])) {
                foreach ($rows[0] as $cIdx => $val) {
                    if ($val !== null && $val !== '') {
                        $cleanHeader = trim(strtoupper((string)$val));
                        $headerMap[$cleanHeader] = $cIdx;

                        // Detect time headers (e.g. '07:11' or '07:11:00')
                        if (preg_match('/^(\d{1,2}):(\d{2})(:\d{2})?$/', $cleanHeader, $matches)) {
                            $timeStr = sprintf('%02d:%02d', (int)$matches[1], (int)$matches[2]);
                            $dynamicTimeHeaders[$cleanHeader] = [
                                'timeStr' => $timeStr,
                                'cIdx' => $cIdx
                            ];
                        }
                    }
                }
            }

            $model = new ScheduleBellModel();
            
            // Clear existing data to replace with new imported data
            $model->truncate();

            $isTruth = function($val) {
                if (empty($val)) return 0;
                return in_array(trim(strtoupper((string)$val)), ['1', 'V', 'Y', 'YES', 'TRUE', 'ON']) ? 1 : 0;
            };

            // Start from row 1 (index 1 in array, since row 0 is header)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip empty rows
                if (empty(trim((string)($row[0] ?? ''))) && empty(trim((string)($row[1] ?? '')))) {
                    continue;
                }

                $data = [
                    'no'        => $row[0] ?? 0,
                    'conten'    => $row[1] ?? '',
                    'building'  => $row[2] ?? '',
                    'day_1'     => $isTruth($row[3] ?? null),
                    'day_2'     => $isTruth($row[4] ?? null),
                    'day_3'     => $isTruth($row[5] ?? null),
                    'day_4'     => $isTruth($row[6] ?? null),
                    'day_5'     => $isTruth($row[7] ?? null),
                    'day_6'     => $isTruth($row[8] ?? null),
                    'spk_in'    => $isTruth($row[9] ?? null),
                    'spk_out'   => $isTruth($row[10] ?? null),
                ];

                // Parse dynamic time columns from the Excel headers
                foreach ($dynamicTimeHeaders as $lbl => $meta) {
                    $cIdx = $meta['cIdx'];
                    $timeStr = $meta['timeStr'];
                    $isActive = isset($row[$cIdx]) ? $isTruth($row[$cIdx]) : 0;
                    
                    if ($isActive === 1) {
                        $colKey = $this->ensureCustomTimeColumn($timeStr);
                        if ($colKey) {
                            $data[$colKey] = 1;
                        }
                    }
                }

                // Parse announcement_text
                $annIdx = $headerMap['ANNOUNCEMENT_TEXT'] ?? ($headerMap['ANNOUNCEMENT'] ?? ($headerMap['TEXT'] ?? 70));
                $data['announcement_text'] = isset($row[$annIdx]) ? trim((string)$row[$annIdx]) : '';

                // Parse audio_file
                $audioIdx = $headerMap['AUDIO_FILE'] ?? ($headerMap['AUDIO'] ?? 71);
                $data['audio_file'] = isset($row[$audioIdx]) ? trim((string)$row[$audioIdx]) : '';

                // Parse highlight (Supports 72 in new format, or 43 in old template format)
                $highIdx = $headerMap['HIGHLIGHT (KUNING)'] ?? ($headerMap['HIGHLIGHT'] ?? (count($row) <= 50 ? 43 : 72));
                $data['highlight'] = isset($row[$highIdx]) ? $isTruth($row[$highIdx]) : 0;

                $model->insert($data);
            }

            $this->reorderSchedulesByTime();
            $this->logActivity('IMPORT', 'Melakukan Import Excel Schedule Bell.');
            return redirect()->to('/schedule-bell')->with('success', 'Data Excel berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->to('/schedule-bell')->with('error', 'Terjadi kesalahan saat memproses file Excel: ' . $e->getMessage());
        }
    }

    public function inlineUpdate()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $id    = $this->request->getPost('id');
        $field = $this->request->getPost('field');
        $value = $this->request->getPost('value');

        if (!$id || !$field) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Parameter tidak lengkap.']);
        }

        $model = new ScheduleBellModel();
        $row   = $model->find($id);

        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        $allowed = [
            'no', 'conten', 'building', 'announcement_text', 'audio_file', 'highlight',
            'day_1', 'day_2', 'day_3', 'day_4', 'day_5', 'day_6',
            'spk_in', 'spk_out',
            't_001', 't_100', 't_600', 't_650', 't_700', 't_705', 't_710', 't_715',
            't_730', 't_800', 't_810', 't_830', 't_858', 't_900', 't_910', 't_930',
            't_940', 't_945', 't_950', 't_1000', 't_1010', 't_1030', 't_1058',
            't_1100', 't_1110', 't_1115', 't_1130', 't_1145', 't_1200', 't_1201',
            't_1210', 't_1230',
            't_1240', 't_1245', 't_1246', 't_1247', 't_1300', 't_1301', 't_1305',
            't_1310', 't_1315', 't_1316', 't_1325', 't_1330', 't_1335', 't_1345',
            't_1358', 't_1410', 't_1430', 't_1500', 't_1530', 't_1505', 't_1555',
            't_1558', 't_1600', 't_1615', 't_1625', 't_1630', 't_1645'
        ];

        if (!in_array($field, $allowed)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Field tidak valid.']);
        }

        if ($model->update($id, [$field => $value])) {
            $this->reorderSchedulesByTime();
            $this->logActivity('INLINE_EDIT', "Mengubah field '$field' menjadi '$value' pada ID #{$id}");
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => 'Berhasil diperbarui.',
                'id'         => $id,
                'field'      => $field,
                'value'      => $value,
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui database.']);
    }

    public function duplicate($id)
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $model = new ScheduleBellModel();
        $row   = $model->find($id);

        if (!$row) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        unset($row['id'], $row['created_at'], $row['updated_at']);
        $row['conten'] = $row['conten'] . ' (Copy)';

        $maxNo = $model->selectMax('no')->first();
        $row['no'] = ($maxNo['no'] ?? 0) + 1;

        if ($newId = $model->insert($row)) {
            $this->reorderSchedulesByTime();
            $this->logActivity('DUPLICATE', "Menduplikasi jadwal ID #{$id} menjadi ID #{$newId}");
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => 'Jadwal berhasil diduplikasi.',
                'new_id'     => $newId,
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menduplikasi data.']);
    }

    public function applyPreset()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $action = $this->request->getPost('action');
        $db     = \Config\Database::connect();

        if ($action === 'workdays') {
            $db->table('schedule_bell')->update(['day_1' => 1, 'day_2' => 1, 'day_3' => 1, 'day_4' => 1, 'day_5' => 1]);
            $msg = 'Preset Hari Kerja (Senin-Jumat) berhasil diterapkan ke semua jadwal.';
        } elseif ($action === 'spk_in') {
            $db->table('schedule_bell')->update(['spk_in' => 1]);
            $msg = 'Preset Speaker Indoor (IN) berhasil diterapkan ke semua jadwal.';
        } elseif ($action === 'spk_out') {
            $db->table('schedule_bell')->update(['spk_out' => 1]);
            $msg = 'Preset Speaker Outdoor (OUT) berhasil diterapkan ke semua jadwal.';
        } elseif ($action === 'clear_ring') {
            $timeKeys = [
                't_001', 't_100', 't_600', 't_650', 't_700', 't_705', 't_710', 't_715',
                't_730', 't_800', 't_810', 't_830', 't_858', 't_900', 't_910', 't_930',
                't_940', 't_945', 't_950', 't_1000', 't_1010', 't_1030', 't_1058',
                't_1100', 't_1110', 't_1115', 't_1130', 't_1145', 't_1200', 't_1201',
                't_1210', 't_1230',
                't_1240', 't_1245', 't_1246', 't_1247', 't_1300', 't_1301', 't_1305',
                't_1310', 't_1315', 't_1316', 't_1325', 't_1330', 't_1335', 't_1345',
                't_1358', 't_1410', 't_1430', 't_1500', 't_1530', 't_1505', 't_1555',
                't_1558', 't_1600', 't_1615', 't_1625', 't_1630', 't_1645'
            ];
            $clearData = array_fill_keys($timeKeys, 0);
            $db->table('schedule_bell')->update($clearData);
            $msg = 'Semua Dering Waktu berhasil dibersihkan.';
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Aksi preset tidak dikenal.']);
        }

        $this->logActivity('PRESET', "Menerapkan preset '$action'");
        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => $msg,
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash()
        ]);
    }

    public function display()
    {
        $settingModel = new \App\Models\SettingModel();
        $setting = $settingModel->first();
        $bypass = $setting['schedule_bypass'] ?? 0;

        if ($bypass != 1) {
            if (!session()->get('logged_in')) {
                return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu.');
            }
            if (!has_access('schedule_bell.php')) {
                return redirect()->to('/')->with('error', 'Akses ditolak.');
            }
        }

        $model     = new ScheduleBellModel();
        $schedules = $model->orderBy('no', 'ASC')->findAll();
        $holidays  = $this->loadHolidaysArray();

        $data = [
            'title'       => 'Kiosk TV Display Mode - Schedule Bell',
            'schedules'   => $schedules,
            'holidays'    => $holidays,
            'timeColumns' => $this->getDynamicTimeColumns()
        ];

        return view('schedule_bell/display', $data);
    }

    public function printView()
    {
        if (!has_access('schedule_bell.php')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $model     = new ScheduleBellModel();
        $schedules = $model->orderBy('no', 'ASC')->findAll();

        $data = [
            'title'       => 'Cetak Schedule Ring of Information Audio',
            'schedules'   => $schedules,
            'timeColumns' => $this->getDynamicTimeColumns()
        ];

        return view('schedule_bell/print', $data);
    }

    public function getHolidays()
    {
        $holidays = $this->loadHolidaysArray();
        return $this->response->setJSON([
            'status'   => 'success',
            'holidays' => $holidays
        ]);
    }

    public function getLogs()
    {
        $filePath = WRITEPATH . 'schedule_bell_logs.json';
        $logs = [];
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $arr  = json_decode($json, true);
            if (is_array($arr)) $logs = $arr;
        }
        return $this->response->setJSON([
            'status' => 'success',
            'logs'   => $logs
        ]);
    }

    public function toggleHoliday()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $date  = $this->request->getPost('date');
        $note  = $this->request->getPost('note') ?? 'Hari Libur';

        if (!$date) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal tidak valid.']);
        }

        $holidays = $this->loadHolidaysArray();
        $foundIndex = -1;
        foreach ($holidays as $idx => $h) {
            if ($h['date'] === $date) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex >= 0) {
            array_splice($holidays, $foundIndex, 1);
            $msg = "Tanggal $date dihapus dari daftar Hari Libur.";
        } else {
            $holidays[] = ['date' => $date, 'note' => $note];
            $msg = "Tanggal $date ditambahkan sebagai Hari Libur ($note).";
        }

        $this->saveHolidaysArray($holidays);
        $this->logActivity('HOLIDAY', "Mengubah status hari libur pada tanggal $date ($note)");

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => $msg,
            'holidays'   => $holidays,
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash()
        ]);
    }

    private function logActivity($action, $details)
    {
        $filePath = WRITEPATH . 'schedule_bell_logs.json';
        $logs = [];
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $arr  = json_decode($json, true);
            if (is_array($arr)) $logs = $arr;
        }

        $user = session()->get('user');
        $username = is_array($user) ? ($user['username'] ?? 'User') : (is_string($user) ? $user : 'User');

        array_unshift($logs, [
            'timestamp' => date('Y-m-d H:i:s'),
            'user'      => $username,
            'action'    => $action,
            'details'   => $details
        ]);

        if (count($logs) > 200) {
            $logs = array_slice($logs, 0, 200);
        }

        file_put_contents($filePath, json_encode($logs, JSON_PRETTY_PRINT));
    }

    public function getProfiles()
    {
        $profiles = $this->loadProfilesArray();
        return $this->response->setJSON([
            'status'   => 'success',
            'profiles' => array_keys($profiles)
        ]);
    }

    public function saveProfile()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $profileName = trim($this->request->getPost('profile_name') ?? '');
        if (!$profileName) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nama profil tidak boleh kosong.']);
        }

        $model = new ScheduleBellModel();
        $schedules = $model->orderBy('no', 'ASC')->findAll();

        $profiles = $this->loadProfilesArray();
        $profiles[$profileName] = [
            'updated_at' => date('Y-m-d H:i:s'),
            'data'       => $schedules
        ];

        $this->saveProfilesArray($profiles);
        $this->logActivity('PROFILE', "Menyimpan profil shift: '$profileName'");

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => "Profil shift '$profileName' berhasil disimpan.",
            'profiles'   => array_keys($profiles),
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash()
        ]);
    }

    public function switchProfile()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $profileName = trim($this->request->getPost('profile_name') ?? '');
        $profiles = $this->loadProfilesArray();

        if (!isset($profiles[$profileName])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Profil tidak ditemukan.']);
        }

        $model = new ScheduleBellModel();
        $model->truncate();

        $dataset = $profiles[$profileName]['data'] ?? [];
        foreach ($dataset as $row) {
            unset($row['id'], $row['created_at'], $row['updated_at']);
            $model->insert($row);
        }

        $this->logActivity('PROFILE', "Mengalihkan profil shift aktif ke: '$profileName'");

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => "Berhasil mengaktifkan profil shift '$profileName'.",
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash()
        ]);
    }

    public function generatePeriodic()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $conten    = trim($this->request->getPost('conten') ?? 'Pengumuman Berkala');
        $building  = trim($this->request->getPost('building') ?? 'Production Building');
        $announce  = trim($this->request->getPost('announcement_text') ?? '');
        $startHour = intval($this->request->getPost('start_hour') ?? 8);
        $endHour   = intval($this->request->getPost('end_hour') ?? 16);
        $interval  = intval($this->request->getPost('interval_min') ?? 60);

        if ($interval <= 0) $interval = 60;

        $model = new ScheduleBellModel();
        $maxNoRow = $model->selectMax('no')->first();
        $startNo = ($maxNoRow['no'] ?? 0) + 1;

        $mornTimes = [
            '001'=>'00:01', '100'=>'01:00', '600'=>'06:00', '650'=>'06:50', '700'=>'07:00', '705'=>'07:05', '710'=>'07:10', '715'=>'07:15',
            '730'=>'07:30', '800'=>'08:00', '810'=>'08:10', '830'=>'08:30', '858'=>'08:58'
        ];
        $noonTimes = [
            '900'=>'09:00', '910'=>'09:10', '930'=>'09:30', '940'=>'09:40', '945'=>'09:45', '950'=>'09:50', '1000'=>'10:00', '1010'=>'10:10',
            '1030'=>'10:30', '1058'=>'10:58', '1100'=>'11:00', '1110'=>'11:10', '1115'=>'11:15', '1130'=>'11:30', '1145'=>'11:45',
            '1200'=>'12:00', '1201'=>'12:01', '1210'=>'12:10', '1230'=>'12:30'
        ];
        $allTimeMap = array_merge($mornTimes, $noonTimes);

        $timeFlags = [];
        $startSecs = $startHour * 3600;
        $endSecs   = $endHour * 3600;

        for ($secs = $startSecs; $secs <= $endSecs; $secs += ($interval * 60)) {
            $h = floor($secs / 3600);
            $m = floor(($secs % 3600) / 60);
            $targetTimeStr = sprintf('%02d:%02d', $h, $m);

            foreach ($allTimeMap as $tKey => $tStr) {
                if ($tStr === $targetTimeStr) {
                    $timeFlags['t_' . $tKey] = 1;
                }
            }
        }

        if (empty($timeFlags)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada kolom waktu dering yang cocok untuk interval tersebut.']);
        }

        $row = array_merge([
            'no'                => $startNo,
            'conten'            => $conten,
            'building'          => $building,
            'announcement_text' => $announce,
            'day_1'             => 1, 'day_2' => 1, 'day_3' => 1, 'day_4' => 1, 'day_5' => 1, 'day_6' => 0,
            'spk_in'            => 1, 'spk_out' => 1, 'highlight' => 0
        ], $timeFlags);

        $model->insert($row);
        $this->reorderSchedulesByTime();
        $this->logActivity('PERIODIC_GEN', "Membuat jadwal berkala '$conten' (Interval $interval mnt)");

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => "Jadwal berkala '$conten' berhasil dibuat!",
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash()
        ]);
    }

    private function loadProfilesArray()
    {
        $filePath = WRITEPATH . 'schedule_bell_profiles.json';
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $arr  = json_decode($json, true);
            if (is_array($arr)) return $arr;
        }
        return [];
    }

    private function saveProfilesArray($arr)
    {
        $filePath = WRITEPATH . 'schedule_bell_profiles.json';
        file_put_contents($filePath, json_encode($arr, JSON_PRETTY_PRINT));
    }

    private function loadHolidaysArray()
    {
        $filePath = WRITEPATH . 'schedule_bell_holidays.json';
        if (file_exists($filePath)) {
            $json = file_get_contents($filePath);
            $arr  = json_decode($json, true);
            if (is_array($arr)) return $arr;
        }
        return [];
    }

    private function saveHolidaysArray($arr)
    {
        $filePath = WRITEPATH . 'schedule_bell_holidays.json';
        file_put_contents($filePath, json_encode($arr, JSON_PRETTY_PRINT));
    }

    public function getAudioFiles()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $uploadDir = FCPATH . 'uploads/audio_bells/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $files = [];
        $scanned = glob($uploadDir . '*.*');
        if ($scanned) {
            foreach ($scanned as $f) {
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
                    $basename = basename($f);
                    $files[] = [
                        'name'  => $basename,
                        'path'  => 'uploads/audio_bells/' . $basename,
                        'url'   => base_url('uploads/audio_bells/' . $basename),
                        'size'  => round(filesize($f) / 1024, 1) . ' KB',
                        'mtime' => date('d M Y H:i', filemtime($f))
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'files'  => $files
        ]);
    }

    public function uploadAudio()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $audioFile = $this->request->getFile('audio_file');
        if (!$audioFile || !$audioFile->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File audio tidak valid.']);
        }

        $uploadDir = FCPATH . 'uploads/audio_bells/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $origName  = $audioFile->getClientName();
        $cleanName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
        $targetPath = $uploadDir . $cleanName;

        if (file_exists($targetPath)) {
            $cleanName = time() . '_' . $cleanName;
        }

        if ($audioFile->move($uploadDir, $cleanName)) {
            $relPath = 'uploads/audio_bells/' . $cleanName;
            $this->logActivity('AUDIO_UPLOAD', "Mengunggah file audio baru: {$cleanName}");
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => 'File audio berhasil diunggah.',
                'file_name'  => $cleanName,
                'file_path'  => $relPath,
                'file_url'   => base_url($relPath),
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengunggah file.']);
    }

    public function deleteAudio()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $filename = basename($this->request->getPost('filename') ?? '');
        if (!$filename) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nama file tidak valid.']);
        }

        $filePath = FCPATH . 'uploads/audio_bells/' . $filename;
        if (file_exists($filePath)) {
            @unlink($filePath);
            $this->logActivity('AUDIO_DELETE', "Menghapus file audio: {$filename}");
            return $this->response->setJSON([
                'status'     => 'success',
                'message'    => "File audio '{$filename}' berhasil dihapus.",
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash()
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak ditemukan.']);
    }

    public function reorder()
    {
        if (!has_access('schedule_bell.php')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.']);
        }

        $this->reorderSchedulesByTime();
        $this->logActivity('REORDER', 'Mengurutkan ulang jadwal berdasarkan waktu secara otomatis.');

        return $this->response->setJSON([
            'status'     => 'success',
            'message'    => 'Seluruh urutan jadwal (Nomor Urut) berhasil disesuaikan secara kronologis berdasarkan waktu!',
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash()
        ]);
    }

    private function ensureCustomTimeColumn($timeStr)
    {
        static $checkedCols = [];

        $parts = explode(':', trim($timeStr));
        if (count($parts) < 2) return null;

        $h = sprintf('%02d', (int)$parts[0]);
        $m = sprintf('%02d', (int)$parts[1]);

        $numH = (int)$h;
        $numM = (int)$m;
        
        $key = 't_' . ($numH == 0 ? sprintf('%02d', $numM) : ($numH . sprintf('%02d', $numM)));

        if (isset($checkedCols[$key])) {
            return $key;
        }

        try {
            $db = \Config\Database::connect();
            
            if (!$db->fieldExists($key, 'schedule_bell')) {
                $forge = \Config\Database::forge();
                $forge->addColumn('schedule_bell', [
                    $key => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0]
                ]);
            }
            
            $checkedCols[$key] = true;
            return $key;
        } catch (\Throwable $e) {
            $checkedCols[$key] = true;
            return $key; // Return key anyway, it likely already exists
        }
    }

    private function reorderSchedulesByTime()
    {
        $model = new ScheduleBellModel();
        $schedules = $model->findAll();

        if (empty($schedules)) return;

        $timeSecondsMap = [
            't_001' => 60,     't_100' => 3600,   't_600' => 21600,  't_650' => 24600,
            't_700' => 25200,  't_705' => 25500,  't_710' => 25800,  't_715' => 26100,
            't_730' => 27000,  't_800' => 28800,  't_810' => 29400,  't_830' => 30600,
            't_858' => 32280,  't_900' => 32400,  't_910' => 33000,  't_930' => 34200,
            't_940' => 34800,  't_945' => 35100,  't_950' => 35400,  't_1000'=> 36000,
            't_1010'=> 36600,  't_1030'=> 37800,  't_1058'=> 39480,  't_1100'=> 39600,
            't_1110'=> 39960,  't_1115'=> 40500,  't_1130'=> 41400,  't_1145'=> 42300,
            't_1200'=> 43200,  't_1201'=> 43260,  't_1210'=> 43800,  't_1230'=> 45000,
            't_1240'=> 45600,  't_1245'=> 45900,  't_1246'=> 45960,  't_1247'=> 46020,
            't_1300'=> 46800,  't_1301'=> 46860,  't_1305'=> 47100,  't_1310'=> 47400,
            't_1315'=> 47700,  't_1316'=> 47760,  't_1325'=> 48300,  't_1330'=> 48600,
            't_1335'=> 48900,  't_1410'=> 51000,  't_1430'=> 52200,  't_1500'=> 54000,
            't_1505'=> 54300,  't_1530'=> 55800,  't_1555'=> 57300,  't_1558'=> 57480,
            't_1600'=> 57600,  't_1615'=> 58500,  't_1625'=> 59100,  't_1630'=> 59400,
            't_1645'=> 60300
        ];

        $getEarliest = function($row) use ($timeSecondsMap) {
            $minSecs = 999999;
            foreach ($row as $k => $val) {
                if ($val == 1 && strpos($k, 't_') === 0) {
                    if (isset($timeSecondsMap[$k])) {
                        $secs = $timeSecondsMap[$k];
                    } else {
                        $clean = str_replace('t_', '', $k);
                        if (strlen($clean) === 3) $clean = '0' . $clean;
                        if (strlen($clean) === 4) {
                            $h = (int)substr($clean, 0, 2);
                            $m = (int)substr($clean, 2, 2);
                            $secs = $h * 3600 + $m * 60;
                        } else {
                            $secs = 999999;
                        }
                    }
                    if ($secs < $minSecs) {
                        $minSecs = $secs;
                    }
                }
            }
            return $minSecs;
        };

        usort($schedules, function($a, $b) use ($getEarliest) {
            $secA = $getEarliest($a);
            $secB = $getEarliest($b);
            if ($secA === $secB) {
                return (int)$a['id'] <=> (int)$b['id'];
            }
            return $secA <=> $secB;
        });

        $db = \Config\Database::connect();
        $num = 1;
        foreach ($schedules as $item) {
            if ($item['no'] != $num) {
                $db->table('schedule_bell')->where('id', $item['id'])->update(['no' => $num]);
            }
            $num++;
        }
    }

    private function cleanDataForDatabase(array $data): array
    {
        try {
            $db = \Config\Database::connect();
            $fieldsInTable = $db->getFieldNames('schedule_bell');
            $clean = [];
            foreach ($data as $key => $val) {
                // Allow the key if it's in the cached fields list, OR if it's a dynamic time column (t_*) we just created
                if (in_array($key, $fieldsInTable, true) || strpos($key, 't_') === 0) {
                    $clean[$key] = $val;
                }
            }
            return $clean;
        } catch (\Exception $e) {
            unset($data['audio_file_upload'], $data['custom_time_input'], $data['csrf_test_name']);
            return $data;
        }
    }
}
