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
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        color: white;
        font-size: 13px;
        transition: background 0.2s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-add { background-color: #28a745; margin-bottom: 15px; }
    .btn-edit { background-color: #ffc107; color: #333; padding: 5px 10px; font-size: 11px; }
    .btn-delete { background-color: #dc3545; padding: 5px 10px; font-size: 11px; }
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
        padding: 6px 8px;
        border-bottom: 1px solid #eee;
        text-align: left;
        font-size: 13px;
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
    
    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
        color: white;
    }
    .bg-active { background-color: #28a745; }
    .bg-inactive { background-color: #dc3545; }
    .bg-reserved { background-color: #17a2b8; }
    
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
    .btn-nvr {
        display: inline-block;
        padding: 8px 20px;
        background-color: #e2e8f0;
        color: #333;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        border: 1px solid #ccc;
        transition: background-color 0.2s;
        font-size: 13px;
    }
    .btn-nvr:hover { background-color: #cbd5e1; }
    .btn-nvr.active { background-color: #0077b6; color: white; border-color: #0077b6; }
</style>

<div class="right-frame">
    <div style="margin-bottom: 20px;">
        <a href="<?= site_url('ip-management') ?>" style="text-decoration: none; color: #0077b6; font-weight: bold; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; transition: transform 0.2s;" onmouseover="this.style.transform='translateX(-5px)'" onmouseout="this.style.transform='none'">
            ⬅️ Kembali ke Portal IP List Hub
        </a>
    </div>
    <h3>🌐 IP List Management - User PC</h3><br>

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
        <form action="<?= site_url('ip-management/bulk-delete') ?>" method="POST" id="bulkDeleteForm" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data yang dipilih?');">
            
            <?php if ($page_perm !== 'R'): ?>
            <button type="button" class="action-btn btn-add" onclick="openModal('addModal')">➕ Tambah IP Baru</button>
            <button type="button" class="action-btn" style="background:#17a2b8; margin-bottom: 15px;" onclick="openModal('importModal')">📥 Import Excel</button>
            <a href="<?= site_url('ip-management/downloadTemplate') ?>" class="action-btn" style="background:#28a745; margin-bottom: 15px; text-decoration: none; margin-left: 5px;">⬇️ Download Template</a>
            <button type="submit" class="action-btn btn-bulk-delete" id="btnBulkDelete" disabled>🗑 Hapus Terpilih</button>
            <?php else: ?>
            <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; margin-bottom:15px;">
                Mode <b>Read-Only</b>. Anda tidak dapat mengubah data.
            </div>
            <?php endif; ?>
            
            <div style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 10px;">
                <?php
                $vlan_list = [];
                for ($i = 40; $i <= 56; $i++) {
                    $vlan_list[] = $i;
                }
                $vlan_list[] = 60;
                
                $is_all_active = ($active_vlan == '') ? 'active' : '';
                echo '<a href="'.site_url('ip-management/user-pc').'" class="btn-nvr '.$is_all_active.'">Semua VLAN</a>';
                
                foreach($vlan_list as $v) {
                    $is_v_active = ($active_vlan == $v) ? 'active' : '';
                    echo '<a href="'.site_url('ip-management/user-pc').'?vlan='.$v.'" class="btn-nvr '.$is_v_active.'">VLAN '.$v.'</a>';
                }
                ?>
            </div>
            
            <div class="sort-bar" style="margin-top: 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; flex-wrap: wrap;">
                <label style="font-size: 13px; font-weight: 600; color: #475569;">Urutkan berdasarkan:</label>
                <select id="sort-column" class="sort-select" style="padding: 6px 10px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; outline: none; cursor: pointer;" onchange="applySort()">
                    <option value="ip_address" <?= $sort == 'ip_address' ? 'selected' : '' ?>>IP Address</option>
                    <option value="mac_address" <?= $sort == 'mac_address' ? 'selected' : '' ?>>MAC Address</option>
                    <option value="device_type" <?= $sort == 'device_type' ? 'selected' : '' ?>>Device Type</option>
                    <option value="user_assigned" <?= $sort == 'user_assigned' ? 'selected' : '' ?>>Assigned To</option>
                    <option value="department" <?= $sort == 'department' ? 'selected' : '' ?>>Department</option>
                    <option value="status" <?= $sort == 'status' ? 'selected' : '' ?>>Status</option>
                </select>
                <button id="btn-sort-dir" class="btn-sort-dir" style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; cursor: pointer; font-size: 13px; font-weight: 700; color: #475569;" data-dir="<?= $dir ?>" onclick="toggleDir(event)">
                    <?= $dir == 'ASC' ? 'A-Z ↑' : 'Z-A ↓' ?>
                </button>
                <div style="margin-left: 15px;">
                    <input type="text" id="searchInput" placeholder="Cari IP/Device..." style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px; width: 200px; font-size: 13px;" onkeyup="filterTable()">
                </div>
                <div class="sort-info" style="font-size: 12px; color: #94a3b8; margin-left: auto;">
                    Menampilkan data <?= $active_vlan ? 'VLAN ' . esc($active_vlan) : 'Semua VLAN' ?>
                </div>
            </div>

            <div class="table-scroll-container">
                <table class="ip-table" id="dataTable">
                    <thead>
                        <tr>
                            <?php if ($page_perm !== 'R'): ?>
                            <th width="5%"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                            <?php endif; ?>
                            <th width="5%">No</th>
                            <th width="15%">IP Address</th>
                            <th width="15%">MAC Address</th>
                            <th width="15%">Device Type</th>
                            <th width="15%">Assigned To / Dept</th>
                            <th width="10%">Status</th>
                            <?php if ($page_perm !== 'R'): ?>
                            <th width="10%" style="text-align: center;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($ips)): ?>
                            <?php $no = 1; foreach($ips as $row): ?>
                                <tr>
                                    <?php if ($page_perm !== 'R'): ?>
                                    <td><input type="checkbox" name="ids[]" value="<?= $row['id'] ?>" class="checkItem" onclick="toggleBulkDeleteBtn()"></td>
                                    <?php endif; ?>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= esc($row['ip_address']) ?></strong></td>
                                    <td><?= esc($row['mac_address'] ?? '-') ?></td>
                                    <td><?= esc($row['device_type'] ?? '-') ?></td>
                                    <td>
                                        <?= esc($row['user_assigned'] ?? '-') ?><br>
                                        <small style="color: #666;"><?= esc($row['department'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <?php 
                                            $bg = 'bg-inactive';
                                            if ($row['status'] == 'Active') $bg = 'bg-active';
                                            if ($row['status'] == 'Reserved') $bg = 'bg-reserved';
                                        ?>
                                        <span class="badge <?= $bg ?>"><?= esc($row['status']) ?></span>
                                    </td>
                                    <?php if ($page_perm !== 'R'): ?>
                                    <td style="text-align: center;">
                                        <button type="button" class="action-btn btn-edit" onclick='openEditModal(<?= json_encode($row) ?>)'>✎</button>
                                        <a href="<?= site_url('ip-management/delete/'.$row['id']) ?>" class="action-btn btn-delete" onclick="return confirm('Hapus data IP ini?');">✖</a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="<?= ($page_perm !== 'R') ? '8' : '6' ?>" style="text-align:center; padding: 20px;">Belum ada data IP yang dikelola.</td></tr>
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
        <h3>Tambah Alokasi IP Baru</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('ip-management/store') ?>" method="POST">
            <div style="display: flex; flex-wrap: wrap; gap: 2%;">
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>IP Address *</label>
                    <input type="text" name="ip_address" required placeholder="Contoh: 192.168.1.100">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>MAC Address</label>
                    <input type="text" name="mac_address" placeholder="Contoh: 00:1A:2B:3C:4D:5E">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Kategori Perangkat</label>
                    <select name="device_type">
                        <option value="PC">PC</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Server">Server</option>
                        <option value="Printer">Printer</option>
                        <option value="Switch">Switch</option>
                        <option value="Router">Router</option>
                        <option value="CCTV">CCTV</option>
                        <option value="Access Point">Access Point</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Nama Pengguna / Assigned To</label>
                    <input type="text" name="user_assigned" placeholder="Contoh: John Doe / Server Utama">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Departemen / Lokasi</label>
                    <input type="text" name="department" placeholder="Contoh: IT Support / Lantai 2">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Status</label>
                    <select name="status">
                        <option value="Active">Active</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan / Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Catatan tambahan..."></textarea>
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
        <h3>Edit Data IP</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form id="editForm" method="POST">
            <div style="display: flex; flex-wrap: wrap; gap: 2%;">
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>IP Address *</label>
                    <input type="text" name="ip_address" id="edit_ip" required>
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>MAC Address</label>
                    <input type="text" name="mac_address" id="edit_mac">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Kategori Perangkat</label>
                    <select name="device_type" id="edit_device">
                        <option value="PC">PC</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Server">Server</option>
                        <option value="Printer">Printer</option>
                        <option value="Switch">Switch</option>
                        <option value="Router">Router</option>
                        <option value="CCTV">CCTV</option>
                        <option value="Access Point">Access Point</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Nama Pengguna / Assigned To</label>
                    <input type="text" name="user_assigned" id="edit_user">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Departemen / Lokasi</label>
                    <input type="text" name="department" id="edit_dept">
                </div>
                <div class="form-group" style="flex: 0 0 48%;">
                    <label>Status</label>
                    <select name="status" id="edit_status">
                        <option value="Active">Active</option>
                        <option value="Reserved">Reserved</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan / Deskripsi</label>
                <textarea name="description" id="edit_desc" rows="3"></textarea>
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
        <h3>Import Data dari Excel</h3>
        <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
        
        <form action="<?= site_url('ip-management/importExcel') ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Pilih File Excel (.xls, .xlsx, .csv)</label>
                <input type="file" name="file_excel" accept=".xls,.xlsx,.csv" required style="padding: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <a href="<?= site_url('ip-management/downloadTemplate') ?>" style="color: #28a745; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-flex; align-items: center; gap: 5px;">
                    ⬇️ Download Template Excel
                </a>
            </div>
            <div style="background-color:#e2e3e5; color:#383d41; padding:10px; border-radius:4px; font-size:12px; margin-bottom:15px;">
                <b>Format Kolom Excel (Tanpa Header / Dimulai Baris 2):</b><br>
                A: IP Address<br>
                B: MAC Address<br>
                C: Kategori Perangkat (PC, Laptop, Server, dll)<br>
                D: Nama Pengguna / Assigned To<br>
                E: Departemen / Lokasi<br>
                F: Status (Active, Reserved, Inactive)<br>
                G: Keterangan
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
        document.getElementById('editForm').action = "<?= site_url('ip-management/update/') ?>" + data.id;
        document.getElementById('edit_ip').value = data.ip_address;
        document.getElementById('edit_mac').value = data.mac_address || '';
        document.getElementById('edit_device').value = data.device_type || 'PC';
        document.getElementById('edit_user').value = data.user_assigned || '';
        document.getElementById('edit_dept').value = data.department || '';
        document.getElementById('edit_status').value = data.status || 'Active';
        document.getElementById('edit_desc').value = data.description || '';
        
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
    
    function applySort() {
        const sort = document.getElementById('sort-column').value;
        const dir = document.getElementById('btn-sort-dir').getAttribute('data-dir');
        const vlan = '<?= esc($active_vlan) ?>';
        let url = '<?= site_url('ip-management/user-pc') ?>?sort=' + sort + '&dir=' + dir;
        if (vlan) url += '&vlan=' + vlan;
        window.location.href = url;
    }
    
    function toggleDir(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-sort-dir');
        let currentDir = btn.getAttribute('data-dir');
        let newDir = currentDir === 'ASC' ? 'DESC' : 'ASC';
        btn.setAttribute('data-dir', newDir);
        applySort();
    }
</script>

<?= $this->endSection() ?>
