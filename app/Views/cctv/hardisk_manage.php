<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<style>
    .right-frame {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        color: white;
        transition: opacity 0.2s;
        font-size: 12px;
    }
    .action-btn:hover {
        opacity: 0.8;
    }
    
    .modal {
        display: none; 
        position: fixed; 
        z-index: 9999; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5); 
    }
    .modal-content {
        background-color: #fff;
        margin: 5% auto; 
        padding: 0;
        border: 1px solid #888;
        width: 500px; 
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    .modal-header {
        background-color: #f1f1f1;
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-header h3 {
        margin: 0;
        font-size: 16px;
        color: #333;
    }
    .close {
        color: #aaa;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover {
        color: #000;
    }
    .modal-body {
        padding: 20px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 13px;
        box-sizing: border-box;
    }
    .modal-footer {
        padding: 15px 20px;
        background-color: #f9f9f9;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-save { background-color: #28a745; }
    .btn-cancel { background-color: #6c757d; }
</style>

<div class="right-frame">
    <h3 style="margin-top: 0;">Hardisk Manage</h3>
    <p style="color: #666; font-size: 13px; margin-bottom: 20px;">Kelola informasi detail kapasitas, merk, dan status fisik hardisk untuk setiap NVR.</p>

    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('message')): ?>
        <div style="background:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:15px; font-size: 13px;">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:15px; font-size: 13px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
        <?php 
        $nvrColors = [
            'NVR-A' => '#28a745', 'NVR-B' => '#0077b6', 'NVR-C' => '#ffc107',
            'NVR-D' => '#dc3545', 'NVR-E' => '#fd7e14', 'NVR-F' => '#6c757d',
            'NVR A' => '#28a745', 'NVR B' => '#0077b6', 'NVR C' => '#ffc107',
            'NVR D' => '#dc3545', 'NVR E' => '#fd7e14', 'NVR F' => '#6c757d',
        ];
        $colorPalette = ['#0077b6', '#2a9d8f', '#d62828', '#f77f00', '#003049', '#8338ec', '#ff006e'];
        $localColorIdx = 0;
        
        foreach ($nvrs as $nvr): 
            $nvrName = $nvr['nama_nvr'];
            $spaceName = str_replace('NVR', 'HDD SPACE', $nvrName);
            $nvrColor = $nvrColors[$nvrName] ?? $colorPalette[$localColorIdx++ % count($colorPalette)];
            $prefix = str_replace(['NVR-', 'NVR '], '', $nvrName);
        ?>
        <div style="background: #fff; border: 1px solid <?= $nvrColor ?>40; border-top: 4px solid <?= $nvrColor ?>; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden;">
            <div style="background: <?= $nvrColor ?>15; padding: 10px 15px; border-bottom: 1px solid <?= $nvrColor ?>30;">
                <h4 style="margin: 0; color: <?= $nvrColor ?>; font-weight: bold; text-align: center; font-size: 14px;"><?= esc($spaceName) ?></h4>
            </div>
            <div style="padding: 12px; display: grid; grid-template-columns: repeat(auto-fill, minmax(75px, 1fr)); gap: 8px;">
                <?php for ($i = 1; $i <= 30; $i++): 
                    $hddLabel = trim($prefix) . '-' . $i;
                    $hddData = $hddMap[$nvrName][$hddLabel] ?? null;
                    
                    $hasData = $hddData !== null;
                    $bgColor = $hasData ? $nvrColor . '15' : '#f8f9fa';
                    $borderColor = $hasData ? $nvrColor : '#dee2e6';
                    $textColor = $hasData ? $nvrColor : '#6c757d';
                    $iconColor = $hasData ? $nvrColor : '#adb5bd';
                    
                    $capText = $hasData && $hddData['capacity'] ? $hddData['capacity'] : '-';
                    $hddJson = $hasData ? htmlspecialchars(json_encode($hddData), ENT_QUOTES, 'UTF-8') : 'null';
                ?>
                    <div onclick="openManageModal('<?= esc($nvrName) ?>', '<?= esc($hddLabel) ?>', <?= $hddJson ?>)" style="background: <?= $bgColor ?>; border: 1px solid <?= $borderColor ?>; border-radius: 6px; padding: 6px; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 -2px 0 rgba(0,0,0,0.02); transition: transform 0.15s ease, box-shadow 0.15s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='inset 0 -2px 0 rgba(0,0,0,0.02)';">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="<?= $iconColor ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 2px;">
                            <line x1="22" y1="12" x2="2" y2="12"></line>
                            <path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                            <line x1="6" y1="16" x2="6.01" y2="16"></line>
                            <line x1="10" y1="16" x2="10.01" y2="16"></line>
                        </svg>
                        <span style="font-size: 10px; font-weight: bold; color: <?= $textColor ?>;"><?= esc($hddLabel) ?></span>
                        <span style="font-size: 9px; color: #555;"><?= esc($capText) ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Manage HDD -->
<div id="manageModal" class="modal">
    <div class="modal-content">
        <form action="<?= site_url('cctv/hardisk-manage/save') ?>" method="POST" id="manageForm">
            <div class="modal-header">
                <h3 id="modalTitle">Manage Hardisk</h3>
                <span class="close" onclick="closeModal('manageModal')">&times;</span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="nvr_name" id="inp_nvr_name">
                <input type="hidden" name="hdd_label" id="inp_hdd_label">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Merk / Brand</label>
                        <select id="inp_brand_select" onchange="handleBrandChange(this.value)">
                            <option value="Seagate">Seagate</option>
                            <option value="Western Digital (WD)">Western Digital (WD)</option>
                            <option value="Toshiba">Toshiba</option>
                            <option value="Samsung">Samsung</option>
                            <option value="Custom">Custom (Isi Mandiri)</option>
                        </select>
                        <input type="text" name="brand" id="inp_brand" placeholder="Masukkan merk..." style="display:none; margin-top:8px;">
                    </div>
                    <div class="form-group">
                        <label>Kapasitas</label>
                        <div style="display: flex; gap: 10px;">
                            <select name="capacity_num" id="inp_capacity_num" style="flex: 1;">
                                <option value="2">2</option>
                                <option value="4">4</option>
                                <option value="6">6</option>
                                <option value="8">8</option>
                                <option value="16">16</option>
                            </select>
                            <select name="capacity_unit" id="inp_capacity_unit" style="width: 80px;">
                                <option value="TB">TB</option>
                                <option value="GB">GB</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Serial Number (S/N)</label>
                        <input type="text" name="serial_number" id="inp_sn" placeholder="Nomor Seri">
                    </div>
                    <div class="form-group">
                        <label>Status Kondisi</label>
                        <select name="status" id="inp_status">
                            <option value="Active">Active / Normal</option>
                            <option value="Spare">Spare / Cadangan</option>
                            <option value="Broken">Broken / Rusak</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top: 15px;">
                    <label>Catatan (Remark)</label>
                    <textarea name="remark" id="inp_remark" rows="3" placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <?php if ($page_perm !== 'R'): ?>
                <button type="submit" class="action-btn btn-save">💾 Simpan</button>
                <?php endif; ?>
                <button type="button" class="action-btn btn-cancel" onclick="closeModal('manageModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function openManageModal(nvr, label, data) {
        document.getElementById('modalTitle').innerText = 'Manage HDD ' + label + ' (' + nvr + ')';
        document.getElementById('inp_nvr_name').value = nvr;
        document.getElementById('inp_hdd_label').value = label;
        
        if (data) {
            if (data.brand) {
                let predefined = ["Seagate", "Western Digital (WD)", "Toshiba", "Samsung"];
                if (predefined.includes(data.brand)) {
                    document.getElementById('inp_brand_select').value = data.brand;
                    document.getElementById('inp_brand').value = data.brand;
                    document.getElementById('inp_brand').style.display = 'none';
                } else {
                    document.getElementById('inp_brand_select').value = "Custom";
                    document.getElementById('inp_brand').value = data.brand;
                    document.getElementById('inp_brand').style.display = 'block';
                }
            } else {
                document.getElementById('inp_brand_select').value = "Seagate";
                document.getElementById('inp_brand').value = "Seagate";
                document.getElementById('inp_brand').style.display = 'none';
            }
            
            document.getElementById('inp_sn').value = data.serial_number || '';
            document.getElementById('inp_status').value = data.status || 'Active';
            document.getElementById('inp_remark').value = data.remark || '';
            
            if (data.capacity) {
                let match = data.capacity.match(/(\d+)\s*(GB|TB)/i);
                if (match) {
                    document.getElementById('inp_capacity_num').value = match[1];
                    document.getElementById('inp_capacity_unit').value = match[2].toUpperCase();
                }
            } else {
                document.getElementById('inp_capacity_num').value = '8';
                document.getElementById('inp_capacity_unit').value = 'TB';
            }
        } else {
            document.getElementById('manageForm').reset();
            document.getElementById('inp_nvr_name').value = nvr;
            document.getElementById('inp_hdd_label').value = label;
            document.getElementById('inp_status').value = 'Active';
            document.getElementById('inp_capacity_num').value = '8';
            document.getElementById('inp_capacity_unit').value = 'TB';
            document.getElementById('inp_brand_select').value = "Seagate";
            document.getElementById('inp_brand').value = "Seagate";
            document.getElementById('inp_brand').style.display = 'none';
        }
        
        document.getElementById('manageModal').style.display = 'block';
    }

    function handleBrandChange(val) {
        if (val === 'Custom') {
            document.getElementById('inp_brand').style.display = 'block';
            document.getElementById('inp_brand').value = '';
            document.getElementById('inp_brand').focus();
        } else {
            document.getElementById('inp_brand').style.display = 'none';
            document.getElementById('inp_brand').value = val;
        }
    }

    window.onclick = function(e) {
        if (e.target == document.getElementById('manageModal')) {
            closeModal('manageModal');
        }
    }
</script>

<?= $this->endSection() ?>
