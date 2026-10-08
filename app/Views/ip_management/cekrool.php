<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .right-frame {
        background: #f4f6fa;
        padding: 20px;
        min-height: calc(100vh - 60px);
        overflow-y: auto;
    }
    .card-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
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
        max-height: 65vh;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #eee;
        border-radius: 5px;
    }
    .ip-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px; /* Force scroll on small screens instead of squishing */
    }
    .ip-table th, .ip-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #eee;
        text-align: left;
        font-size: 12px;
        vertical-align: middle;
    }
    .ip-table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 1;
        white-space: nowrap;
    }
    .ip-table td {
        word-break: break-word;
    }
    .ip-table tr:hover {
        background-color: #f1f8ff;
    }
    
    /* Responsive Toolbar */
    .toolbar-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    .toolbar-left, .toolbar-right {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }
    .toolbar-right select, .toolbar-right input {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 12px;
        height: 32px;
        box-sizing: border-box;
    }
    
    @media (max-width: 768px) {
        .toolbar-container { flex-direction: column; align-items: stretch; }
        .toolbar-left, .toolbar-right { flex-direction: column; align-items: stretch; }
        .action-btn { text-align: center; width: 100%; margin: 0 !important; }
        .toolbar-right select, .toolbar-right input { width: 100% !important; }
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
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 500px;
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
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 13px;
        color: #333;
    }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
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
    }
    .form-submit-btn:hover {
        background-color: #005f93;
    }
    .back-portal {
        display: inline-block;
        margin-bottom: 15px;
        color: #0077b6;
        text-decoration: none;
        font-weight: bold;
        font-size: 13px;
    }
    .back-portal:hover {
        text-decoration: underline;
    }
</style>

<div class="right-frame">
    <div class="card-container">
        <a href="<?= site_url('ip-management') ?>" class="back-portal">⬅️ Kembali ke Portal IP List</a>
        <h2>Cekrool Management</h2>
        <p style="color: #666; font-size: 13px; margin-bottom: 20px;">Kelola data cek perizinan role administrator dan integrasi perizinan perangkat keamanan jaringan Anda.</p>

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

        <form action="<?= site_url('ip-management/cekrool/bulk-delete') ?>" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Cekrool yang dipilih?');">
            
            <div class="toolbar-container">
                <div class="toolbar-left">
                    <?php if ($page_perm !== 'R'): ?>
                    <button type="button" class="action-btn btn-add" onclick="openModal('addModal')" style="margin:0;">➕ Tambah Device</button>
                    <button type="button" class="action-btn" style="background:#17a2b8; margin:0;" onclick="openModal('importModal')">📥 Import Excel</button>
                    <a href="<?= site_url('ip-management/cekrool/downloadTemplate') ?>" class="action-btn" style="background:#28a745; margin:0; text-decoration: none;">⬇️ Template</a>
                    <a href="<?= site_url('ip-management/cekrool/exportExcel') ?>" class="action-btn" style="background:#fd7e14; margin:0; text-decoration: none;">📤 Export</a>
                    <button type="submit" class="action-btn btn-bulk-delete" id="btnBulkDelete" style="margin:0;" disabled>🗑 Hapus Terpilih</button>
                    <?php else: ?>
                    <div style="background:#fff3cd; color:#856404; padding:8px 12px; border-radius:4px; font-size:12px;">
                        <b>Read-Only Mode</b>
                    </div>
                    <a href="<?= site_url('ip-management/cekrool/exportExcel') ?>" class="action-btn" style="background:#fd7e14; margin:0; text-decoration: none;">📤 Export Excel</a>
                    <?php endif; ?>
                </div>
                
                <div class="toolbar-right">
                    <div style="display:flex; align-items:center; gap:5px;">
                        <label style="margin:0; font-size:12px; font-weight:bold; color:#555; white-space:nowrap;">Urutkan:</label>
                        <select id="sortSelect" onchange="sortTable()" style="width: 140px;">
                            <option value="default">Default (No)</option>
                            <option value="location_asc">Location (A-Z)</option>
                            <option value="device_asc">Device ID (A-Z)</option>
                            <option value="device_desc">Device ID (Z-A)</option>
                            <option value="ip_asc">IP (Naik)</option>
                            <option value="ip_desc">IP (Turun)</option>
                        </select>
                    </div>
                    <div style="display:flex; align-items:center; gap:5px;">
                        <label style="margin:0; font-size:12px; font-weight:bold; color:#555; white-space:nowrap;">Ping:</label>
                        <select id="pingIntervalSelect" onchange="startPingCycle()" style="width: 90px;">
                            <option value="1">1 Mnt</option>
                            <option value="5" selected>5 Mnt</option>
                            <option value="10">10 Mnt</option>
                            <option value="30">30 Mnt</option>
                        </select>
                    </div>
                    <input type="text" id="searchInput" placeholder="Cari Device..." style="width: 160px;" onkeyup="filterTable()">
                </div>
            </div>

            <div class="table-scroll-container">
                <table class="ip-table" id="dataTable">
                    <thead>
                        <tr>
                            <?php if ($page_perm !== 'R'): ?>
                            <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                            <?php endif; ?>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th style="width: 80px;">Building</th>
                            <th style="width: 200px;">Location</th>
                            <th style="width: 150px; text-align: center;">Device ID</th>
                            <th style="width: 130px; text-align: center;">IP Address</th>
                            <th style="width: 180px;">Type Device</th>
                            <th style="width: 100px; text-align: center;">Status</th>
                            <th>Notes</th>
                            <?php if ($page_perm !== 'R'): ?>
                            <th style="width: 80px; text-align: center;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($devices)): ?>
                            <?php $no = 1; foreach($devices as $row): ?>
                                <tr>
                                    <?php if ($page_perm !== 'R'): ?>
                                    <td style="text-align: center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="checkItem" onclick="toggleBulkDeleteBtn()"></td>
                                    <?php endif; ?>
                                    <td style="text-align: center;"><?= $no++ ?></td>
                                    <td><?= esc($row['building'] ?? '-') ?></td>
                                    <td><?= esc($row['location']) ?></td>
                                    <td style="text-align: center;"><strong><?= esc($row['device_id'] ?? '-') ?></strong></td>
                                    <td style="text-align: center;"><code><?= esc($row['ip_address'] ?? '-') ?></code></td>
                                    <td><?= esc($row['type_device']) ?></td>
                                    <td class="ping-status-cell" data-ip="<?= esc($row['ip_address'] ?? '-') ?>" style="text-align: center; vertical-align: middle;">
                                        <span style="color:#aaa; font-style:italic; font-size: 11px;">Menunggu...</span>
                                    </td>
                                    <td><?= esc($row['notes'] ?? '-') ?></td>
                                    <?php if ($page_perm !== 'R'): ?>
                                    <td style="text-align: center;">
                                        <button type="button" class="action-btn btn-edit" onclick='openEditModal(<?= json_encode($row) ?>)'>✎</button>
                                        <a href="<?= site_url('ip-management/cekrool/delete/'.$row['id']) ?>" class="action-btn btn-delete" onclick="return confirm('Hapus data Device Cekrool ini?');">✖</a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= ($page_perm !== 'R') ? '8' : '6' ?>" style="text-align:center; padding: 20px;">Belum ada data Device Cekrool yang dikelola.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Data -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('addModal')">&times;</span>
        <h3>Tambah Device Cekrool Baru</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('ip-management/cekrool/store') ?>" method="POST">
            <div class="form-group">
                <label for="building">Building</label>
                <select id="building" name="building" required>
                    <option value="-">-</option>
                    <option value="F1">F1</option>
                    <option value="F2">F2</option>
                    <option value="F3">F3</option>
                    <option value="F4">F4</option>
                    <option value="F5">F5</option>
                    <option value="F6">F6</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="IH">IH</option>
                    <option value="New Chemical">New Chemical</option>
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required placeholder="Contoh: Lantai 1 Pos Security">
            </div>
            <div class="form-group">
                <label for="device_id">Device ID</label>
                <input type="text" id="device_id" name="device_id" required value="-" style="text-align: center;">
            </div>
            <div class="form-group">
                <label for="ip_address">IP Address</label>
                <input type="text" id="ip_address" name="ip_address" required value="-" style="text-align: center;">
            </div>
            <div class="form-group">
                <label for="type_device">Type Device</label>
                <select id="type_device" name="type_device" required>
                    <option value="-">-</option>
                    <option value="Fingerprint Reader">Fingerprint Reader</option>
                    <option value="Card Reader">Card Reader</option>
                </select>
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Keterangan tambahan..."></textarea>
            </div>
            <button type="submit" class="form-submit-btn">Simpan Device</button>
        </form>
    </div>
</div>

<!-- Modal Edit Data -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit Device Cekrool</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="" method="POST" id="editForm">
            <div class="form-group">
                <label for="edit_building">Building</label>
                <select id="edit_building" name="building" required>
                    <option value="-">-</option>
                    <option value="F1">F1</option>
                    <option value="F2">F2</option>
                    <option value="F3">F3</option>
                    <option value="F4">F4</option>
                    <option value="F5">F5</option>
                    <option value="F6">F6</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="IH">IH</option>
                    <option value="New Chemical">New Chemical</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_location">Location</label>
                <input type="text" id="edit_location" name="location" required>
            </div>
            <div class="form-group">
                <label for="edit_device_id">Device ID</label>
                <input type="text" id="edit_device_id" name="device_id" required style="text-align: center;">
            </div>
            <div class="form-group">
                <label for="edit_ip_address">IP Address</label>
                <input type="text" id="edit_ip_address" name="ip_address" required style="text-align: center;">
            </div>
            <div class="form-group">
                <label for="edit_type_device">Type Device</label>
                <select id="edit_type_device" name="type_device" required>
                    <option value="-">-</option>
                    <option value="Fingerprint Reader">Fingerprint Reader</option>
                    <option value="Card Reader">Card Reader</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_notes">Notes</label>
                <textarea id="edit_notes" name="notes" rows="3"></textarea>
            </div>
            <button type="submit" class="form-submit-btn">Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="importModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('importModal')">&times;</span>
        <h3>Import Data via Excel</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('ip-management/cekrool/importExcel') ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file_excel">Pilih File Excel (.xls, .xlsx, .csv)</label>
                <input type="file" id="file_excel" name="file_excel" accept=".xls,.xlsx,.csv" required style="border:none; padding:10px 0;">
            </div>
            <p style="font-size: 11px; color: #856404; background: #fff3cd; padding: 8px; border-radius: 4px; margin-bottom:15px; line-height:1.4;">
                ⚠️ <strong>Perhatian:</strong> Pastikan urutan kolom sesuai dengan template (Building, Location, Device ID, IP Address, Type Device, Notes). Baris kosong otomatis dilewati, kolom kosong akan diisi dengan '-'.
            </p>
            <button type="submit" class="form-submit-btn" style="background-color: #17a2b8;">Mulai Proses Import</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = "block";
    }
    
    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    function openEditModal(data) {
        document.getElementById('editForm').action = "<?= site_url('ip-management/cekrool/update/') ?>" + data.id;
        document.getElementById('edit_building').value = data.building || '-';
        document.getElementById('edit_location').value = data.location;
        document.getElementById('edit_device_id').value = data.device_id;
        document.getElementById('edit_ip_address').value = data.ip_address;
        
        let typeDeviceOptions = Array.from(document.getElementById('edit_type_device').options).map(o => o.value);
        if (typeDeviceOptions.includes(data.type_device)) {
            document.getElementById('edit_type_device').value = data.type_device;
        } else {
            document.getElementById('edit_type_device').value = '-';
        }
        
        document.getElementById('edit_notes').value = data.notes || '';
        
        openModal('editModal');
    }

    // Close modal when clicking outside of it
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

    // Bind manually click event for selectAll if checked state changes
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
        const trs = document.getElementById("dataTable").getElementsByTagName("tr");

        for (let i = 1; i < trs.length; i++) { // skip header
            let text = trs[i].textContent.toLowerCase();
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
        
        if (trs.length === 0 || trs[0].cells.length <= 1) return;

        trs.sort((a, b) => {
            const isReadOnly = <?= ($page_perm === 'R') ? 'true' : 'false' ?>;
            const noIndex = isReadOnly ? 0 : 1;
            const locationIndex = isReadOnly ? 1 : 2;
            const deviceIndex = isReadOnly ? 2 : 3;
            const ipIndex = isReadOnly ? 3 : 4;
            
            let valA = '';
            let valB = '';
            
            if (val === 'default') {
                valA = parseInt(a.cells[noIndex].textContent);
                valB = parseInt(b.cells[noIndex].textContent);
                return valA - valB;
            } else if (val === 'location_asc') {
                valA = a.cells[locationIndex].textContent.trim().toLowerCase();
                valB = b.cells[locationIndex].textContent.trim().toLowerCase();
                return valA.localeCompare(valB);
            } else if (val === 'device_asc') {
                valA = a.cells[deviceIndex].textContent.trim().toLowerCase();
                valB = b.cells[deviceIndex].textContent.trim().toLowerCase();
                return valA.localeCompare(valB);
            } else if (val === 'device_desc') {
                valA = a.cells[deviceIndex].textContent.trim().toLowerCase();
                valB = b.cells[deviceIndex].textContent.trim().toLowerCase();
                return valB.localeCompare(valA);
            } else if (val === 'ip_asc' || val === 'ip_desc') {
                const ipA = a.cells[ipIndex].textContent.trim();
                const ipB = b.cells[ipIndex].textContent.trim();
                
                const ipToNum = (ipStr) => {
                    if (ipStr === '-' || ipStr === '') return 0;
                    const parts = ipStr.split('.');
                    if (parts.length !== 4) return 0;
                    return parts.reduce((acc, octet) => (acc << 8) + parseInt(octet, 10), 0) >>> 0;
                };
                
                valA = ipToNum(ipA);
                valB = ipToNum(ipB);
                
                return val === 'ip_asc' ? valA - valB : valB - valA;
            }
            return 0;
        });
        
        tbody.innerHTML = '';
        trs.forEach(tr => tbody.appendChild(tr));
    }

    let cekroolIntervalTimer = null;

    function startPingCycle() {
        const intervalMinutes = parseInt(document.getElementById('pingIntervalSelect').value);
        if (cekroolIntervalTimer) clearInterval(cekroolIntervalTimer);
        
        runPingAll();
        cekroolIntervalTimer = setInterval(runPingAll, intervalMinutes * 60000);
    }

    async function runPingAll() {
        const cells = document.querySelectorAll('.ping-status-cell');
        for(let cell of cells) {
            // Jangan memproses baris yang sedang disembunyikan filter
            if (cell.parentElement.style.display === 'none') continue;

            const ip = cell.getAttribute('data-ip');
            if(!ip || ip === '-' || ip === '') {
                cell.innerHTML = '<span style="color:#7f8c8d; font-size:11px; font-weight:bold;">IP not set</span>';
                continue;
            }
            
            cell.innerHTML = '<span style="color:#f39c12; font-size:11px; font-style:italic;">Mengecek... <span style="display:inline-block; animation:blinkDot 1s infinite; width:5px; height:5px; background:#f39c12; border-radius:50%;"></span></span>';
            try {
                const res = await fetch('<?= site_url('monitoring/cekPing') ?>?ip=' + ip);
                const data = await res.json();
                if(data.status === 'online') {
                    cell.innerHTML = `<span style="background-color:#2ecc71; color:white; padding:3px 8px; border-radius:12px; font-size:10px; font-weight:bold; letter-spacing:0.5px; box-shadow:0 2px 4px rgba(46,204,113,0.3);">UP</span><br><span style="font-size:10px; color:#555; display:inline-block; margin-top:3px;">${data.response_time}</span>`;
                } else {
                    cell.innerHTML = `<span style="background-color:#e74c3c; color:white; padding:3px 8px; border-radius:12px; font-size:10px; font-weight:bold; letter-spacing:0.5px; box-shadow:0 2px 4px rgba(231,76,60,0.3);">DOWN</span>`;
                }
            } catch(e) {
                cell.innerHTML = '<span style="color:#e74c3c; font-size:11px;">Error</span>';
            }
        }
    }

    // Jalankan pertama kali saat halaman siap
    document.addEventListener('DOMContentLoaded', () => {
        startPingCycle();
    });
</script>

<?= $this->endSection() ?>
