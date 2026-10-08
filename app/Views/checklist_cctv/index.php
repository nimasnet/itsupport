<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .header-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 15px 0 20px 0;
    }
    .btn-bulk-delete {
        padding: 10px 15px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 0;
        transform: scale(0.9);
        pointer-events: none;
    }
    
    /* Hover Image View Pointing */
    .pointing-thumb-container { position: relative; display: inline-block; }
    .pointing-thumb { width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; cursor: pointer; transition: opacity 0.2s; }
    .pointing-thumb:hover { opacity: 0.8; }
    .pointing-large {
        display: none; position: fixed; top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        width: 400px; max-width: 90vw; height: auto;
        border-radius: 12px; box-shadow: 0 15px 50px rgba(0,0,0,0.5);
        border: 3px solid <?= $top_color ?? '#0077b6' ?>;
        z-index: 99999; background: #fff;
    }
    .pointing-thumb-container:hover .pointing-large {
        display: block;
        animation: popUpCenter 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes popUpCenter {
        from { opacity: 0; transform: translate(-50%, -40%) scale(0.8); }
        to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
    .btn-bulk-delete.show { opacity: 1; transform: scale(1); pointer-events: auto; }
    .btn-bulk-delete:hover { background-color: #c82333; }
    
    .btn-action { padding: 8px 15px; font-weight: bold; border-radius: 4px; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
    .btn-primary { background-color: #007bff; color: white; }
    .btn-primary:hover { background-color: #0056b3; }
    .btn-success { background-color: #28a745; color: white; }
    .btn-success:hover { background-color: #218838; }
    .btn-warning { background-color: #ffc107; color: black; }
    .btn-warning:hover { background-color: #e0a800; }
    
    .checkbox-container { display: inline-block; position: relative; cursor: pointer; user-select: none; width: 18px; height: 18px; }
    .checkbox-container input { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .checkmark { position: absolute; top: 0; left: 0; height: 18px; width: 18px; background-color: #fff; border: 2px solid #cbd5e0; border-radius: 4px; transition: all 0.2s; }
    .checkbox-container input:checked ~ .checkmark { background-color: #dc3545; border-color: #dc3545; }
    .checkbox-container input:indeterminate ~ .checkmark { background-color: #a0aec0; border-color: #a0aec0; }
    .checkmark:after { content: ""; position: absolute; display: none; left: 5px; top: 1px; width: 4px; height: 8px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); }
    .checkbox-container input:checked ~ .checkmark:after { display: block; }
    .checkbox-container input:indeterminate ~ .checkmark:after { display: block; left: 5px; top: 2px; width: 6px; height: 6px; background: white; border-radius: 1px; border: none;}

    /* Fitur Tambahan CSS */
    .btn-nvr {
        display: inline-block; padding: 8px 15px; background-color: #e2e8f0; color: #333; text-decoration: none; border-radius: 4px; font-weight: bold; border: 1px solid #ccc; transition: background-color 0.2s; font-size: 13px;
    }
    .btn-nvr:hover { background-color: #cbd5e1; }
    .btn-nvr.active { background-color: <?= $top_color ?>; color: white; border-color: <?= $top_color ?>; }

    .sort-bar {
        display: flex; align-items: center; gap: 10px; padding: 8px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; flex-wrap: wrap;
    }
    .sort-bar label { font-size: 13px; font-weight: 600; color: #475569; white-space: nowrap; }
    .sort-select, .excel-filter-select {
        padding: 6px 10px; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #1e293b; cursor: pointer; outline: none; transition: border-color 0.2s;
    }
    .btn-sort-dir {
        padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; cursor: pointer; font-size: 13px; font-weight: 700; color: #475569; transition: 0.2s;
    }
    .btn-sort-dir:hover { background: #e2e8f0; }

    /* Excel-like filter styling */
    .filter-header { display: flex; flex-direction: column; gap: 4px; }
    .filter-header select { width: 100%; font-weight: normal; max-width: 120px; }

    /* Modal Search Styles */
    .search-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
    .search-modal-overlay.show { display: flex; }
    .search-modal { background: #fff; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); width: 850px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column; animation: modalFadeIn 0.2s ease-out; }
    @keyframes modalFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .modal-header { padding: 15px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border-radius: 8px 8px 0 0; }
    .modal-header h4 { margin: 0; color: #333; font-size: 16px; }
    .btn-close-modal { background: none; border: none; font-size: 22px; cursor: pointer; color: #888; line-height: 1; }
    .modal-body { padding: 20px; overflow-y: auto; flex: 1; }
    .search-bar-container { display: flex; gap: 10px; margin-bottom: 20px; }
    .search-bar-container select, .search-bar-container input { padding: 9px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; outline: none; }
    .search-bar-container input { flex: 1; }
    .search-bar-container button { background: <?= $top_color ?>; color: white; border: none; padding: 0 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .search-results-table { width: 100%; border-collapse: collapse; }
    .search-results-table th, .search-results-table td { border: 1px solid #eee; padding: 10px; text-align: left; font-size: 13px; }
    .search-results-table th { background: #f4f6f8; position: sticky; top: -20px; }

    /* Penyesuaian Tabel Checklist Utama */
    #checklistTable {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px; /* Perkecil ukuran font tabel */
    }
    #checklistTable th, #checklistTable td {
        padding: 6px 8px; /* Perkecil padding agar lebih padat */
        border: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    #checklistTable th {
        background: #f8fafc;
        color: #374151;
        font-weight: 700;
        white-space: nowrap; /* Mencegah header turun ke bawah */
    }
    #checklistTable td {
        color: #475569;
    }
    #checklistTable td.notes-cell {
        min-width: 250px;
        white-space: normal; /* Biarkan catatan membungkus ke baris baru */
    }
    #checklistTable th .filter-header select {
        max-width: 100px;
        padding: 4px 6px;
        font-size: 11px;
    }
</style>

<div class="right-frame">
    <h3>Checklist Status CCTV</h3>
    
    <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; flex-wrap: wrap; gap: 10px; flex: 1;">
            <?php
            $is_all_active = ($active_nvr == '') ? 'active' : '';
            echo '<a href="'.site_url('checklist-cctv').'" class="btn-nvr '.$is_all_active.'">Semua NVR</a>';
            
            if (!empty($nvrList)) {
                foreach($nvrList as $nvr_item) {
                    $nvr_name = esc($nvr_item['nama_nvr']);
                    $is_nvr_active = ($active_nvr == $nvr_item['nama_nvr']) ? 'active' : '';
                    echo '<a href="'.site_url('checklist-cctv').'?nvr='.urlencode($nvr_item['nama_nvr']).'" class="btn-nvr '.$is_nvr_active.'">'.$nvr_name.'</a>';
                }
            }
            ?>
        </div>
    </div>

    <div class="header-action">
        <div>
            <?php if ($can_edit): ?>
            <a href="<?= site_url('checklist-cctv/downloadTemplate') ?>" class="btn-action btn-warning">📄 Download Template</a>
            <button type="button" class="btn-action btn-success" onclick="openImportModal()">📥 Import Excel</button>
            <?php endif; ?>
            <?php
            $exportNvr = !empty($active_nvr) ? '?nvr=' . urlencode($active_nvr) : '';
            $exportUrl = site_url('checklist-cctv/exportExcel') . $exportNvr;
            $exportLabel = !empty($active_nvr) ? 'Export: ' . esc($active_nvr) : '📤 Export Excel';
            ?>
            <a href="javascript:void(0)" onclick="exportData('<?= $exportUrl ?>')" class="btn-action btn-primary"><?= $exportLabel ?></a>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <button id="btn-open-search" class="btn-action" style="background-color: #f39c12; color: white;"><span>🔍</span> Pencarian Lanjut</button>
            <?php if ($can_edit): ?>
            <form id="bulkDeleteForm" action="<?= site_url('checklist-cctv/bulk-delete') ?>" method="post" style="display:inline;">
                <button type="submit" id="btn-bulk-delete" class="btn-bulk-delete" onclick="return confirm('Yakin ingin menghapus data terpilih?');">
                    🗑️ Hapus (<span id="selected-count">0</span>)
                </button>
            </form>
            <a href="<?= site_url('checklist-cctv/create') ?>"><button style="background:<?= $top_color ?>; color:white; border:none; padding:10px 15px; border-radius:4px; font-weight:bold; cursor:pointer;">+ Tambah Data</button></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="sort-bar">
        <label>🔀 Urutkan:</label>
        <select class="sort-select" id="sort-col">
            <option value="tanggal">Tanggal</option>
            <option value="jam">Jam</option>
            <option value="nvr">NVR</option>
            <option value="channel">Channel</option>
            <option value="nama_cctv">Nama CCTV</option>
        </select>
        <button class="btn-sort-dir" id="btn-sort-dir" title="Klik untuk balik urutan">▼ DESC</button>
        <span class="sort-info" id="sort-info">Default: ID DESC</span>
        
        <button type="button" id="btn-reset-filter" class="btn-action btn-warning" style="margin-left:auto; padding: 4px 10px; font-size:12px; display:none;">Reset Excel Filter</button>
    </div>
    
    <div style="overflow-x: auto;">
        <table id="checklistTable">
            <thead>
                <tr>
                    <?php if ($can_edit): ?>
                    <th width="3%">
                        <label class="checkbox-container">
                            <input type="checkbox" id="selectAll">
                            <span class="checkmark"></span>
                        </label>
                    </th>
                    <?php endif; ?>
                    <th>
                        <div class="filter-header">
                            Tanggal
                            <select class="excel-filter-select" data-col="tanggal"><option value="">Semua</option></select>
                        </div>
                    </th>
                    <th>Jam</th>
                    <th>
                        <div class="filter-header">
                            PIC Check
                            <select class="excel-filter-select" data-col="pic_check"><option value="">Semua</option></select>
                        </div>
                    </th>
                    <th>
                        <div class="filter-header">
                            NVR
                            <select class="excel-filter-select" data-col="nvr"><option value="">Semua</option></select>
                        </div>
                    </th>
                    <th>Channel</th>
                    <th>Nama CCTV</th>
                    <th>
                        <div class="filter-header">
                            Status
                            <select class="excel-filter-select" data-col="status"><option value="">Semua</option></select>
                        </div>
                    </th>
                    <th>
                        <div class="filter-header">
                            IT Respon
                            <select class="excel-filter-select" data-col="it_respon"><option value="">Semua</option></select>
                        </div>
                    </th>
                    <th>Notes</th>
                    <th>Documentations</th>
                    <?php if ($can_edit): ?>
                    <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checklists as $c): ?>
                <tr>
                    <?php if ($can_edit): ?>
                    <td>
                        <label class="checkbox-container">
                            <input type="checkbox" class="selectItem" value="<?= $c['id'] ?>" form="bulkDeleteForm" name="ids[]">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <?php endif; ?>
                    <td><?= esc($c['tanggal']) ?></td>
                    <td><?= esc($c['jam']) ?></td>
                    <td><?= esc($c['pic_check']) ?></td>
                    <td><?= esc($c['nvr']) ?></td>
                    <td><?= esc($c['channel']) ?></td>
                    <td><?= esc($c['nama_cctv']) ?></td>
                    <td>
                        <?php 
                        $badgeColor = '#6c757d'; // default gray
                        if (strpos($c['status'], 'Aktif') !== false) $badgeColor = '#28a745';
                        else if (strpos($c['status'], 'Offline') !== false) $badgeColor = '#dc3545';
                        else if (strpos($c['status'], 'Maintenance') !== false) $badgeColor = '#ffc107';
                        ?>
                        <span style="background-color: <?= $badgeColor ?>; color: <?= $badgeColor == '#ffc107' ? '#000' : '#fff' ?>; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; white-space: nowrap; display: inline-block;"><?= esc($c['status']) ?></span>
                    </td>
                    <td class="it-respon-cell" data-id="<?= $c['id'] ?>">
                        <?php
                        $ir = $c['it_respon'] ?? '';
                        $irColor = '#6c757d';
                        if ($ir === 'Solved') $irColor = '#28a745';
                        elseif ($ir === 'On Plan') $irColor = '#17a2b8';
                        elseif ($ir === 'Waiting Device') $irColor = '#ffc107';
                        elseif ($ir === 'Hampered by Equipment') $irColor = '#dc3545';
                        elseif ($ir === 'Authentication Password') $irColor = '#6f42c1';
                        if (session()->get('user') === 'admin'):
                        ?>
                        <div style="display:flex; align-items:center; gap:5px;">
                            <?php if($ir): ?>
                            <span class="it-respon-badge" style="background:<?= $irColor ?>; color:<?= $irColor==='#ffc107'?'#000':'#fff' ?>; padding:3px 7px; border-radius:4px; font-size:11px; font-weight:bold; white-space:nowrap;"><?= esc($ir) ?></span>
                            <?php endif; ?>
                            <button onclick="openItResponModal(<?= $c['id'] ?>, '<?= esc($ir) ?>')"
                                style="background:#4e73df; color:#fff; border:none; border-radius:4px; padding:3px 8px; font-size:11px; cursor:pointer; white-space:nowrap;">
                                <?= $ir ? '✏️ Ubah' : '➕ Respon' ?>
                            </button>
                        </div>
                        <?php else: ?>
                        <?php if($ir): ?>
                        <span style="background:<?= $irColor ?>; color:<?= $irColor==='#ffc107'?'#000':'#fff' ?>; padding:3px 7px; border-radius:4px; font-size:11px; font-weight:bold;"><?= esc($ir) ?></span>
                        <?php else: ?>
                        <?php if(strpos($c['status'], 'Aktif & Record') !== false): ?>
                        <span style="color:#aaa; font-size:12px; font-style:italic;">Not Required</span>
                        <?php else: ?>
                        <span style="color:#dc3545; font-size:12px; font-weight:bold;">Waiting IT Respon</span>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td id="ket-<?= $c['id'] ?>" class="notes-cell" data-id="<?= $c['id'] ?>"><?php
                        $rawNote = $c['keterangan'] ?? '';
                        // Render ** bold ** markers as <strong>
                        echo nl2br(preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', esc($rawNote)));
                    ?></td>
                    <td id="doc-<?= $c['id'] ?>" style="text-align:center; vertical-align:middle;">
                        <?php if (!empty($c['image'])): ?>
                            <?php $imgUrl = base_url('uploads/it_respon/' . $c['image']); ?>
                            <div class="pointing-thumb-container">
                                <img src="<?= $imgUrl ?>" class="pointing-thumb" onclick="openLightbox('<?= $imgUrl ?>')" alt="Foto">
                                <img src="<?= $imgUrl ?>" class="pointing-large" alt="Foto Besar">
                            </div>
                        <?php else: ?>
                            <span style="color:#aaa; font-size:11px; font-style:italic;">-</span>
                        <?php endif; ?>
                    </td>
                    <?php if ($can_edit): ?>
                    <td>
                        <div style="display:flex; gap:4px;">
                            <a href="<?= site_url('checklist-cctv/edit/'.$c['id']) ?>" style="background-color: #ffc107; color: #000; padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 11px; font-weight: bold; white-space: nowrap;">✏️ Edit</a>
                            <a href="<?= site_url('checklist-cctv/delete/'.$c['id']) ?>" onclick="return confirm('Yakin hapus data ini?');" style="background-color: #dc3545; color: white; padding: 4px 8px; text-decoration: none; border-radius: 4px; font-size: 11px; font-weight: bold; white-space: nowrap;">🗑️ Hapus</a>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($checklists)): ?>
                <tr class="no-data">
                    <td colspan="<?= $can_edit ? 11 : 9 ?>" style="text-align: center;">Belum ada data checklist.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Import -->
<div id="importModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div style="background:#fff; margin:10% auto; padding:20px; width:400px; border-radius:8px;">
        <h4 style="margin-top:0;">Import Checklist Status</h4>
        <form action="<?= site_url('checklist-cctv/importExcel') ?>" method="post" enctype="multipart/form-data">
            <div style="margin-bottom:15px;">
                <label>Pilih File Excel (.xls, .xlsx)</label>
                <input type="file" name="file_excel" accept=".xls,.xlsx" required style="width:100%; padding:8px; margin-top:5px;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeImportModal()" class="btn-action" style="background:#ccc;">Batal</button>
                <button type="submit" class="btn-action btn-success">Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pencarian Lanjut -->
<div class="search-modal-overlay" id="searchModal">
    <div class="search-modal">
        <div class="modal-header">
            <h4>Pencarian Lanjut Checklist CCTV</h4>
            <button class="btn-close-modal" id="btnCloseSearch">&times;</button>
        </div>
        <div class="modal-body">
            <div class="search-bar-container">
                <select id="search-by">
                    <option value="all">Semua Kriteria</option>
                    <option value="nama_cctv">Nama CCTV</option>
                    <option value="pic_check">PIC Check</option>
                    <option value="nvr">NVR</option>
                    <option value="status">Status</option>
                    <option value="keterangan">Keterangan</option>
                </select>
                <input type="text" id="search-keyword" placeholder="Ketik kata kunci pencarian..." autocomplete="off">
                <button id="btn-do-search">Cari</button>
            </div>
            <div id="search-loading" style="display: none; text-align: center; padding: 20px; color: #888;">Sedang mencari data...</div>
            <table class="search-results-table" id="search-results-table" style="display: none;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>PIC</th>
                        <th>NVR</th>
                        <th>CCTV</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody id="search-results-body"></tbody>
            </table>
            <div id="search-empty" style="display: none; text-align: center; padding: 20px; color: #888;">Tidak ada data yang ditemukan.</div>
        </div>
    </div>
</div>

<!-- Modal IT Respon Actions -->
<div id="ir-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#fff; border-radius:12px; width:460px; max-width:95vw; box-shadow:0 8px 40px rgba(0,0,0,0.25); overflow:hidden; animation:modalFadeIn 0.2s ease-out;">
        <div style="background:linear-gradient(135deg,#4e73df,#224abe); padding:18px 22px; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span style="font-size:22px;">🔧</span>
                <div>
                    <h4 style="margin:0; color:#fff; font-size:16px;">IT Respon Actions</h4>
                    <p style="margin:0; color:#c8d8ff; font-size:12px;">Catat tindakan penanganan CCTV bermasalah</p>
                </div>
            </div>
            <button onclick="closeItResponModal()" style="background:rgba(255,255,255,0.2); border:none; color:#fff; width:30px; height:30px; border-radius:50%; font-size:18px; cursor:pointer; display:flex; align-items:center; justify-content:center;">&times;</button>
        </div>
        <div style="padding:22px;">
            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:700; color:#374151; display:block; margin-bottom:6px;">Status IT Respon <span style="color:#ef4444;">*</span></label>
                <select id="ir-select" style="width:100%; padding:10px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:14px; color:#1f2937; outline:none;">
                    <option value="">-- Pilih Status --</option>
                    <option value="Solved">✅ Solved</option>
                    <option value="On Plan">📋 On Plan</option>
                    <option value="Waiting Device">⏳ Waiting Device</option>
                    <option value="Hampered by Equipment">🚫 Hampered by Equipment</option>
                    <option value="Authentication Password">🔑 Authentication Password</option>
                    <option value="Already checked but unresolved">⚠️ Already checked but unresolved</option>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                <div>
                    <label style="font-size:13px; font-weight:700; color:#374151; display:block; margin-bottom:6px;">📅 Tanggal <span style="color:#ef4444;">*</span></label>
                    <input type="date" id="ir-date" style="width:100%; padding:10px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:14px; outline:none;">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:700; color:#374151; display:block; margin-bottom:6px;">⏰ Jam <span style="color:#ef4444;">*</span></label>
                    <input type="time" id="ir-time" style="width:100%; padding:10px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:14px; outline:none;">
                </div>
            </div>

            <!-- ===== IMAGE ===== -->
            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:700; color:#374151; display:block; margin-bottom:6px;">🖼️ Image <span style="font-size:11px; font-weight:normal; color:#6b7280; background:#f3f4f6; padding:2px 6px; border-radius:4px; margin-left:4px;">Opsional</span></label>

                <div class="option-tabs" style="display:flex; background:#f8fafc; border-radius:8px; padding:4px; margin-bottom:10px; border:1px solid #e2e8f0;">
                    <button type="button" class="option-tab active" id="tab-img-file" onclick="switchImageTab('file')" style="flex:1; padding:8px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; border:none; background:linear-gradient(135deg, #4e73df, #224abe); color:#fff; transition:all 0.2s;">
                        📁 Pilih File
                    </button>
                    <button type="button" class="option-tab" id="tab-img-camera" onclick="switchImageTab('camera')" style="flex:1; padding:8px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer; border:none; background:transparent; color:#64748b; transition:all 0.2s;">
                        📷 Camera
                    </button>
                </div>

                <!-- Option 1: File -->
                <div id="img-file-panel">
                    <div class="img-upload-area" id="file-drop-area" style="border:2px dashed #93c5fd; border-radius:12px; background:#f0f9ff; padding:20px; text-align:center; cursor:pointer; position:relative;">
                        <input type="file" name="image" id="image-file-input" accept="image/*" onchange="previewImageFile(this)" style="position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;">
                        <div style="font-size:28px; margin-bottom:4px;">📂</div>
                        <p style="margin:0; font-size:12px; color:#64748b;"><strong>Klik atau drag & drop</strong> gambar ke sini</p>
                        <p style="font-size:11px; color:#94a3b8; margin-top:4px;">JPG, PNG, WEBP — Maks. 5MB</p>
                    </div>
                    <div id="file-preview-box" style="margin-top:12px; border-radius:10px; overflow:hidden; display:none; position:relative;">
                        <img id="file-preview-img" src="" alt="Preview" style="width:100%; max-height:200px; object-fit:cover; display:block;">
                        <button type="button" onclick="removeFilePreview()" style="position:absolute; top:8px; right:8px; background:rgba(220,38,38,0.85); color:#fff; border:none; border-radius:20px; padding:4px 10px; font-size:12px; cursor:pointer; font-weight:600;">✕ Hapus</button>
                    </div>
                </div>

                <!-- Option 2: Camera -->
                <div id="img-camera-panel" style="display:none;">
                    <div style="border:1.5px dashed #93c5fd; border-radius:12px; background:#eff6ff; padding:16px; text-align:center;">
                        <video id="video-image" playsinline autoplay style="width:100%; max-height:220px; object-fit:cover; border-radius:8px; display:none; border:2px solid #3b82f6;"></video>
                        <canvas id="canvas-image" style="display:none;"></canvas>
                        <img id="photo-image-preview" alt="Foto IT Respon" style="width:100%; max-height:200px; object-fit:cover; border-radius:8px; display:none; border:2px solid #16a34a; margin-top:8px;">

                        <div id="cam-img-placeholder" style="padding:10px;">
                            <div style="font-size:28px; margin-bottom:4px;">📷</div>
                            <p style="font-size:12px; color:#64748b; margin:0;">Gunakan kamera perangkat Anda<br>untuk mengambil foto kerusakan.</p>
                        </div>

                        <div style="margin-top:10px; display:flex; flex-wrap:wrap; justify-content:center; gap:6px;">
                            <button type="button" class="cam-btn" onclick="startCamera('image')" style="padding:6px 12px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">📷 Buka Kamera</button>
                            <button type="button" class="cam-btn" id="capture-img-btn" onclick="capturePhoto('image')" style="display:none; padding:6px 12px; background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">📸 Ambil Foto</button>
                            <button type="button" class="cam-btn" id="stop-img-btn" onclick="stopCamera('image')" style="display:none; padding:6px 12px; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">⏹ Stop</button>
                            <button type="button" class="cam-btn" id="retake-img-btn" onclick="retakePhoto('image')" style="display:none; padding:6px 12px; background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">🔄 Ulangi</button>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="camera_image_data">
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:13px; font-weight:700; color:#374151; display:block; margin-bottom:6px;">💬 Komentar Perbaikan <span style="color:#ef4444;">*</span></label>
                <textarea id="ir-note" rows="4" placeholder="Tuliskan detail tindakan perbaikan yang dilakukan..." style="width:100%; padding:10px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:14px; resize:vertical; outline:none; font-family:inherit;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button onclick="closeItResponModal()" style="padding:10px 20px; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">Batal</button>
                <button id="ir-save-btn" style="padding:10px 24px; background:linear-gradient(135deg,#4e73df,#224abe); color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">💾 Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox IT Respon Actions -->
<div id="lightbox-ir" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:10001; align-items:center; justify-content:center; backdrop-filter:blur(5px);">
    <button onclick="closeLightbox()" style="position:absolute; top:20px; right:30px; background:transparent; border:none; color:#fff; font-size:40px; cursor:pointer;">&times;</button>
    <img id="lightbox-ir-img" src="" style="max-width:90%; max-height:90vh; border-radius:8px; box-shadow:0 10px 50px rgba(0,0,0,0.5);">
</div>

<script>
    function openImportModal() {
        document.getElementById('importModal').style.display = 'block';
    }
    
    function closeImportModal() {
        document.getElementById('importModal').style.display = 'none';
    }

    const selectAll = document.getElementById('selectAll');
    const selectItems = document.querySelectorAll('.selectItem');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const selectedCount = document.getElementById('selected-count');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.selectItem:not([style*="display: none"])').forEach(item => item.checked = this.checked);
            updateBulkDeleteButton();
        });

        selectItems.forEach(item => {
            item.addEventListener('change', function() {
                updateBulkDeleteButton();
            });
        });
    }

    function updateBulkDeleteButton() {
        const checkedItems = document.querySelectorAll('.selectItem:checked:not([style*="display: none"])');
        const count = checkedItems.length;
        if(selectedCount) selectedCount.textContent = count;

        if (btnBulkDelete) {
            if (count > 0) {
                btnBulkDelete.classList.add('show');
            } else {
                btnBulkDelete.classList.remove('show');
            }
        }
    }

    // Advanced Search
    const searchModal = document.getElementById('searchModal');
    const btnOpenSearch = document.getElementById('btn-open-search');
    const btnCloseSearch = document.getElementById('btnCloseSearch');
    const btnDoSearch = document.getElementById('btn-do-search');
    const searchKeyword = document.getElementById('search-keyword');
    const searchBy = document.getElementById('search-by');
    const searchResultsBody = document.getElementById('search-results-body');
    const searchResultsTable = document.getElementById('search-results-table');
    const searchLoading = document.getElementById('search-loading');
    const searchEmpty = document.getElementById('search-empty');

    if (btnOpenSearch) {
        btnOpenSearch.addEventListener('click', () => { searchModal.classList.add('show'); searchKeyword.focus(); });
    }
    if (btnCloseSearch) {
        btnCloseSearch.addEventListener('click', () => searchModal.classList.remove('show'));
    }
    if (searchModal) {
        searchModal.addEventListener('click', (e) => { if(e.target === searchModal) searchModal.classList.remove('show'); });
    }
    if (searchKeyword) {
        searchKeyword.addEventListener('keypress', (e) => { if (e.key === 'Enter') performSearch(); });
    }
    if (btnDoSearch) {
        btnDoSearch.addEventListener('click', performSearch);
    }

    function performSearch() {
        const keyword = searchKeyword.value.trim();
        if (!keyword) return;
        
        searchResultsTable.style.display = 'none';
        searchEmpty.style.display = 'none';
        searchLoading.style.display = 'block';
        searchResultsBody.innerHTML = '';

        const activeNvr = '<?= esc($active_nvr) ?>';
        
        fetch(`<?= site_url('checklist-cctv/search_api') ?>?keyword=${encodeURIComponent(keyword)}&by=${encodeURIComponent(searchBy.value)}&nvr=${encodeURIComponent(activeNvr)}`)
        .then(res => res.json())
        .then(res => {
            searchLoading.style.display = 'none';
            if(res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(item => {
                    html += `<tr>
                        <td>${item.tanggal} ${item.jam}</td>
                        <td>${item.pic_check}</td>
                        <td>${item.nvr}</td>
                        <td>${item.nama_cctv}</td>
                        <td>${item.status}</td>
                        <td>${item.keterangan || ''}</td>
                    </tr>`;
                });
                searchResultsBody.innerHTML = html;
                searchResultsTable.style.display = 'table';
            } else {
                searchEmpty.style.display = 'block';
            }
        }).catch(() => {
            searchLoading.style.display = 'none';
            searchEmpty.style.display = 'block';
            searchEmpty.textContent = 'Terjadi kesalahan jaringan.';
        });
    }

    // Sort Bar Logic
    const sortSelect = document.getElementById('sort-col');
    const sortDirBtn = document.getElementById('btn-sort-dir');
    const sortInfo = document.getElementById('sort-info');
    const tbody = document.querySelector('#checklistTable tbody');
    let sortDir = 'desc';

    const colMap = { 'tanggal': 1, 'jam': 2, 'pic_check': 3, 'nvr': 4, 'channel': 5, 'nama_cctv': 6, 'status': 7, 'it_respon': 8 };
    const colOffset = <?= $can_edit ? 0 : -1 ?>; 

    function sortTable() {
        const colKey = sortSelect.value;
        const colIdx = colMap[colKey] + colOffset;
        const rows = Array.from(tbody.querySelectorAll('tr:not(.no-data)'));

        rows.sort((a, b) => {
            let tdA = a.querySelectorAll('td')[colIdx];
            let tdB = b.querySelectorAll('td')[colIdx];
            if (!tdA || !tdB) return 0;
            let valA = tdA.textContent.trim().toLowerCase();
            let valB = tdB.textContent.trim().toLowerCase();
            
            let cmp = valA.localeCompare(valB, 'id', {numeric: true});
            return sortDir === 'asc' ? cmp : -cmp;
        });

        rows.forEach(tr => tbody.appendChild(tr));
        const dirLabel = sortDir === 'asc' ? '▲ ASC' : '▼ DESC';
        sortDirBtn.textContent = dirLabel;
        sortInfo.textContent = 'Urut: ' + sortSelect.options[sortSelect.selectedIndex].text + ' ' + dirLabel;
    }

    if (sortSelect && sortDirBtn) {
        sortSelect.addEventListener('change', sortTable);
        sortDirBtn.addEventListener('click', () => {
            sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            sortTable();
        });
    }

    // Excel-like Column Filters
    const filterSelects = document.querySelectorAll('.excel-filter-select');
    const btnResetFilter = document.getElementById('btn-reset-filter');
    
    // Populate unique values into dropdowns
    function populateFilters() {
        const rows = Array.from(tbody.querySelectorAll('tr:not(.no-data)'));
        
        filterSelects.forEach(select => {
            const colKey = select.dataset.col;
            const colIdx = colMap[colKey] + colOffset;
            let uniqueVals = new Set();
            
            rows.forEach(tr => {
                let td = tr.querySelectorAll('td')[colIdx];
                if (td) uniqueVals.add(td.textContent.trim());
            });
            
            let sortedVals = Array.from(uniqueVals).sort();
            
            // Keep the "Semua" option
            select.innerHTML = '<option value="">Semua</option>';
            sortedVals.forEach(val => {
                if(val) {
                    let opt = document.createElement('option');
                    opt.value = val;
                    opt.textContent = val;
                    select.appendChild(opt);
                }
            });
            
            select.addEventListener('change', applyExcelFilters);
        });
    }

    function applyExcelFilters() {
        const rows = Array.from(tbody.querySelectorAll('tr:not(.no-data)'));
        let hasFilter = false;
        
        // Get active filters
        let activeFilters = [];
        filterSelects.forEach(select => {
            if (select.value !== "") {
                activeFilters.push({
                    colIdx: colMap[select.dataset.col] + colOffset,
                    val: select.value
                });
                hasFilter = true;
            }
        });
        
        btnResetFilter.style.display = hasFilter ? 'inline-block' : 'none';
        
        let visibleCount = 0;
        rows.forEach(tr => {
            let isMatch = true;
            activeFilters.forEach(f => {
                let td = tr.querySelectorAll('td')[f.colIdx];
                if (td && td.textContent.trim() !== f.val) {
                    isMatch = false;
                }
            });
            tr.style.display = isMatch ? '' : 'none';
            
            // hide checkbox if row is hidden so it doesn't get affected by select all
            let cb = tr.querySelector('.selectItem');
            if (cb) cb.style.display = isMatch ? '' : 'none';
            
            if (isMatch) visibleCount++;
        });
        
        updateBulkDeleteButton();
    }
    
    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', () => {
            filterSelects.forEach(s => s.value = "");
            applyExcelFilters();
        });
    }

    // Initialize
    setTimeout(() => {
        populateFilters();
    }, 100);
    function exportData(defaultUrl) {
        const checkedItems = document.querySelectorAll('.selectItem:checked:not([style*="display: none"])');
        
        if (checkedItems.length > 0) {
            const selectedIds = Array.from(checkedItems).map(cb => cb.value);
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= site_url('checklist-cctv/exportExcel') ?>';
            
            const inputIds = document.createElement('input');
            inputIds.type = 'hidden';
            inputIds.name = 'ids';
            inputIds.value = selectedIds.join(',');
            
            if (defaultUrl.includes('?')) {
                const urlParams = new URLSearchParams(defaultUrl.split('?')[1]);
                if (urlParams.has('nvr')) {
                    const inputNvr = document.createElement('input');
                    inputNvr.type = 'hidden';
                    inputNvr.name = 'nvr';
                    inputNvr.value = urlParams.get('nvr');
                    form.appendChild(inputNvr);
                }
            }
            
            form.appendChild(inputIds);
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        } else {
            window.location.href = defaultUrl;
        }
    }

    // ===================== IT RESPON MODAL =====================
    let currentItResponId = null;

    function openItResponModal(id, currentRespon) {
        currentItResponId = id;
        const now = new Date();
        document.getElementById('ir-date').value = now.toISOString().split('T')[0];
        document.getElementById('ir-time').value = now.toTimeString().slice(0,5);
        document.getElementById('ir-select').value = currentRespon || '';
        document.getElementById('ir-note').value = '';

        // Reset image states
        removeFilePreview();
        if(document.getElementById('retake-img-btn').style.display !== 'none') {
            retakePhoto('image');
            stopCamera('image');
        }
        switchImageTab('file');

        document.getElementById('ir-modal').style.display = 'flex';
    }

    function closeItResponModal() {
        document.getElementById('ir-modal').style.display = 'none';
        currentItResponId = null;
        stopCamera('image');
    }

    document.getElementById('ir-save-btn').addEventListener('click', function() {
        const it_respon   = document.getElementById('ir-select').value;
        const respon_date = document.getElementById('ir-date').value;
        const respon_time = document.getElementById('ir-time').value;
        const respon_note = document.getElementById('ir-note').value.trim();

        if (!it_respon) {
            Swal.fire({ icon: 'warning', title: 'Pilih status IT Respon terlebih dahulu!' });
            return;
        }
        if (!respon_note) {
            Swal.fire({ icon: 'warning', title: 'Komentar perbaikan tidak boleh kosong!' });
            return;
        }

        const formData = new FormData();
        formData.append('id', currentItResponId);
        formData.append('it_respon', it_respon);
        formData.append('respon_date', respon_date);
        formData.append('respon_time', respon_time);
        formData.append('respon_note', respon_note);

        if (imageMode === 'file') {
            const fileInput = document.getElementById('image-file-input');
            if (fileInput.files.length > 0) {
                formData.append('image_file', fileInput.files[0]);
            }
        } else if (imageMode === 'camera') {
            const camData = document.getElementById('camera_image_data').value;
            if (camData) {
                formData.append('camera_image', camData);
            }
        }

        fetch('<?= site_url('checklist-cctv/update-it-respon') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status === 'success') {
                closeItResponModal();
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message, timer: 1500, showConfirmButton: false });

                // Update badge IT Respon di baris tabel
                const irCell = document.querySelector(`.it-respon-cell[data-id='${currentItResponId}']`);
                if (irCell) {
                    const colorMap = { 'Solved': '#28a745', 'On Plan': '#17a2b8', 'Waiting Device': '#ffc107', 'Hampered by Equipment': '#dc3545', 'Authentication Password': '#6f42c1' };
                    const c = colorMap[it_respon] || '#6c757d';
                    const textColor = c === '#ffc107' ? '#000' : '#fff';
                    irCell.querySelector('div').innerHTML = `
                        <span class="it-respon-badge" style="background:${c};color:${textColor};padding:3px 7px;border-radius:4px;font-size:11px;font-weight:bold;white-space:nowrap;">${it_respon}</span>
                        <button onclick="openItResponModal(${currentItResponId}, '${it_respon}')" style="background:#4e73df;color:#fff;border:none;border-radius:4px;padding:3px 8px;font-size:11px;cursor:pointer;white-space:nowrap;">✏️ Ubah</button>
                    `;
                }

                // Update kolom Notes di baris tabel
                const cellKeterangan = document.getElementById('ket-' + currentItResponId);
                if (cellKeterangan) {
                    let oldHtml = cellKeterangan.innerHTML;
                    if (oldHtml.includes('--- IT Respon Update ---')) {
                        cellKeterangan.innerHTML = oldHtml + '<br>--- IT Respon Update ---<br>' + res.keterangan.split('--- IT Respon Update ---').pop().replace(/\n/g, '<br>');
                    } else {
                        cellKeterangan.innerHTML = oldHtml + (oldHtml ? '<br>--- IT Respon Update ---<br>' : '') + res.keterangan.replace(/\n/g, '<br>');
                    }
                }

                const cellDoc = document.getElementById('doc-' + currentItResponId);
                if (cellDoc && res.image) {
                    const imgUrl = "<?= base_url('uploads/it_respon/') ?>" + res.image;
                    cellDoc.innerHTML = `<div class="pointing-thumb-container">
                        <img src="${imgUrl}" class="pointing-thumb" onclick="openLightbox('${imgUrl}')" alt="Foto">
                        <img src="${imgUrl}" class="pointing-large" alt="Foto Besar">
                    </div>`;
                }
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
            }
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Terjadi kesalahan jaringan.' }));
    });

    document.getElementById('ir-modal').addEventListener('click', function(e) {
        if (e.target === this) closeItResponModal();
    });

    // =================== IMAGE TAB & CAMERA ===================
    let imageMode = 'file';
    let camStream = null;

    function switchImageTab(mode) {
        imageMode = mode;
        const btnFile = document.getElementById('tab-img-file');
        const btnCam = document.getElementById('tab-img-camera');
        
        if (mode === 'file') {
            btnFile.classList.add('active');
            btnFile.style.background = 'linear-gradient(135deg, #4e73df, #224abe)';
            btnFile.style.color = '#fff';
            
            btnCam.classList.remove('active');
            btnCam.style.background = 'transparent';
            btnCam.style.color = '#64748b';
            
            document.getElementById('img-file-panel').style.display = '';
            document.getElementById('img-camera-panel').style.display = 'none';
            stopCamera('image');
        } else {
            btnCam.classList.add('active');
            btnCam.style.background = 'linear-gradient(135deg, #4e73df, #224abe)';
            btnCam.style.color = '#fff';
            
            btnFile.classList.remove('active');
            btnFile.style.background = 'transparent';
            btnFile.style.color = '#64748b';
            
            document.getElementById('img-file-panel').style.display = 'none';
            document.getElementById('img-camera-panel').style.display = '';
        }
    }

    function previewImageFile(input) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('file-preview-img').src = e.target.result;
            document.getElementById('file-preview-box').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    function removeFilePreview() {
        document.getElementById('image-file-input').value = '';
        document.getElementById('file-preview-img').src = '';
        document.getElementById('file-preview-box').style.display = 'none';
    }

    async function startCamera(type) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Browser Anda tidak mendukung akses kamera.' });
            return;
        }
        if (camStream) {
            camStream.getTracks().forEach(t => t.stop());
            camStream = null;
        }

        const constraintsList = [
            { video: { facingMode: { ideal: 'environment' } }, audio: false },
            { video: { facingMode: { ideal: 'user' } }, audio: false },
            { video: true, audio: false }
        ];

        let stream = null;
        for (const constraints of constraintsList) {
            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                break;
            } catch (err) {}
        }

        if (!stream) {
            Swal.fire({ icon: 'error', title: 'Akses Ditolak', text: 'Tidak dapat mengakses kamera.' });
            return;
        }

        camStream = stream;
        const video = document.getElementById('video-image');
        video.srcObject = stream;
        video.style.display = 'block';
        
        document.getElementById('cam-img-placeholder').style.display = 'none';
        document.getElementById('photo-image-preview').style.display = 'none';
        
        document.querySelector('button[onclick="startCamera(\'image\')"]').style.display = 'none';
        document.getElementById('capture-img-btn').style.display = 'inline-flex';
        document.getElementById('stop-img-btn').style.display = 'inline-flex';
        document.getElementById('retake-img-btn').style.display = 'none';
    }

    function capturePhoto(type) {
        const video = document.getElementById('video-image');
        const canvas = document.getElementById('canvas-image');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL('image/png');

        stopCamera(type);

        const preview = document.getElementById('photo-image-preview');
        preview.src = dataUrl;
        preview.style.display = 'block';
        
        document.getElementById('retake-img-btn').style.display = 'inline-flex';
        document.getElementById('camera_image_data').value = dataUrl;
        
        document.querySelector('button[onclick="startCamera(\'image\')"]').style.display = 'none';
    }

    function stopCamera(type) {
        if (camStream) {
            camStream.getTracks().forEach(t => t.stop());
            camStream = null;
        }
        const video = document.getElementById('video-image');
        if (video) {
            video.srcObject = null;
            video.style.display = 'none';
        }
        document.getElementById('capture-img-btn').style.display = 'none';
        document.getElementById('stop-img-btn').style.display = 'none';
        document.querySelector('button[onclick="startCamera(\'image\')"]').style.display = 'inline-flex';
    }

    function retakePhoto(type) {
        document.getElementById('photo-image-preview').style.display = 'none';
        document.getElementById('retake-img-btn').style.display = 'none';
        document.getElementById('cam-img-placeholder').style.display = 'block';
        document.getElementById('camera_image_data').value = '';
        startCamera(type);
    }

    // =================== LIGHTBOX ===================
    function openLightbox(src) {
        document.getElementById('lightbox-ir-img').src = src;
        document.getElementById('lightbox-ir').style.display = 'flex';
    }
    function closeLightbox() {
        document.getElementById('lightbox-ir').style.display = 'none';
    }
    document.getElementById('lightbox-ir').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });
</script>

<?= $this->endSection() ?>
