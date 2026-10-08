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
        border: 1px solid #eee;
        border-radius: 5px;
    }
    .ip-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ip-table th, .ip-table td {
        padding: 5px 10px;
        border-bottom: 1px solid #eee;
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
    }
    .ip-table tr:hover {
        background-color: #f1f8ff;
    }
    
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0; width: 100%; height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 60%;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close-btn:hover { color: black; }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .form-actions {
        margin-top: 20px;
        text-align: right;
    }
</style>

<div class="right-frame">
    <div style="margin-bottom: 20px;">
        <a href="<?= site_url('ip-management') ?>" style="text-decoration: none; color: #0077b6; font-weight: bold; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(-5px)'" onmouseout="this.style.transform='none'">
            ⬅️ Kembali ke Portal IP List Hub
        </a>
    </div>
    <h3>🔌 Switch Network Management</h3><br>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="background-color:#d4edda; color:#155724; padding:10px; border-radius:4px; margin-bottom:15px; border:1px solid #c3e6cb;">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:4px; margin-bottom:15px; border:1px solid #f5c6cb;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card-container">
        <form action="<?= site_url('ip-management/switch-network/bulk-delete') ?>" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Switch yang dipilih?');">
            
            <?php if ($page_perm !== 'R'): ?>
            <button type="button" class="action-btn btn-add" onclick="openModal('addModal')">➕ Tambah Switch Baru</button>
            <button type="button" class="action-btn" style="background:#17a2b8; margin-bottom: 15px;" onclick="openModal('importModal')">📥 Import Excel</button>
            <a href="<?= site_url('ip-management/switch-network/downloadTemplate') ?>" class="action-btn" style="background:#28a745; margin-bottom: 15px; text-decoration: none; margin-left: 5px;">⬇️ Download Template</a>
            <a href="<?= site_url('ip-management/switch-network/exportExcel') ?>" class="action-btn" style="background:#fd7e14; margin-bottom: 15px; text-decoration: none; margin-left: 5px;">📤 Export Excel</a>
            <button type="submit" class="action-btn btn-bulk-delete" id="btnBulkDelete" disabled>🗑 Hapus Terpilih</button>
            <?php else: ?>
            <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; margin-bottom:15px; display: flex; justify-content: space-between; align-items: center;">
                <span>Mode <b>Read-Only</b>. Anda tidak dapat mengubah data Switch.</span>
                <a href="<?= site_url('ip-management/switch-network/exportExcel') ?>" class="action-btn" style="background:#fd7e14; text-decoration: none;">📤 Export Excel</a>
            </div>
            <?php endif; ?>
            
            <div style="margin-bottom: 15px; float: right; display: flex; gap: 10px; align-items: center;">
                <label style="margin: 0; font-size: 12px; font-weight: bold; color: #555;">Urutkan:</label>
                <select id="sortSelect" onchange="sortTable()" style="padding: 6px; border: 1px solid #ccc; border-radius: 4px; width: 180px; font-size: 12px; height: 32px; box-sizing: border-box;">
                    <option value="default">Default (No)</option>
                    <option value="name_asc">Name Switch (A-Z)</option>
                    <option value="name_desc">Name Switch (Z-A)</option>
                    <option value="ip_asc">IP Address (1-10 / Naik)</option>
                    <option value="ip_desc">IP Address (10-1 / Turun)</option>
                    <option value="lokasi_asc">Lokasi (A-Z)</option>
                </select>
                <input type="text" id="searchInput" placeholder="Cari Switch..." style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; width: 200px; font-size: 12px; height: 32px; box-sizing: border-box;" onkeyup="filterTable()">
            </div>

            <div class="table-scroll-container">
                <table class="ip-table" id="dataTable">
                    <thead>
                        <tr>
                            <?php if ($page_perm !== 'R'): ?>
                            <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                            <?php endif; ?>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th style="width: 200px;">Lokasi</th>
                            <th style="width: 150px;">Name Switch</th>
                            <th style="width: 130px;">IP Address</th>
                            <th style="width: 180px;">Type Switch</th>
                            <th>Notes</th>
                            <?php if ($page_perm !== 'R'): ?>
                            <th style="width: 80px; text-align: center;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($switches)): ?>
                            <?php $no = 1; foreach($switches as $row): ?>
                                <tr>
                                    <?php if ($page_perm !== 'R'): ?>
                                    <td style="text-align: center;"><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="checkItem" onclick="toggleBulkDeleteBtn()"></td>
                                    <?php endif; ?>
                                    <td style="text-align: center;"><?= $no++ ?></td>
                                    <td><?= esc($row['lokasi']) ?></td>
                                    <td><strong><?= esc($row['name_switch']) ?></strong></td>
                                    <td><code><?= esc($row['ip_address']) ?></code></td>
                                    <td><?= esc($row['type_switch']) ?></td>
                                    <td><?= esc($row['notes'] ?? '-') ?></td>
                                    <?php if ($page_perm !== 'R'): ?>
                                    <td style="text-align: center;">
                                        <button type="button" class="action-btn btn-edit" onclick='openEditModal(<?= json_encode($row) ?>)'>✎</button>
                                        <a href="<?= site_url('ip-management/switch-network/delete/'.$row['id']) ?>" class="action-btn btn-delete" onclick="return confirm('Hapus data Switch ini?');">✖</a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= ($page_perm !== 'R') ? '8' : '6' ?>" style="text-align:center; padding: 20px;">Belum ada data Switch yang dikelola.</td></tr>
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
        <h3>Tambah Switch Baru</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('ip-management/switch-network/store') ?>" method="POST">
            <div style="display: flex; flex-wrap: wrap; gap: 2%;">
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Lokasi *</label>
                    <input type="text" name="lokasi" required placeholder="Contoh: Lantai 2 Server Room">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Name Switch *</label>
                    <input type="text" name="name_switch" required placeholder="Contoh: Switch-Core-01">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>IP Address *</label>
                    <input type="text" name="ip_address" required placeholder="Contoh: 192.168.10.2">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Type Switch *</label>
                    <input type="text" name="type_switch" required placeholder="Contoh: Cisco Catalyst 2960 / Ruijie">
                </div>
            </div>
            <div class="form-group">
                <label>Notes / Deskripsi</label>
                <textarea name="notes" rows="3" placeholder="Catatan tambahan..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="action-btn" style="background:#6c757d;" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="action-btn btn-add" style="margin-bottom:0;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Data -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit Data Switch</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form id="editForm" method="POST">
            <div style="display: flex; flex-wrap: wrap; gap: 2%;">
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Lokasi *</label>
                    <input type="text" name="lokasi" id="edit_lokasi" required>
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Name Switch *</label>
                    <input type="text" name="name_switch" id="edit_name_switch" required>
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>IP Address *</label>
                    <input type="text" name="ip_address" id="edit_ip" required>
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Type Switch *</label>
                    <input type="text" name="type_switch" id="edit_type_switch" required>
                </div>
            </div>
            <div class="form-group">
                <label>Notes / Deskripsi</label>
                <textarea name="notes" id="edit_notes" rows="3"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="action-btn" style="background:#6c757d;" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="action-btn btn-add" style="margin-bottom:0;">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import Excel -->
<div id="importModal" class="modal">
    <div class="modal-content" style="width: 400px;">
        <span class="close-btn" onclick="closeModal('importModal')">&times;</span>
        <h3>Import Data Switch dari Excel</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('ip-management/switch-network/importExcel') ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Pilih File Excel (.xls, .xlsx, .csv)</label>
                <input type="file" name="file_excel" accept=".xls,.xlsx,.csv" required style="padding: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <a href="<?= site_url('ip-management/switch-network/downloadTemplate') ?>" style="color: #28a745; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                    ⬇️ Download Template Excel
                </a>
            </div>
            <div style="background-color:#e2e3e5; color:#383d41; padding:10px; border-radius:4px; font-size:12px; margin-bottom:15px;">
                <b>Format Kolom Excel (Tanpa Header / Dimulai Baris 2):</b><br>
                A: Lokasi<br>
                B: Name Switch<br>
                C: IP Address<br>
                D: Type Switch<br>
                E: Notes
            </div>
            
            <div class="form-actions">
                <button type="button" class="action-btn" style="background:#6c757d;" onclick="closeModal('importModal')">Batal</button>
                <button type="submit" class="action-btn" style="background:#17a2b8; margin-bottom:0;">Upload & Import</button>
            </div>
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
        document.getElementById('editForm').action = "<?= site_url('ip-management/switch-network/update/') ?>" + data.id;
        document.getElementById('edit_lokasi').value = data.lokasi;
        document.getElementById('edit_name_switch').value = data.name_switch;
        document.getElementById('edit_ip').value = data.ip_address;
        document.getElementById('edit_type_switch').value = data.type_switch;
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
            const lokasiIndex = isReadOnly ? 1 : 2;
            const nameIndex = isReadOnly ? 2 : 3;
            const ipIndex = isReadOnly ? 3 : 4;
            
            let valA = '';
            let valB = '';
            
            if (val === 'default') {
                valA = parseInt(a.cells[noIndex].textContent);
                valB = parseInt(b.cells[noIndex].textContent);
                return valA - valB;
            } else if (val === 'name_asc') {
                valA = a.cells[nameIndex].textContent.trim().toLowerCase();
                valB = b.cells[nameIndex].textContent.trim().toLowerCase();
                return valA.localeCompare(valB);
            } else if (val === 'name_desc') {
                valA = a.cells[nameIndex].textContent.trim().toLowerCase();
                valB = b.cells[nameIndex].textContent.trim().toLowerCase();
                return valB.localeCompare(valA);
            } else if (val === 'lokasi_asc') {
                valA = a.cells[lokasiIndex].textContent.trim().toLowerCase();
                valB = b.cells[lokasiIndex].textContent.trim().toLowerCase();
                return valA.localeCompare(valB);
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
</script>

<?= $this->endSection() ?>
