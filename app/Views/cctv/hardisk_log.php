<?php
function roman($integer) {
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

function formatDisplayDate($dateStr) {
    if (!$dateStr || $dateStr === '-') return '-';
    $dateStr = trim($dateStr);
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateStr, $m)) {
        return "{$m[3]}/{$m[2]}/{$m[1]}";
    }
    if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $dateStr, $m)) {
        return "{$m[1]}/{$m[2]}/{$m[3]}";
    }
    return $dateStr;
}
?>
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        color: white;
        font-size: 12px;
        transition: background 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-add { background-color: #28a745; margin-bottom: 15px; }
    .btn-edit { background-color: #ffc107; color: #333; padding: 4px 8px; font-size: 10px; }
    .btn-delete { background-color: #dc3545; padding: 4px 8px; font-size: 10px; }
    .btn-bulk-delete { background-color: #dc3545; margin-bottom: 15px; margin-left: 10px; }
    
    .table-scroll-container {
        max-height: 75vh;
        overflow-x: auto;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 5px;
    }
    .ip-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1800px; /* Force scroll horizontally for slot records */
    }
    .ip-table th, .ip-table td {
        padding: 6px 10px;
        border: 1px solid #ddd;
        text-align: left;
        font-size: 12px;
    }
    .ip-table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 1;
        text-align: center;
    }
    .ip-table tr:hover {
        background-color: #f1f8ff;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 3% auto;
        padding: 25px;
        border: 1px solid #888;
        width: 95%;
        max-width: 1050px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close-btn:hover {
        color: black;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 220px 1fr;
        gap: 20px;
        align-items: start;
    }
    .form-group {
        margin-bottom: 12px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 12px;
        color: #333;
    }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 12px;
    }
    .form-submit-btn {
        background-color: #0077b6;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        width: 100%;
        margin-top: 10px;
    }
    .form-submit-btn:hover {
        background-color: #005f93;
    }
    
    .slots-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: nowrap;
        gap: 8px;
    }
    .slots-header h4 {
        margin: 0;
        color: #0077b6;
        white-space: nowrap;
    }
    /* Dynamic Slots Layout */
    .slots-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 8px;
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 4px;
        background: #fafafa;
        box-sizing: border-box;
    }
    .slot-card {
        background: #fff;
        border: 1px solid #dce3ec;
        border-radius: 6px;
        padding: 8px 10px;
        box-sizing: border-box;
    }
    .slot-card .slot-label {
        font-size: 11px;
        font-weight: bold;
        color: #0077b6;
        margin-bottom: 6px;
    }
    .slot-card .slot-row {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 4px;
    }
    .slot-card .slot-row label {
        font-size: 10px;
        color: #666;
        width: 36px;
        flex-shrink: 0;
    }
    .slot-card .slot-row input {
        flex: 1;
        padding: 3px 4px;
        font-size: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        min-width: 0;
    }
    
    @keyframes spin-record {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .recording-spinner {
        width: 12px;
        height: 12px;
        border: 2px solid rgba(40, 167, 69, 0.2);
        border-top-color: #28a745;
        border-right-color: #28a745;
        border-radius: 50%;
        display: inline-block;
        animation: spin-record 1s linear infinite;
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 10;
    }
    
    .hud-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 15, 30, 0.85);
        border: 1px solid #00f3ff;
        border-radius: 8px;
        color: #00f3ff;
        font-family: 'Courier New', Courier, monospace;
        font-size: 10px;
        padding: 8px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        z-index: 20;
        display: flex;
        flex-direction: column;
        justify-content: center;
        box-sizing: border-box;
        box-shadow: inset 0 0 10px rgba(0, 243, 255, 0.3);
    }
    .nvr-card-container:hover .hud-overlay {
        opacity: 1;
        visibility: visible;
    }
    .hud-overlay h5 {
        margin: 0 0 4px 0;
        font-size: 11px;
        color: #fff;
        border-bottom: 1px dashed #00f3ff;
        padding-bottom: 2px;
        text-transform: uppercase;
        text-shadow: 0 0 3px #00f3ff;
    }
    .hud-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }
    .hud-label {
        color: #7ab4b7;
    }
    .hud-value {
        font-weight: bold;
    }
    .scan-line {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 2px;
        background: rgba(0, 243, 255, 0.8);
        box-shadow: 0 0 4px #00f3ff;
        animation: scan 2s linear infinite;
        opacity: 0.6;
    }
    @keyframes scan {
        0% { top: 0; }
        100% { top: 100%; }
    }
</style>

<div class="right-frame">
        <h3 style="margin-top: 0;">Hardisk Log Replacement</h3>
        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">Catatan pergantian hardisk (HDD) per NVR beserta status rentang tanggal data rekaman.</p>

        <!-- Flash Messages -->
        <?php if(session()->getFlashdata('message')): ?>
            <div style="background:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:15px; font-size: 13px;">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:15px; font-size: 13px; text-align: left;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('cctv/hardisk-log/bulk-delete') ?>" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data log hardisk yang dipilih?');">
            
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
                <div>
                    <?php if ($page_perm !== 'R'): ?>
                    <button type="button" class="action-btn btn-add" style="margin-bottom: 0;" onclick="openAddModal()">➕ Tambah Log Device</button>
                    <button type="button" class="action-btn" style="background:#17a2b8; margin-bottom: 0; margin-left: 5px;" onclick="openModal('importModal')">📥 Import Excel</button>
                    <a href="<?= site_url('cctv/hardisk-log/downloadTemplate') ?>" class="action-btn" style="background:#28a745; margin-bottom: 0; text-decoration: none; margin-left: 5px;">⬇️ Download Template</a>
                    <a href="<?= site_url('cctv/hardisk-log/exportExcel') ?>?show_time=<?= $show_time ? '1' : '0' ?>&show_hdd=<?= $show_hdd ? '1' : '0' ?>" class="action-btn" style="background:#fd7e14; margin-bottom: 0; text-decoration: none; margin-left: 5px;">📤 Export Excel</a>
                    <button type="submit" class="action-btn btn-bulk-delete" style="margin-bottom: 0;" id="btnBulkDelete" disabled onclick="return confirm('Hapus keseluruhan baris NVR yang dipilih?');">🗑 Hapus NVR Terpilih</button>
                    <?php else: ?>
                    <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 0;">
                        <span>Mode <b>Read-Only</b>. Anda tidak dapat mengubah data Log Hardisk.</span>
                        <a href="<?= site_url('cctv/hardisk-log/exportExcel') ?>?show_time=<?= $show_time ? '1' : '0' ?>&show_hdd=<?= $show_hdd ? '1' : '0' ?>" class="action-btn" style="background:#fd7e14; text-decoration: none; margin-left: 15px;">📤 Export Excel</a>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; gap: 10px; align-items: center;">
                    <label style="margin: 0; font-size: 11px; font-weight: bold; color: #333; display: flex; align-items: center; gap: 4px; cursor: pointer; border: 1px solid #ccc; padding: 4px 8px; border-radius: 4px; background: #fff;">
                        <input type="checkbox" id="showHddCheck" <?= $show_hdd ? 'checked' : '' ?> onchange="window.location.href='<?= site_url('cctv/hardisk-log') ?>?show_time=<?= $show_time ? '1' : '0' ?>&show_hdd=' + (this.checked ? '1' : '0');" style="margin: 0;"> Show HDD
                    </label>
                    <label style="margin: 0; font-size: 11px; font-weight: bold; color: #333; display: flex; align-items: center; gap: 4px; cursor: pointer; border: 1px solid #ccc; padding: 4px 8px; border-radius: 4px; background: #fff;">
                        <input type="checkbox" id="showTimeCheck" <?= $show_time ? 'checked' : '' ?> onchange="window.location.href='<?= site_url('cctv/hardisk-log') ?>?show_hdd=<?= $show_hdd ? '1' : '0' ?>&show_time=' + (this.checked ? '1' : '0');" style="margin: 0;"> Show Time
                    </label>
                    <div style="width: 1px; height: 20px; background: #ccc; margin: 0 2px;"></div>
                    <label style="margin: 0; font-size: 12px; font-weight: bold; color: #555;">Urutkan:</label>
                    <select id="sortSelect" onchange="sortTable()" style="padding: 6px; border: 1px solid #ccc; border-radius: 4px; width: 180px; font-size: 12px; height: 32px; box-sizing: border-box;">
                        <option value="default">Default (No)</option>
                        <option value="device_asc">Device Name (A-Z)</option>
                        <option value="device_desc">Device Name (Z-A)</option>
                    </select>
                    <input type="text" id="searchInput" placeholder="Cari Device..." style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; width: 200px; font-size: 12px; height: 32px; box-sizing: border-box;" onkeyup="filterTable()">
                </div>
            </div>

            <div class="table-scroll-container">
                <table class="ip-table" id="dataTable">
                    <thead>
                        <tr>
                            <?php if ($page_perm !== 'R'): ?>
                            <th rowspan="2" style="width: 40px; vertical-align: middle;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                            <?php endif; ?>
                            <th rowspan="2" style="width: 50px; vertical-align: middle;">No</th>
                            <th rowspan="2" style="width: 150px; vertical-align: middle; text-align: left;">Device</th>
                            <th colspan="<?= $maxSlots ?>">START ~ END HDD RECORD</th>
                            <th rowspan="2" style="width: 100px; vertical-align: middle; text-align: center;">Start<br>Record</th>
                            <th rowspan="2" style="width: 100px; vertical-align: middle; text-align: center;">Last<br>Record</th>
                            <th rowspan="2" style="width: 100px; vertical-align: middle; text-align: center;">Start Time<br>Record</th>
                            <th rowspan="2" style="width: 100px; vertical-align: middle; text-align: center;">Last Time<br>Record</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: left;">Remark</th>
                        </tr>
                        <tr>
                            <?php 
                                $slotWidth = '125px';
                                if ($show_time && $show_hdd) $slotWidth = '185px';
                                elseif ($show_time) $slotWidth = '175px';
                                elseif ($show_hdd) $slotWidth = '145px';
                                
                                for ($s = 1; $s <= $maxSlots; $s++): 
                            ?>
                            <th style="width: <?= $slotWidth ?>; min-width: <?= $slotWidth ?>;"><?= roman($s) ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hddInventory = [];
                        $nvrColors = [
                            'NVR-A' => '#28a745', // Hijau
                            'NVR-B' => '#0077b6', // Biru
                            'NVR-C' => '#9c27b0', // Ungu
                            'NVR-D' => '#dc3545', // Merah
                            'NVR-E' => '#fd7e14', // Orange
                            'NVR-F' => '#6c757d', // Abu-abu
                            // Alternative names just in case
                            'NVR A' => '#28a745',
                            'NVR B' => '#0077b6',
                            'NVR C' => '#9c27b0',
                            'NVR D' => '#dc3545',
                            'NVR E' => '#fd7e14',
                            'NVR F' => '#6c757d',
                        ];
                        $colorPalette = ['#0077b6', '#2a9d8f', '#d62828', '#f77f00', '#003049', '#8338ec', '#ff006e', '#3a86ff', '#fb5607', '#ffbe0b'];
                        $colorIndex = 0;
                        if(!empty($logs)): 
                        ?>
                            <?php $no = 1; foreach($logs as $row): 
                                $nvrName = $row['device_name'];
                                if (!isset($nvrColors[$nvrName])) {
                                    $nvrColors[$nvrName] = $colorPalette[$colorIndex % count($colorPalette)];
                                    $colorIndex++;
                                }
                                $nvrColor = $nvrColors[$nvrName];
                            ?>
                                <tr>
                                    <?php if ($page_perm !== 'R'): ?>
                                    <td style="text-align: center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="checkItem" onclick="toggleBulkDeleteBtn()"></td>
                                    <?php endif; ?>
                                    <td style="text-align: center;"><?= $no++ ?></td>
                                    <td><strong><?= esc($row['device_name']) ?></strong></td>
                                    
                                    <?php for ($s = 0; $s < $maxSlots; $s++): ?>
                                    <td style="text-align: center; font-size: 11px;">
                                        <?php 
                                            $slotStrRaw = $row['slots'][$s] ?? '-';
                                            if ($slotStrRaw !== '-') {
                                                $records = explode('||', $slotStrRaw);
                                                $recCount = count($records);
                                                foreach ($records as $idx => $recStr) {
                                                    $hddNum = '';
                                                    if (strpos($recStr, '|HDD:') !== false) {
                                                        $parts = explode('|HDD:', $recStr);
                                                        $recStr = $parts[0];
                                                        $hddNum = $parts[1] ?? '';
                                                    }

                                                    $owDays = '';
                                                    if (strpos($recStr, '|OW:') !== false) {
                                                        $parts = explode('|OW:', $recStr);
                                                        $recStr = $parts[0];
                                                        $owDays = $parts[1] ?? '';
                                                    }

                                                    if ($hddNum !== '') {
                                                        $hdds = explode(',', $hddNum);
                                                        foreach ($hdds as $hNum) {
                                                            $hNum = trim($hNum);
                                                            $hddInfo = '';
                                                            if (strpos($hNum, '[') !== false && strpos($hNum, ']') !== false) {
                                                                preg_match('/\[(.*?)\]/', $hNum, $matches);
                                                                if (isset($matches[1])) $hddInfo = $matches[1];
                                                                $hNum = trim(preg_replace('/\[.*?\]/', '', $hNum));
                                                            }
                                                            $hNum = strtoupper(trim($hNum));
                                                            if (!empty($hNum)) {
                                                                $hddInventory[$hNum] = [
                                                                    'nvr' => $nvrName,
                                                                    'slot' => roman($s + 1), // Using roman function for tooltip consistency
                                                                    'date' => preg_replace('/\s+\d{1,2}:\d{2}(:\d{2})?/', '', $recStr), // Clean date for tooltip
                                                                    'ow' => $owDays,
                                                                    'color' => $nvrColor,
                                                                    'info' => $hddInfo
                                                                ];
                                                            }
                                                        }
                                                    }

                                                    if (!$show_time) {
                                                        $recStr = preg_replace('/\s+\d{1,2}:\d{2}(:\d{2})?/', '', $recStr);
                                                    }
                                                    
                                                    echo '<div class="record-cell-wrapper" style="position:relative; cursor:pointer; padding: 2px; border-radius: 4px; transition: background 0.2s; ' . ($idx < $recCount - 1 ? 'border-bottom: 1px dashed #ccc; padding-bottom: 6px; margin-bottom: 6px;' : '') . '" onclick="toggleRecordActions(event, this)" onmouseover="this.style.background=\'#f8f9fa\'" onmouseout="this.style.background=\'transparent\'">';
                                                    
                                                    // Tombol aksi di dalam sel (balloon)
                                                    if ($page_perm !== 'R') {
                                                        $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                                                        echo '<div class="record-actions-balloon" style="display:none; position:absolute; top: -10px; right: -5px; background:#fff; border:1px solid #ccc; box-shadow:0 4px 6px rgba(0,0,0,0.15); padding:4px; border-radius:6px; z-index:100; flex-direction:row; gap:4px;">';
                                                        echo "<button type='button' class='action-btn btn-edit' style='padding: 3px 6px; font-size: 10px; margin: 0; display:flex; align-items:center; border-radius: 3px;' onclick='event.stopPropagation(); if(confirm(\"Buka form Edit untuk NVR ini agar dapat mengubah data sel ini?\")) openEditModal($rowJson, $s)'>✎ Edit</button>";
                                                        echo "<a href='" . site_url('cctv/hardisk-log/delete-record/'.$row['id'].'/'.$s.'/'.$idx) . "' class='action-btn btn-delete' style='padding: 3px 6px; font-size: 10px; margin: 0; text-decoration:none; display:flex; align-items:center; border-radius: 3px;' onclick='event.stopPropagation(); return confirm(\"Hanya hapus record ini saja dari tabel?\");'>✖ Hapus</a>";
                                                        echo '</div>';
                                                    }
                                                    
                                                    echo esc($recStr);
                                                    
                                                    if ($show_hdd) {
                                                        if ($hddNum !== '') {
                                                            $formattedHdd = [];
                                                            foreach (explode(',', $hddNum) as $h) {
                                                                $h = trim($h);
                                                                if (strpos($h, '[') !== false && strpos($h, ']') !== false) {
                                                                    preg_match('/\[(.*?)\]/', $h, $matches);
                                                                    $info = isset($matches[1]) ? $matches[1] : '';
                                                                    $num = trim(preg_replace('/\[.*?\]/', '', $h));
                                                                    $formattedHdd[] = $num . ' <span style="font-weight:normal;">(' . esc($info) . ')</span>';
                                                                } else {
                                                                    $formattedHdd[] = $h;
                                                                }
                                                            }
                                                            echo '<br><span style="font-size:9px; color:#555; font-weight:bold; background:#e9ecef; padding: 2px 4px; border-radius: 3px; display:inline-block; margin-top:4px; border:1px solid #ced4da;">[HDD #'.implode(', ', $formattedHdd).']</span>';
                                                        }
                                                        if ($owDays !== '') {
                                                            echo '<br><span class="badge" style="background:#ffc107; color:#333; margin-top:2px; display:inline-block; font-size: 10px;">⚠️ Overwrite ('.esc($owDays).')</span>';
                                                        }
                                                    }
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                    <?php endfor; ?>
                                    
                                    <td><code><?= esc(formatDisplayDate($row['start_record'])) ?></code></td>
                                    <td><code><?= esc(formatDisplayDate($row['last_record'])) ?></code></td>
                                    <td><code><?= esc($row['start_time_record'] ?: '-') ?></code></td>
                                    <td><code><?= esc($row['last_time_record'] ?: '-') ?></code></td>
                                    <td><?= esc($row['remark'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= ($page_perm !== 'R') ? ($maxSlots + 8) : ($maxSlots + 7) ?>" style="text-align:center; padding: 20px;">Belum ada data Log Hardisk yang dikelola.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- NVR Images Layout -->
        <div style="margin-top: 30px; background: #fff; padding: 15px 20px; border-radius: 8px; border: 1px solid #dce3ec; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow-x: auto;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                <?php 
                $nvrIdx = 0;
                foreach ($nvrs as $nvr): 
                    $nvrName = $nvr['nama_nvr'];
                    // Hikvision for NVR A, Dahua for the rest
                    $isHikvision = (strpos(strtoupper($nvrName), 'NVR-A') !== false || strtoupper($nvrName) === 'NVR A');
                    $imgSrc = $isHikvision ? base_url('images/nvr_hikvision.jpg') : base_url('images/nvr_dahua.jpg');
                    $delay = $nvrIdx * 0.15;
                ?>
                <div class="nvr-card-container" style="position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #fff; padding: 20px 5px 5px 5px; border-radius: 8px; border: 1px solid #eaeaea; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; cursor: crosshair;">
                    <div style="position: absolute; top: 4px; left: 6px; display: flex; align-items: center; z-index: 10;">
                        <span style="color: #ff0000; font-weight: 800; font-size: 11px; letter-spacing: 0.5px;"><?= esc($nvrName) ?></span>
                    </div>
                    <span class="recording-spinner" title="Proses record berjalan" style="animation-delay: -<?= $delay ?>s;"></span>
                    <img src="<?= $imgSrc ?>" alt="<?= esc($nvrName) ?>" style="width: 100%; height: 50px; object-fit: contain; mix-blend-mode: multiply;">
                    
                    <!-- HUD Overlay -->
                    <div class="hud-overlay">
                        <div class="scan-line"></div>
                        <h5>SYS_DATA</h5>
                        <div class="hud-row"><span class="hud-label">IP:</span><span class="hud-value"><?= esc($nvr['ip_address'] ?? '192.168.x.x') ?></span></div>
                        <div class="hud-row"><span class="hud-label">TYPE:</span><span class="hud-value"><?= $isHikvision ? 'HKV-8B' : 'DH-UHD' ?></span></div>
                        <div class="hud-row"><span class="hud-label">STAT:</span><span class="hud-value" style="color:#4cff4c; text-shadow:0 0 3px #4cff4c;">ONLINE</span></div>
                    </div>
                </div>
                <?php 
                $nvrIdx++;
                endforeach; 
                ?>
            </div>
        </div>

        <!-- HDD Physical Inventory Representation -->
        <div style="margin-top: 15px; background: #fff; padding: 15px 20px; border-radius: 8px; border: 1px solid #dce3ec; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow-x: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h4 style="margin: 0; color: #333; display: flex; align-items: center; gap: 8px;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0077b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="12" x2="2" y2="12"></line>
                        <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                        <line x1="6" y1="16" x2="6.01" y2="16"></line>
                        <line x1="10" y1="16" x2="10.01" y2="16"></line>
                    </svg>
                    Inventaris Fisik Hardisk
                </h4>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                <?php 
                $localColorIdx = 0;
                foreach ($nvrs as $nvr): 
                    $nvrName = $nvr['nama_nvr'];
                    $spaceName = str_replace('NVR', 'HDD SPACE', $nvrName);
                    
                    if (isset($nvrColors[$nvrName])) {
                        $nvrColor = $nvrColors[$nvrName];
                    } else {
                        $nvrColor = $colorPalette[$localColorIdx % count($colorPalette)];
                        $localColorIdx++;
                    }
                    
                    $prefix = str_replace('NVR-', '', $nvrName);
                    $prefix = str_replace('NVR ', '', $prefix); // just in case
                ?>
                <div style="background: #fff; border: 1px solid <?= $nvrColor ?>40; border-top: 4px solid <?= $nvrColor ?>; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden;">
                    <div style="background: <?= $nvrColor ?>15; padding: 10px 15px; border-bottom: 1px solid <?= $nvrColor ?>30;">
                        <h4 style="margin: 0; color: <?= $nvrColor ?>; font-weight: bold; text-align: center; font-size: 14px;"><?= esc($spaceName) ?></h4>
                    </div>
                    <div style="padding: 12px; display: grid; grid-template-columns: repeat(auto-fill, minmax(65px, 1fr)); gap: 8px;">
                        <?php for ($i = 1; $i <= 30; $i++): 
                            $hddId = trim($prefix) . '-' . $i;
                            $hdd = $hddInventory[$hddId] ?? null;
                            $isActive = $hdd !== null;
                            
                            $bgColor = $isActive ? $nvrColor . '15' : '#f8f9fa';
                            $borderColor = $isActive ? $nvrColor : '#dee2e6';
                            $textColor = $isActive ? $nvrColor : '#6c757d';
                            $iconColor = $isActive ? $nvrColor : '#adb5bd';
                            
                            $tooltip = "HDD $hddId";
                            if ($isActive) {
                                $tooltip .= "\nNVR: {$hdd['nvr']}\nSlot: {$hdd['slot']}\nDate: {$hdd['date']}";
                                if ($hdd['ow']) {
                                    $tooltip .= "\nStatus: Overwrite ({$hdd['ow']})";
                                }
                                if (!empty($hdd['info'])) {
                                    $tooltip .= "\nInfo: {$hdd['info']}";
                                }
                            } else {
                                $tooltip .= "\n(Kosong / Belum Terpakai)";
                            }
                            
                            $badgeHtml = '';
                            if ($isActive) {
                                $shortNvr = str_replace(['NVR-', 'NVR '], '', $hdd['nvr']);
                                $badgeLabel = trim($shortNvr) . 'S-' . $hdd['slot'];
                                $badgeHtml = '<span style="position: absolute; top: 0; right: 0; background: ' . $nvrColor . '; color: #fff; font-size: 8px; padding: 1px 4px; border-bottom-left-radius: 4px; border-top-right-radius: 4px; font-weight: bold; line-height: 1.2;">' . esc($badgeLabel) . '</span>';
                            }
                        ?>
                            <div style="position: relative; background: <?= $bgColor ?>; border: 1px solid <?= $borderColor ?>; border-radius: 6px; padding: 6px; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.02); transition: transform 0.15s ease, box-shadow 0.15s ease; cursor: default;" title="<?= esc($tooltip) ?>" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='inset 0 -2px 0 rgba(0,0,0,0.02)';">
                                <?= $badgeHtml ?>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="<?= $iconColor ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 3px;">
                                    <line x1="22" y1="12" x2="2" y2="12"></line>
                                    <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                    <line x1="6" y1="16" x2="6.01" y2="16"></line>
                                    <line x1="10" y1="16" x2="10.01" y2="16"></line>
                                </svg>
                                <span style="font-size: 9px; font-weight: bold; color: <?= $textColor ?>; white-space: nowrap;"><?= esc($hddId) ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

</div>

<!-- Modal Tambah Data -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
        <h3>Tambah Log Pergantian Hardisk</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('cctv/hardisk-log/store') ?>" method="POST" id="addForm">
            <div class="form-grid">
                <div>
                    <h4 style="margin: 5px 0 10px 0; color:#0077b6;">Info Perangkat</h4>
                    <div class="form-group">
                        <label for="device_name">Device (NVR Name)</label>
                        <input type="text" id="device_name" name="device_name" required placeholder="Contoh: NVR - A" list="nvr_list" oninput="checkDeviceSelected()">
                        <datalist id="nvr_list">
                            <?php foreach($nvrs as $nvr): ?>
                                <option value="<?= esc($nvr['nama_nvr']) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="form-group" style="margin-top:10px;">
                        <label for="add_slot_selector">START ~ END HDD RECORD</label>
                        <select id="add_slot_selector" disabled onchange="filterSlots('add', this.value)" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="none" selected>Pilih Slot...</option>
                            <option value="all">Tampilkan Semua</option>
                            <?php for($i=1; $i<=30; $i++): ?>
                            <option value="<?= $i ?>">Space <?= roman($i) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="remark">Remark / Notes</label>
                        <textarea id="remark" name="remark" rows="4" placeholder="Keterangan tambahan..."></textarea>
                    </div>
                </div>
                
                <div style="min-width:0; overflow:hidden;">
                    <div class="slots-header">
                        <h4>Start ~ End HDD Record (Slot)</h4>
                    </div>
                    <div class="slots-container" id="add_slots_container">
                        <!-- Filled dynamically in JS -->
                    </div>
                </div>
            </div>
            <button type="submit" class="form-submit-btn">Simpan Log Hardisk</button>
        </form>
    </div>
</div>

<!-- Modal Edit Data -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit Log Pergantian Hardisk</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="" method="POST" id="editForm">
            <div class="form-grid">
                <div>
                    <h4 style="margin: 5px 0 10px 0; color:#0077b6;">Info Perangkat</h4>
                    <div class="form-group">
                        <label for="edit_device_name">Device (NVR Name)</label>
                        <input type="text" id="edit_device_name" name="device_name" required readonly style="background:#e9ecef; color:#555;">
                    </div>
                    <div class="form-group" style="margin-top:10px;">
                        <label for="edit_slot_selector">START ~ END HDD RECORD</label>
                        <select id="edit_slot_selector" disabled style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; background:#e9ecef; color:#555;">
                            <option value="all">Tampilkan Semua</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_remark">Periode of Use</label>
                        <textarea id="edit_remark" name="remark" rows="4" readonly style="background:#e9ecef; color:#555;"></textarea>
                    </div>
                </div>
                
                <div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <h4 style="margin: 0; color:#0077b6;">Start ~ End HDD Record (Slot)</h4>
                    </div>
                    <div class="slots-container" id="edit_slots_container">
                        <!-- Filled dynamically in JS -->
                    </div>
                </div>
            </div>
            <button type="submit" class="form-submit-btn">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- Modal HDD Info -->
<div id="hddInfoModal" class="modal" style="z-index: 1050;">
    <div class="modal-content" style="width:400px;">
        <span class="close-btn" onclick="closeModal('hddInfoModal')">&times;</span>
        <h3 id="hddInfoModalTitle">Keterangan HDD</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        <input type="hidden" id="hddInfo_prefix">
        <input type="hidden" id="hddInfo_slotIdx">
        <input type="hidden" id="hddInfo_recId">
        <input type="hidden" id="hddInfo_val">
        
        <div class="form-group">
            <label for="hddInfo_start">Start</label>
            <input type="datetime-local" step="1" id="hddInfo_start">
        </div>
        <div class="form-group" style="margin-top:10px;">
            <label for="hddInfo_end">End</label>
            <input type="datetime-local" step="1" id="hddInfo_end">
        </div>
        
        <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn" style="background:#dc3545; color:#fff;" onclick="clearHddInfo()">Hapus Info</button>
            <button type="button" class="btn" style="background:#28a745; color:#fff;" onclick="saveHddInfo()">Simpan Info</button>
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="importModal" class="modal">
    <div class="modal-content" style="width:500px;">
        <span class="close-btn" onclick="closeModal('importModal')">&times;</span>
        <h3>Import Data via Excel</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('cctv/hardisk-log/importExcel') ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file_excel">Pilih File Excel (.xls, .xlsx, .csv)</label>
                <input type="file" id="file_excel" name="file_excel" accept=".xls,.xlsx,.csv" required style="border:none; padding:10px 0;">
            </div>
            <p style="font-size: 11px; color: #856404; background: #fff3cd; padding: 8px; border-radius: 4px; margin-bottom:15px; line-height:1.4;">
                ⚠️ <strong>Perhatian:</strong> Urutan kolom impor harus berupa (Device, Slot 1 - X, dst..., Start Record, Last Record, Remark). Berkas template kami menggunakan 8 slot standard. Kolom kosong akan diisi dengan '-'.
            </p>
            <button type="submit" class="form-submit-btn" style="background-color: #17a2b8;">Mulai Proses Import</button>
        </form>
    </div>
</div>

<?php
$usedSlotsByDevice = [];
if (!empty($logs)) {
    foreach ($logs as $log) {
        $dev = $log['device_name'];
        if (!isset($usedSlotsByDevice[$dev])) {
            $usedSlotsByDevice[$dev] = [];
        }
        $slotsArr = json_decode($log['slots_json'], true) ?: [];
        foreach ($slotsArr as $idx => $val) {
            if (!empty($val) && $val !== '-') {
                if (!in_array($idx + 1, $usedSlotsByDevice[$dev])) {
                    $usedSlotsByDevice[$dev][] = $idx + 1;
                }
            }
        }
    }
}
?>
<script>
    const usedHdds = <?= json_encode(array_keys($hddInventory)) ?>;
    const usedSlotsByDevice = <?= json_encode($usedSlotsByDevice) ?>;

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.record-cell-wrapper')) {
            document.querySelectorAll('.record-actions-balloon').forEach(el => el.style.display = 'none');
        }
    });

    function toggleRecordActions(e, wrapper) {
        // Hide all others first
        document.querySelectorAll('.record-actions-balloon').forEach(el => {
            if (el.parentNode !== wrapper) el.style.display = 'none';
        });
        
        // Toggle current
        let balloon = wrapper.querySelector('.record-actions-balloon');
        if (balloon) {
            balloon.style.display = balloon.style.display === 'none' ? 'flex' : 'none';
        }
    }

    function openModal(id) {
        document.getElementById(id).style.display = "block";
    }
    
    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    // Parse date into YYYY-MM-DDTHH:MM
    function parseDmy(dateStr) {
        if (!dateStr || dateStr === '-') return '';
        let parts = dateStr.trim().split(' ');
        let datePart = parts[0];
        let timePart = parts[1] || '';
        
        let delim = datePart.includes('/') ? '/' : (datePart.includes('-') ? '-' : '');
        if (!delim) return '';

        let dParts = datePart.split(delim);
        if (dParts.length === 3) {
            let y, m, d;
            if (dParts[0].length === 4) {
                // YYYY-MM-DD
                y = dParts[0]; m = dParts[1]; d = dParts[2];
            } else {
                // DD-MM-YYYY
                d = dParts[0]; m = dParts[1]; y = dParts[2];
            }
            let ymd = `${y}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`;
            
            // Format timePart to match HH:MM:SS
            if (timePart) {
                let tParts = timePart.split(':');
                if (tParts.length >= 2) {
                    timePart = `${tParts[0].padStart(2, '0')}:${tParts[1].padStart(2, '0')}`;
                    if (tParts.length >= 3) timePart += `:${tParts[2].padStart(2, '0')}`;
                }
                return `${ymd}T${timePart}`;
            }
            return ymd;
        }
        return '';
    }

    // Format YYYY-MM-DDTHH:MM into DD/MM/YYYY HH:MM
    function formatDmy(ymdTStr) {
        if (!ymdTStr) return '';
        let parts = ymdTStr.split('T');
        let ymdPart = parts[0];
        let timePart = parts[1] || '';
        
        let dParts = ymdPart.split('-');
        if (dParts.length === 3) {
            let dmy = `${dParts[2]}/${dParts[1]}/${dParts[0]}`;
            if (timePart) return `${dmy} ${timePart}`;
            return dmy;
        }
        return '';
    }

    function calcDurationRemark(prefix) {
        let minDate = null;
        let maxDate = null;
        
        let startIdx = 1;
        let endIdx = 30;
        
        if (prefix === 'edit' && window.activeEditSlotIdx !== null) {
            startIdx = window.activeEditSlotIdx + 1;
            endIdx = window.activeEditSlotIdx + 1;
        }
        
        for(let i=startIdx; i<=endIdx; i++) {
            const hidden = document.getElementById(`${prefix}_slot_combined_${i}`);
            if(hidden && hidden.value) {
                const records = hidden.value.split('||');
                records.forEach(r => {
                    let mainStr = r;
                    if(mainStr.includes('|HDD:')) mainStr = mainStr.split('|HDD:')[0];
                    if(mainStr.includes('|OW:')) mainStr = mainStr.split('|OW:')[0];
                    const parts = mainStr.split('-');
                    
                    if(parts[0]) {
                        const sd = new Date(parseDmy(parts[0].trim()));
                        if(!isNaN(sd)) {
                            if(!minDate || sd < minDate) minDate = sd;
                            if(!maxDate || sd > maxDate) maxDate = sd;
                        }
                    }
                    if(parts[1]) {
                        const ed = new Date(parseDmy(parts[1].trim()));
                        if(!isNaN(ed)) {
                            if(!minDate || ed < minDate) minDate = ed;
                            if(!maxDate || ed > maxDate) maxDate = ed;
                        }
                    }
                });
            }
        }
        
        if(!minDate || !maxDate || maxDate < minDate) {
            const remarkEl = document.getElementById(prefix === 'add' ? 'remark' : 'edit_remark');
            if (remarkEl) remarkEl.value = '0 days';
            return;
        }
        
        let startDate = minDate;
        let endDate = maxDate;
        
        let y = endDate.getFullYear() - startDate.getFullYear();
        let m = endDate.getMonth() - startDate.getMonth();
        let d = endDate.getDate() - startDate.getDate();

        if (d < 0) {
            m--;
            const prevMonth = new Date(endDate.getFullYear(), endDate.getMonth(), 0);
            d += prevMonth.getDate();
        }
        if (m < 0) { y--; m += 12; }

        let parts = [];
        if (y > 0) parts.push(`${y} ${y === 1 ? 'year' : 'years'}`);
        if (m > 0) parts.push(`${m} ${m === 1 ? 'month' : 'months'}`);
        if (d > 0) {
            const weeks = Math.floor(d / 7);
            const remDays = d % 7;
            if (weeks > 0) parts.push(`${weeks} ${weeks === 1 ? 'week' : 'weeks'}`);
            if (remDays > 0) parts.push(`${remDays} ${remDays === 1 ? 'day' : 'days'}`);
        }

        const durationStr = parts.length > 0 ? parts.join(' ') : '0 days';

        const remarkEl = document.getElementById(prefix === 'add' ? 'remark' : 'edit_remark');
        if (remarkEl) remarkEl.value = durationStr;
    }

    function toRoman(num) {
        const lookup = {M:1000,CM:900,D:500,CD:400,C:100,XC:90,L:50,XL:40,X:10,IX:9,V:5,IV:4,I:1};
        let roman = '';
        for (let i in lookup) {
            while (num >= lookup[i]) {
                roman += i;
                num -= lookup[i];
            }
        }
        return roman;
    }

    function filterSlots(prefix, val) {
        let usedSlots = [];
        if (prefix === 'add') {
            const devInput = document.getElementById('device_name').value.trim();
            if (devInput !== '') {
                usedSlots = usedSlotsByDevice[devInput] || [];
            }
        }
        
        for (let i = 1; i <= 30; i++) {
            const section = document.getElementById(`${prefix}_slot_section_${i}`);
            if (section) {
                if (prefix === 'add' && usedSlots.includes(i)) {
                    section.style.display = 'none';
                    continue;
                }
                
                if (val === 'none') {
                    section.style.display = 'none';
                } else if (val === 'all' || val == i) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            }
        }
    }
    
    function checkDeviceSelected() {
        const devInput = document.getElementById('device_name').value.trim();
        const selector = document.getElementById('add_slot_selector');
        
        let currentDev = document.getElementById('device_name').getAttribute('data-last-dev') || '';
        
        if (devInput !== '') {
            selector.disabled = false;
            
            if (currentDev !== devInput) {
                const container = document.getElementById('add_slots_container');
                container.innerHTML = '';
                for (let i = 1; i <= 30; i++) {
                    let section = createSlotSection('add', i, []);
                    section.style.display = 'none';
                    container.appendChild(section);
                    addRecordRow('add', i, '');
                }
                document.getElementById('device_name').setAttribute('data-last-dev', devInput);
            }
            
            const usedSlots = usedSlotsByDevice[devInput] || [];
            let oldVal = selector.value;
            let optionsHtml = '<option value="none">Pilih Slot...</option><option value="all">Tampilkan Semua</option>';
            
            let maxUsedSlot = 0;
            if (usedSlots.length > 0) {
                maxUsedSlot = Math.max(...usedSlots);
            }
            let targetLimit = maxUsedSlot + 1;
            if (targetLimit > 30) targetLimit = 30;
            
            for(let i = 1; i <= targetLimit; i++) {
                if (!usedSlots.includes(i)) {
                    optionsHtml += `<option value="${i}">Space ${toRoman(i)}</option>`;
                }
            }
            selector.innerHTML = optionsHtml;
            
            let optExists = Array.from(selector.options).some(o => o.value === oldVal);
            if (optExists && oldVal !== 'none') {
                selector.value = oldVal;
            } else {
                selector.value = 'none';
                filterSlots('add', 'none');
            }
        } else {
            selector.disabled = true;
            selector.value = 'none';
            document.getElementById('device_name').setAttribute('data-last-dev', '');
            filterSlots('add', 'none');
        }
    }

    function createSlotSection(prefix, slotIdx, recordsArray = []) {
        const section = document.createElement('div');
        section.className = 'slot-card'; 
        section.id = `${prefix}_slot_section_${slotIdx}`;
        section.style.marginBottom = '15px';
        
        let combinedVal = recordsArray.join('||');
        
        section.innerHTML = `
            <div class="slot-label" style="display:flex; justify-content:space-between; align-items:center;">
                <span>Space ${toRoman(slotIdx)}</span>
            </div>
            <div id="${prefix}_records_wrapper_${slotIdx}">
                <!-- Records injected here by JS loop -->
            </div>
            <input type="hidden" name="slots[]" id="${prefix}_slot_combined_${slotIdx}" value="${combinedVal}">
        `;
        
        return section;
    }
    
    let recordCounter = 0;
    
    function addRecordRow(prefix, slotIdx, recordStr = '') {
        const wrapper = document.getElementById(`${prefix}_records_wrapper_${slotIdx}`);
        if (!wrapper) return;
        
        recordCounter++;
        const recId = recordCounter;
        
        let startDmy = '', endDmy = '', overwriteDays = '', hddNum = '';
        if (recordStr && recordStr !== '-') {
            let mainStr = recordStr;
            
            if (mainStr.includes('|HDD:')) {
                const parts = mainStr.split('|HDD:');
                mainStr = parts[0];
                hddNum = parts[1] || '';
            }
            if (mainStr.includes('|OW:')) {
                const parts = mainStr.split('|OW:');
                mainStr = parts[0];
                overwriteDays = parts[1] || '';
            }
            const parts = mainStr.split('-');
            if (parts.length >= 1) startDmy = parts[0].trim();
            if (parts.length >= 2) {
                endDmy = parts.slice(1).join('-').trim();
            }
        }
        
        let hddNums = [];
        let hddInfos = {};
        if (hddNum) {
            hddNum.split(',').forEach(n => {
                let hStr = n.trim();
                let info = '';
                if (hStr.includes('[') && hStr.includes(']')) {
                    const match = hStr.match(/\[(.*?)\]/);
                    if (match) {
                        info = match[1];
                        hStr = hStr.replace(/\[.*?\]/, '');
                    }
                }
                hddNums.push(hStr);
                if (info) hddInfos[hStr] = info;
            });
        }
        
        const startYmd = parseDmy(startDmy);
        const endYmd = parseDmy(endDmy);
        const isOwChecked = overwriteDays ? 'checked' : '';
        const owStyle = overwriteDays ? 'display: block;' : 'display: none;';
        
        const recDiv = document.createElement('div');
        recDiv.className = 'record-row-item';
        recDiv.id = `${prefix}_rec_${recId}`;
        recDiv.style.cssText = "border-top: 1px dashed #ccc; margin-top: 10px; padding-top: 5px;";
        
        recDiv.innerHTML = `
            <div style="display: flex; gap: 15px; width: 100%;">
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 4px;">
                        <button type="button" onclick="removeRecordRow('${prefix}', ${slotIdx}, ${recId})" style="background:none; border:none; color:red; cursor:pointer; font-size:16px; padding:0; line-height:1;" title="Hapus Record">&times;</button>
                    </div>
                    <div class="slot-row">
                        <label>Start</label>
                        <input type="datetime-local" step="1" id="${prefix}_rec_start_${recId}" value="${startYmd}" onchange="updateAllRecordsInSlot('${prefix}', ${slotIdx})">
                    </div>
                    <div class="slot-row">
                        <label>End</label>
                        <input type="datetime-local" step="1" id="${prefix}_rec_end_${recId}" value="${endYmd}" onchange="updateAllRecordsInSlot('${prefix}', ${slotIdx})">
                    </div>
                    <div class="slot-row" style="margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 5px; font-size: 11px; cursor: pointer; color: #d39e00; margin: 0;">
                            <input type="checkbox" id="${prefix}_rec_ow_check_${recId}" ${isOwChecked} onchange="toggleRecOw('${prefix}', ${recId}); updateAllRecordsInSlot('${prefix}', ${slotIdx})"> Terjadi Overwrite
                        </label>
                    </div>
                    <div id="${prefix}_rec_ow_container_${recId}" style="${owStyle} margin-top: 5px;">
                        <input type="text" id="${prefix}_rec_ow_val_${recId}" value="${overwriteDays}" placeholder="Berapa hari? Misal: 3 Hari" style="width: 100%; font-size: 11px; padding: 4px;" onkeyup="updateAllRecordsInSlot('${prefix}', ${slotIdx})">
                    </div>
                    <div id="${prefix}_rec_hdd_info_summary_${recId}" style="margin-top: 10px; font-size: 10px; color: #555; background: #f8f9fa; padding: 5px; border-radius: 4px; border: 1px solid #e9ecef; min-height: 20px;">
                        <!-- Summary injected via JS -->
                    </div>
                </div>
                
                <div style="width: 120px; display: flex; flex-direction: column;">
                    <label style="font-size: 11px; color: #0077b6; font-weight:bold; margin: 0 0 5px 0;">Hardisk Usage</label>
                    <div style="flex: 1; height: 120px; overflow-y: auto; border: 1px solid #ccc; border-radius: 4px; padding: 5px; background: #fff;" id="${prefix}_rec_hdd_list_${recId}">
                        ${(() => {
                            let devName = '';
                            if (prefix === 'add') {
                                devName = document.getElementById('device_name').value.trim();
                            } else {
                                devName = document.getElementById('edit_device_name').value.trim();
                            }
                            let hddPrefix = devName.replace('NVR-', '').replace('NVR ', '').trim();
                            
                            return Array.from({length: 30}, (_, i) => {
                                const num = i + 1;
                                const val = hddPrefix ? (hddPrefix + '-' + num) : num.toString();
                                const isChecked = hddNums.includes(val) ? 'checked' : '';
                                const isUsed = usedHdds.includes(val) && !hddNums.includes(val);
                                const disabledAttr = isUsed ? 'disabled' : '';
                                const labelColor = isUsed ? '#ccc' : '#333';
                                const cursorStyle = isUsed ? 'not-allowed' : 'pointer';
                                
                                const infoStr = hddInfos[val] || '';
                                const linkText = infoStr !== '' ? 'edit info' : 'add info';
                                const linkBold = infoStr !== '' ? 'font-weight:bold;' : '';
                                
                                return `
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2px;">
                                    <label style="font-size:10px; cursor:${cursorStyle}; color:${labelColor}; margin:0; display:flex; align-items:center; gap:3px;">
                                        <input type="checkbox" class="${prefix}_rec_hdd_check_${recId}" value="${val}" ${isChecked} ${disabledAttr} data-info="${infoStr}" onchange="var el = document.getElementById('link_hdd_info_${prefix}_${recId}_${val}'); if(el) el.style.display = this.checked ? 'inline' : 'none'; updateAllRecordsInSlot('${prefix}', ${slotIdx})"> HDD ${val}
                                    </label>
                                    ${!isUsed ? '<a href="#" id="link_hdd_info_' + prefix + '_' + recId + '_' + val + '" onclick="openHddInfo(\'' + prefix + '\', ' + slotIdx + ', ' + recId + ', \'' + val + '\'); return false;" style="font-size:9px; color:#0077b6; text-decoration:none; ' + linkBold + ' display:' + (isChecked ? 'inline' : 'none') + ';">' + linkText + '</a>' : ''}
                                </div>`;
                            }).join('');
                        })()}
                    </div>
                </div>
            </div>
        `;
        
        wrapper.appendChild(recDiv);
        updateAllRecordsInSlot(prefix, slotIdx);
    }
    
    function removeRecordRow(prefix, slotIdx, recId) {
        const el = document.getElementById(`${prefix}_rec_${recId}`);
        if (el) {
            el.remove();
            updateAllRecordsInSlot(prefix, slotIdx);
        }
    }
    
    function toggleRecOw(prefix, recId) {
        const chk = document.getElementById(`${prefix}_rec_ow_check_${recId}`);
        const cont = document.getElementById(`${prefix}_rec_ow_container_${recId}`);
        const valInput = document.getElementById(`${prefix}_rec_ow_val_${recId}`);
        if (chk && chk.checked) {
            cont.style.display = 'block';
        } else {
            cont.style.display = 'none';
            if (valInput) valInput.value = '';
        }
    }
    
    function updateAllRecordsInSlot(prefix, slotIdx) {
        const wrapper = document.getElementById(`${prefix}_records_wrapper_${slotIdx}`);
        if (!wrapper) return;
        
        const recDivs = wrapper.querySelectorAll('.record-row-item');
        let records = [];
        
        recDivs.forEach(div => {
            const recId = div.id.replace(`${prefix}_rec_`, '');
            const startVal = document.getElementById(`${prefix}_rec_start_${recId}`).value;
            const endVal = document.getElementById(`${prefix}_rec_end_${recId}`).value;
            const owCheck = document.getElementById(`${prefix}_rec_ow_check_${recId}`);
            const owVal = document.getElementById(`${prefix}_rec_ow_val_${recId}`);
            const hddCheckboxes = div.querySelectorAll(`.${prefix}_rec_hdd_check_${recId}:checked`);
            let hddSelected = [];
            let summaryHtml = '';
            
            hddCheckboxes.forEach(cb => {
                let info = cb.getAttribute('data-info');
                if (info && info.trim() !== '') {
                    hddSelected.push(`${cb.value}[${info.trim()}]`);
                    summaryHtml += `<div style="margin-bottom: 2px;"><span style="font-weight:bold; color:#0077b6;">[HDD ${cb.value}]</span> <span style="color:#333;">${info.trim()}</span></div>`;
                } else {
                    hddSelected.push(cb.value);
                    summaryHtml += `<div style="margin-bottom: 2px;"><span style="font-weight:bold; color:#0077b6;">[HDD ${cb.value}]</span></div>`;
                }
            });
            
            const summaryContainer = document.getElementById(`${prefix}_rec_hdd_info_summary_${recId}`);
            if (summaryContainer) {
                summaryContainer.innerHTML = summaryHtml || '<em>Belum ada HDD terpilih</em>';
            }
            
            if (startVal || endVal) {
                let combined = `${formatDmy(startVal)}-${formatDmy(endVal)}`;
                
                if (owCheck && owCheck.checked) {
                    const owStr = (owVal ? owVal.value : '').trim();
                    if (owStr !== '') {
                        combined += `|OW:${owStr}`;
                    }
                }
                
                if (hddSelected.length > 0) {
                    combined += `|HDD:${hddSelected.join(',')}`;
                }
                
                records.push(combined);
            }
        });
        
        document.getElementById(`${prefix}_slot_combined_${slotIdx}`).value = records.join('||');
        calcDurationRemark(prefix);
    }

    function openAddModal() {
        document.getElementById('addForm').reset();
        document.getElementById('add_slot_selector').value = 'none';
        document.getElementById('add_slot_selector').disabled = true;
        document.getElementById('device_name').setAttribute('data-last-dev', '');
        
        const container = document.getElementById('add_slots_container');
        container.innerHTML = '';
        for (let i = 1; i <= 30; i++) {
            let section = createSlotSection('add', i, []);
            section.style.display = 'none'; // Hide initially
            container.appendChild(section);
            addRecordRow('add', i, ''); // Add one empty record row by default
        }
        openModal('addModal');
    }

    function openEditModal(data, specificSlotIdx = null) {
        window.activeEditSlotIdx = specificSlotIdx;
        document.getElementById('editForm').action = "<?= site_url('cctv/hardisk-log/update/') ?>" + data.id;
        document.getElementById('edit_device_name').value = data.device_name;
        document.getElementById('edit_remark').value = data.remark || '';
        
        const slotSelector = document.getElementById('edit_slot_selector');
        if (specificSlotIdx !== null) {
            slotSelector.innerHTML = `<option value="${specificSlotIdx + 1}">Space ${toRoman(specificSlotIdx + 1)}</option>`;
        } else {
            slotSelector.innerHTML = `<option value="all">Tampilkan Semua</option>`;
        }
        
        const slots = data.slots || [];
        const container = document.getElementById('edit_slots_container');
        container.innerHTML = '';
        
        for (let i = 1; i <= 30; i++) {
            let slotVal = slots[i - 1] || '';
            let records = [];
            if (slotVal && slotVal !== '-') {
                records = slotVal.split('||');
            }
            
            let section = createSlotSection('edit', i, records);
            container.appendChild(section);
            
            if (records.length > 0) {
                records.forEach(rec => addRecordRow('edit', i, rec));
                if (specificSlotIdx !== null) {
                    section.style.display = (i === (specificSlotIdx + 1)) ? 'inline-block' : 'none';
                } else {
                    section.style.display = 'inline-block';
                }
            } else {
                addRecordRow('edit', i, ''); // Add empty row if no records
                section.style.display = 'none'; // Hide empty spaces by default
            }
        }
        
        calcDurationRemark('edit');
        openModal('editModal');
    }

    function convertDateToYmd(dateStr) {
        if (!dateStr || dateStr === '-') return '';
        const parts = dateStr.split('/');
        if (parts.length === 3) {
            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }
        if (dateStr.includes('-')) {
            const partsYmd = dateStr.split('-');
            if (partsYmd.length === 3 && partsYmd[0].length === 4) {
                return dateStr;
            }
        }
        return '';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.checkItem');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        toggleBulkDeleteBtn();
    }

    function openHddInfo(prefix, slotIdx, recId, val) {
        const cb = document.querySelector(`.${prefix}_rec_hdd_check_${recId}[value="${val}"]`);
        if (!cb) return;
        
        let existing = cb.getAttribute('data-info') || '';
        let startDmy = '', endDmy = '';
        if (existing) {
            let parts = existing.split('-');
            if (parts.length >= 1) startDmy = parts[0].trim();
            if (parts.length >= 2) endDmy = parts.slice(1).join('-').trim();
        }
        
        document.getElementById('hddInfoModalTitle').innerText = `Keterangan HDD ${val}`;
        document.getElementById('hddInfo_prefix').value = prefix;
        document.getElementById('hddInfo_slotIdx').value = slotIdx;
        document.getElementById('hddInfo_recId').value = recId;
        document.getElementById('hddInfo_val').value = val;
        
        document.getElementById('hddInfo_start').value = parseDmy(startDmy);
        document.getElementById('hddInfo_end').value = parseDmy(endDmy);
        
        openModal('hddInfoModal');
    }

    function saveHddInfo() {
        const prefix = document.getElementById('hddInfo_prefix').value;
        const slotIdx = document.getElementById('hddInfo_slotIdx').value;
        const recId = document.getElementById('hddInfo_recId').value;
        const val = document.getElementById('hddInfo_val').value;
        
        const startVal = document.getElementById('hddInfo_start').value;
        const endVal = document.getElementById('hddInfo_end').value;
        
        let res = '';
        if (startVal || endVal) {
            res = `${formatDmy(startVal)} - ${formatDmy(endVal)}`;
        }
        
        applyHddInfo(prefix, slotIdx, recId, val, res);
        closeModal('hddInfoModal');
    }

    function clearHddInfo() {
        const prefix = document.getElementById('hddInfo_prefix').value;
        const slotIdx = document.getElementById('hddInfo_slotIdx').value;
        const recId = document.getElementById('hddInfo_recId').value;
        const val = document.getElementById('hddInfo_val').value;
        
        applyHddInfo(prefix, slotIdx, recId, val, '');
        closeModal('hddInfoModal');
    }

    function applyHddInfo(prefix, slotIdx, recId, val, res) {
        const cb = document.querySelector(`.${prefix}_rec_hdd_check_${recId}[value="${val}"]`);
        if (cb) {
            cb.setAttribute('data-info', res.trim());
            const link = document.getElementById(`link_hdd_info_${prefix}_${recId}_${val}`);
            if (link) {
                link.style.fontWeight = res.trim() !== '' ? 'bold' : 'normal';
                link.innerText = res.trim() !== '' ? 'edit info' : 'add info';
            }
            updateAllRecordsInSlot(prefix, slotIdx);
        }
    }

    document.querySelectorAll('.checkItem').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectAll = document.getElementById('selectAll');
            const total = document.querySelectorAll('.checkItem').length;
            const checked = document.querySelectorAll('.checkItem:checked').length;
            if (selectAll) {
                selectAll.checked = (total === checked);
            }
            toggleBulkDeleteBtn();
        });
    });

    function toggleBulkDeleteBtn() {
        const checked = document.querySelectorAll('.checkItem:checked');
        const btn = document.getElementById('btnBulkDelete');
        if (btn) btn.disabled = checked.length === 0;
    }

    function filterTable() {
        const input = document.getElementById("searchInput").value.toLowerCase();
        const trs = document.querySelector("#dataTable tbody").getElementsByTagName("tr");

        for (let i = 0; i < trs.length; i++) {
            let text = trs[i].textContent.toLowerCase();
            if (trs[i].cells.length <= 2) continue; // Skip placeholder
            if (text.includes(input)) {
                trs[i].style.display = "";
            } else {
                trs[i].style.display = "none";
            }
        }
    }

    function sortTable() {
        const select = document.getElementById("sortSelect");
        const val = select.value;
        const tbody = document.querySelector("#dataTable tbody");
        const trs = Array.from(tbody.querySelectorAll("tr"));
        
        if (trs.length === 0 || trs[0].cells.length <= 2) return;

        trs.sort((a, b) => {
            const isReadOnly = <?= ($page_perm === 'R') ? 'true' : 'false' ?>;
            const noIndex = isReadOnly ? 0 : 1;
            const deviceIndex = isReadOnly ? 1 : 2;
            
            let valA = '';
            let valB = '';
            
            if (val === 'default') {
                valA = parseInt(a.cells[noIndex].textContent);
                valB = parseInt(b.cells[noIndex].textContent);
                return valA - valB;
            } else if (val === 'device_asc') {
                valA = a.cells[deviceIndex].textContent.trim().toLowerCase();
                valB = b.cells[deviceIndex].textContent.trim().toLowerCase();
                return valA.localeCompare(valB);
            } else if (val === 'device_desc') {
                valA = a.cells[deviceIndex].textContent.trim().toLowerCase();
                valB = b.cells[deviceIndex].textContent.trim().toLowerCase();
                return valB.localeCompare(valA);
            }
            return 0;
        });
        
        tbody.innerHTML = '';
        trs.forEach(tr => tbody.appendChild(tr));
    }
</script>

<?= $this->endSection() ?>
