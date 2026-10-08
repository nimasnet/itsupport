<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
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
    }
    .btn-nvr:hover { background-color: #cbd5e1; }
    .btn-nvr.active { background-color: <?= $top_color ?>; color: white; border-color: <?= $top_color ?>; }
    /* Modal Search Styles */
    .search-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(2px);
    }
    .search-modal-overlay.show { display: flex; }
    .search-modal {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        width: 850px;
        max-width: 95vw;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        animation: modalFadeIn 0.2s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
        border-radius: 8px 8px 0 0;
    }
    .modal-header h4 { margin: 0; color: #333; font-size: 16px; }
    .btn-close-modal {
        background: none; border: none; font-size: 22px; cursor: pointer; color: #888; line-height: 1;
    }
    .btn-close-modal:hover { color: #d32f2f; }
    
    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }
    .search-bar-container {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    .search-bar-container select, .search-bar-container input {
        padding: 9px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        outline: none;
    }
    .search-bar-container select:focus, .search-bar-container input:focus {
        border-color: <?= $top_color ?>;
    }
    .search-bar-container input {
        flex: 1;
    }
    .search-bar-container button {
        background: <?= $top_color ?>;
        color: white; border: none; padding: 0 20px; border-radius: 4px; cursor: pointer; font-weight: bold; transition: opacity 0.2s;
    }
    .search-bar-container button:hover { opacity: 0.85; }
    
    .search-results-table {
        width: 100%; border-collapse: collapse;
    }
    .search-results-table th, .search-results-table td {
        border: 1px solid #eee; padding: 10px; text-align: left; font-size: 13px;
    }
    .search-results-table th { background: #f4f6f8; color: #555; position: sticky; top: -20px; }
    .action-links a { margin-right: 8px; text-decoration: none; font-weight: bold; }
    .action-edit { color: #0077b6; }
    .action-delete { color: #dc3545; }

    /* Bulk Delete and Checkbox Styles */
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
        box-shadow: 0 2px 5px rgba(220, 53, 69, 0.2);
    }
    .btn-bulk-delete.show { opacity: 1; transform: scale(1); pointer-events: auto; }
    .btn-bulk-delete:hover { background-color: #c82333; box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4); }

    .checkbox-container { display: inline-block; position: relative; cursor: pointer; user-select: none; width: 18px; height: 18px; }
    .checkbox-container input { position: absolute; opacity: 0; cursor: pointer; height: 0; width: 0; }
    .checkmark { position: absolute; top: 0; left: 0; height: 18px; width: 18px; background-color: #fff; border: 2px solid #cbd5e0; border-radius: 4px; transition: all 0.2s; }
    .checkbox-container:hover input ~ .checkmark { border-color: #a0aec0; }
    .checkbox-container input:checked ~ .checkmark { background-color: #dc3545; border-color: #dc3545; }
    .checkbox-container input:indeterminate ~ .checkmark { background-color: #a0aec0; border-color: #a0aec0; }
    .checkmark:after { content: ""; position: absolute; display: none; }
    .checkbox-container input:checked ~ .checkmark:after { display: block; }
    .checkbox-container input:indeterminate ~ .checkmark:after { display: block; left: 5px; top: 2px; width: 6px; height: 6px; background: white; border-radius: 1px; }
    .checkbox-container .checkmark:after { left: 5px; top: 1px; width: 4px; height: 8px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); }

    .popup-balloon {
        position: absolute; display: none; z-index: 1000; background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(8px); border: 1px solid rgba(0, 0, 0, 0.15); border-radius: 10px;
        padding: 14px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); width: 260px;
        transition: opacity 0.2s, transform 0.2s; opacity: 0; transform: scale(0.9) translateY(10px); pointer-events: none;
    }
    .popup-balloon.show { opacity: 1; transform: scale(1) translateY(0); pointer-events: auto; display: block; }
    .balloon-arrow { position: absolute; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; }
    .popup-balloon.arrow-down .balloon-arrow { bottom: -8px; left: 50%; transform: translateX(-50%); border-top: 8px solid rgba(255, 255, 255, 0.98); }
    .popup-balloon.arrow-up .balloon-arrow { top: -8px; left: 50%; transform: translateX(-50%); border-bottom: 8px solid rgba(255, 255, 255, 0.98); }
    .balloon-content p { font-size: 13.5px; color: #2d3748; margin-bottom: 12px; line-height: 1.4; font-weight: 500; }
    .balloon-actions { display: flex; justify-content: flex-end; gap: 8px; }
    .btn-balloon-danger { padding: 5px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; }
    .btn-balloon-danger:hover { background: #c82333; }
    .btn-balloon-secondary { padding: 5px 12px; background: #f0f2f5; color: #4a5568; border: 1px solid #ccd0d5; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; }
    .btn-balloon-secondary:hover { background: #e4e6e9; }

    /* ===== Sort Bar ===== */
    .sort-bar {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 14px; background: #f8fafc;
        border: 1px solid #e2e8f0; border-radius: 8px;
        margin-bottom: 10px; flex-wrap: wrap;
    }
    .sort-bar label { font-size: 13px; font-weight: 600; color: #475569; white-space: nowrap; }
    .sort-select {
        padding: 6px 10px; font-size: 13px;
        border: 1px solid #cbd5e1; border-radius: 6px;
        background: #fff; color: #1e293b; cursor: pointer;
        outline: none; transition: border-color 0.2s;
    }
    .sort-select:focus { border-color: <?= $top_color ?>; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .btn-sort-dir {
        padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px;
        background: #fff; cursor: pointer; font-size: 13px; font-weight: 700;
        color: #475569; transition: 0.2s;
    }
    .btn-sort-dir:hover { background: #e2e8f0; }
    .sort-info { font-size: 12px; color: #94a3b8; margin-left: auto; }

    /* Hover Image View Pointing */
    .pointing-thumb-container {
        position: relative;
        display: inline-block;
    }
    .pointing-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ccc;
        cursor: crosshair;
        transition: opacity 0.2s;
    }
    .pointing-thumb:hover {
        opacity: 0.8;
    }
    .pointing-large {
        display: none;
        position: fixed; /* Berubah jadi fixed agar melayang di tengah layar */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        width: 400px; /* Diperbesar sedikit */
        max-width: 90vw;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.5);
        border: 3px solid <?= $top_color ?>;
        z-index: 99999;
        background: #fff;
    }
    .pointing-thumb-container:hover .pointing-large {
        display: block;
        animation: popUpCenter 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    @keyframes popUpCenter {
        from { opacity: 0; transform: translate(-50%, -40%) scale(0.8); }
        to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
</style>

<div class="right-frame">
    <h3>Data CCTV Berdasarkan NVR</h3><br>

    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">
        <div style="display: flex; flex-wrap: wrap; gap: 10px; flex: 1;">
            <?php
            $is_all_active = ($active_nvr == '') ? 'active' : '';
            echo '<a href="'.site_url('cctv/nvr').'" class="btn-nvr '.$is_all_active.'">Semua NVR</a>';
            
            if (!empty($nvrList)) {
                foreach($nvrList as $nvr) {
                    $nvr_name = esc($nvr['nama_nvr']);
                    $is_nvr_active = ($active_nvr == $nvr['nama_nvr']) ? 'active' : '';
                    echo '<a href="'.site_url('cctv/nvr').'?nvr='.urlencode($nvr['nama_nvr']).'" class="btn-nvr '.$is_nvr_active.'">'.$nvr_name.'</a>';
                }
            }
            ?>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <?php if ($page_perm !== 'R'): ?>
            <button id="btn-bulk-delete" class="btn-bulk-delete">🗑️ Hapus (<span id="selected-count">0</span>)</button>
            <?php
            $exportNvr  = !empty($active_nvr) ? urlencode($active_nvr) : '';
            $exportUrl  = $exportNvr
                ? site_url('cctv/exportExcel/' . $exportNvr)
                : site_url('cctv/exportExcel');
            $exportLabel = !empty($active_nvr) ? 'Export: ' . esc($active_nvr) : 'Export Semua NVR';
            ?>
            <a href="<?= $exportUrl ?>" class="btn-nvr" style="background-color:#0077b6; color:white; padding:8px 15px; text-decoration:none; display:flex; align-items:center; gap:6px; font-weight:bold; box-shadow:0 2px 4px rgba(0,0,0,0.1); border-radius:4px; border:none;" title="Export hanya data NVR yang sedang aktif">
                <span>📤</span> <?= $exportLabel ?>
            </a>
            <?php endif; ?>
            <button id="btn-open-search" style="background-color: #f39c12; color: white; border: none; padding: 8px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <span>🔍</span> Pencarian Lanjut
            </button>
        </div>
    </div>

    <!-- Sort Bar -->
    <div class="sort-bar">
        <label>🔀 Urutkan:</label>
        <select class="sort-select" id="sort-col">
            <option value="no">No</option>
            <option value="nvr">NVR</option>
            <option value="ip">IP Address</option>
            <option value="nama">Nama CCTV</option>
            <option value="posisi">Posisi</option>
            <option value="channel" selected>Channel</option>
        </select>
        <button class="btn-sort-dir" id="btn-sort-dir" title="Klik untuk balik urutan">▲ ASC</button>
        <span class="sort-info" id="sort-info">Default: Channel ▲</span>
    </div>

    <hr style="margin: 10px 0;">

    <table style="width: 100%;">
        <thead>
            <tr>
                <?php if ($page_perm !== 'R'): ?>
                <th style="width: 45px; text-align: center;">
                    <label class="checkbox-container">
                        <input type="checkbox" id="selectAll">
                        <span class="checkmark"></span>
                    </label>
                </th>
                <?php endif; ?>
                <th width="5%">No</th>
                <th>NVR</th>
                <th>IP Address</th>
                <th>Nama CCTV</th>
                <th>Posisi</th>
                <th>Channel</th>
                <th>View Pointing</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            
            if (!empty($cctvData)) {
                foreach($cctvData as $row) {
                    echo "<tr>";
                    if ($page_perm !== 'R') {
                        echo "<td style='text-align: center;'>
                            <label class='checkbox-container'>
                                <input type='checkbox' class='select-item' value='".$row['id']."'>
                                <span class='checkmark'></span>
                            </label>
                        </td>";
                    }
                    echo "<td>".$no++."</td>
                        <td><b>".esc($row['nvr'])."</b></td>
                        <td>".esc($row['ip_address'])."</td>
                        <td>".esc($row['nama_cctv'])."</td>
                        <td>".esc($row['posisi'])."</td>
                        <td>Ch ".esc($row['channel'])."</td>
                        <td style='text-align:center;'>";
                    
                    if (!empty($row['view_pointing_image'])) {
                        $imgUrl = base_url('uploads/cctv_pointing/' . esc($row['view_pointing_image']));
                        echo "<div class='pointing-thumb-container'>
                                <img src='{$imgUrl}' class='pointing-thumb' alt='View'>
                                <img src='{$imgUrl}' class='pointing-large' alt='View Large'>
                              </div>";
                    } else {
                        echo "<span style='color:#ccc; font-size:11px; font-style:italic;'>No View</span>";
                    }
                    
                    echo "</td>
                        <td>";
                    if ($page_perm !== 'R') {
                        echo "<a href='".site_url('cctv/edit/'.$row['id'])."' class='btn-edit'>Edit</a> | 
                              <a href='javascript:void(0)' class='btn-hapus' data-id='".$row['id']."' data-ip='".esc($row['ip_address'])."'>Hapus</a>";
                    } else {
                        echo "<span style='color:#999; font-size:12px; font-style:italic;'>Akses Baca</span>";
                    }
                    echo "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='".($page_perm !== 'R' ? 9 : 8)."' style='text-align:center;'>Belum ada data CCTV untuk NVR ini.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div id="delete-balloon" class="popup-balloon">
        <div class="balloon-arrow"></div>
        <div class="balloon-content">
            <p id="balloon-message">Apakah Anda yakin ingin menghapus data?</p>
            <div class="balloon-actions">
                <button id="balloon-confirm-btn" class="btn-balloon-danger">Hapus</button>
                <button id="balloon-cancel-btn" class="btn-balloon-secondary">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Search Modal Overlay -->
<div class="search-modal-overlay" id="searchModal">
    <div class="search-modal">
        <div class="modal-header">
            <h4>Pencarian CCTV (Semua NVR)</h4>
            <button class="btn-close-modal" id="btnCloseSearch">&times;</button>
        </div>
        <div class="modal-body">
            <div class="search-bar-container">
                <select id="search-by">
                    <option value="all">Semua Kriteria</option>
                    <option value="ip_address">IP Address</option>
                    <option value="nama_cctv">Nama CCTV</option>
                    <option value="posisi">Lokasi / Posisi</option>
                    <option value="nvr">NVR</option>
                </select>
                <input type="text" id="search-keyword" placeholder="Ketik kata kunci pencarian (misal: Ruang Rapat)..." autocomplete="off">
                <button id="btn-do-search">Cari</button>
            </div>
            
            <div id="search-loading" style="display: none; text-align: center; padding: 20px; color: #888;">Sedang mencari data...</div>
            
            <table class="search-results-table" id="search-results-table" style="display: none;">
                <thead>
                    <tr>
                        <th>NVR</th>
                        <th>IP Address</th>
                        <th>Nama CCTV</th>
                        <th>Posisi</th>
                        <th>Ch</th>
                        <th width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="search-results-body">
                    <!-- Results injected via JS -->
                </tbody>
            </table>
            
            <div id="search-empty" style="display: none; text-align: center; padding: 20px; color: #888;">Tidak ada data yang ditemukan.</div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('searchModal');
    const btnOpen = document.getElementById('btn-open-search');
    const btnClose = document.getElementById('btnCloseSearch');
    const btnSearch = document.getElementById('btn-do-search');
    const searchKeyword = document.getElementById('search-keyword');
    const searchBy = document.getElementById('search-by');
    const tbody = document.getElementById('search-results-body');
    const tableEl = document.getElementById('search-results-table');
    const loadingEl = document.getElementById('search-loading');
    const emptyEl = document.getElementById('search-empty');
    
    // Buka Modal
    btnOpen.addEventListener('click', () => {
        modal.classList.add('show');
        searchKeyword.focus();
    });
    
    // Tutup Modal
    const closeModal = () => {
        modal.classList.remove('show');
    };
    btnClose.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if(e.target === modal) closeModal();
    });
    
    // Trigger pencarian saat menekan Enter
    searchKeyword.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    
    btnSearch.addEventListener('click', performSearch);
    
    function performSearch() {
        const keyword = searchKeyword.value.trim();
        if (keyword === '') {
            alert('Silakan masukkan kata kunci pencarian.');
            return;
        }
        
        const by = searchBy.value;
        
        tableEl.style.display = 'none';
        emptyEl.style.display = 'none';
        loadingEl.style.display = 'block';
        tbody.innerHTML = '';
        
        fetch('<?= site_url('cctv/search_api') ?>?keyword=' + encodeURIComponent(keyword) + '&by=' + encodeURIComponent(by))
        .then(res => res.json())
        .then(res => {
            loadingEl.style.display = 'none';
            if(res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(item => {
                    html += `<tr>
                        <td><b>${escapeHtml(item.nvr)}</b></td>
                        <td>${escapeHtml(item.ip_address)}</td>
                        <td>${escapeHtml(item.nama_cctv)}</td>
                        <td>${escapeHtml(item.posisi)}</td>
                        <td>${escapeHtml(item.channel)}</td>
                        <td class="action-links">`;
                    
                    if ('<?= $page_perm ?>' !== 'R') {
                        html += `
                            <a href="<?= site_url('cctv/edit') ?>/${item.id}" class="action-edit" title="Edit">Edit</a> | 
                            <a href="javascript:void(0)" onclick="confirmDelete(${item.id}, '${escapeHtml(item.ip_address)}')" class="action-delete" title="Hapus">Hapus</a>`;
                    } else {
                        html += `<span style="color:#999; font-size:12px; font-style:italic;">Akses Baca</span>`;
                    }
                        
                    html += `</td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                tableEl.style.display = 'table';
            } else {
                emptyEl.style.display = 'block';
            }
        })
        .catch(err => {
            console.error(err);
            loadingEl.style.display = 'none';
            emptyEl.style.display = 'block';
            emptyEl.innerText = 'Terjadi kesalahan saat mengambil data.';
        });
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/[&<>"']/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    }
    // --- Checkbox and Bulk Delete Logic ---
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.select-item');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const selectedCountSpan = document.getElementById('selected-count');
    const balloon = document.getElementById('delete-balloon');
    const balloonMsg = document.getElementById('balloon-message');
    const balloonConfirm = document.getElementById('balloon-confirm-btn');
    const balloonCancel = document.getElementById('balloon-cancel-btn');
    const rightFrame = document.querySelector('.right-frame');
    
    let balloonTimeout = null;

    function updateBulkButtonState() {
        const checkedCount = document.querySelectorAll('.select-item:checked').length;
        if(selectedCountSpan) selectedCountSpan.textContent = checkedCount;
        
        if (btnBulkDelete) {
            if (checkedCount > 0) {
                btnBulkDelete.classList.add('show');
            } else {
                btnBulkDelete.classList.remove('show');
                if (balloon && balloon.classList.contains('show') && balloon.dataset.triggerType === 'bulk') {
                    hideBalloon();
                }
            }
        }

        if (selectAllCheckbox && itemCheckboxes.length > 0) {
            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === itemCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => { cb.checked = selectAllCheckbox.checked; });
            updateBulkButtonState();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkButtonState);
    });

    function showBalloon(targetElement, message, type, onConfirm) {
        if (balloonTimeout) { clearTimeout(balloonTimeout); balloonTimeout = null; }

        balloonMsg.textContent = message;
        balloon.dataset.triggerType = type;
        
        balloonConfirm.onclick = function() {
            onConfirm();
            hideBalloon();
        };

        balloon.style.display = 'block';
        balloon.offsetHeight; 
        balloon.classList.add('show');

        const targetRect = targetElement.getBoundingClientRect();
        const balloonRect = balloon.getBoundingClientRect();
        const containerRect = rightFrame.getBoundingClientRect();

        let top = (targetRect.top - containerRect.top) + rightFrame.scrollTop - balloonRect.height - 10;
        let left = (targetRect.left - containerRect.left) + rightFrame.scrollLeft + (targetRect.width / 2) - (balloonRect.width / 2);
        
        let placement = 'arrow-down';

        if (top < rightFrame.scrollTop) {
            top = (targetRect.bottom - containerRect.top) + rightFrame.scrollTop + 10;
            placement = 'arrow-up';
        }

        if (left < 10) left = 10;
        else if (left + balloonRect.width > containerRect.width - 10) left = containerRect.width - balloonRect.width - 10;

        balloon.className = 'popup-balloon ' + placement + ' show';
        balloon.style.top = top + 'px';
        balloon.style.left = left + 'px';

        const clickOutsideHandler = function(e) {
            if (!balloon.contains(e.target) && !targetElement.contains(e.target)) {
                hideBalloon();
                document.removeEventListener('click', clickOutsideHandler);
            }
        };

        setTimeout(() => { document.addEventListener('click', clickOutsideHandler); }, 50);
    }

    function hideBalloon() {
        if(balloon) {
            balloon.classList.remove('show');
            balloonTimeout = setTimeout(() => {
                if (!balloon.classList.contains('show')) balloon.style.display = 'none';
            }, 200);
        }
    }

    if(balloonCancel) balloonCancel.addEventListener('click', hideBalloon);

    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const ip = this.dataset.ip;
            const message = "Yakin ingin menghapus CCTV dengan IP " + ip + "?";
            
            showBalloon(this, message, 'single', function() {
                window.location.href = "<?= site_url('cctv/delete/') ?>" + id;
            });
        });
    });

    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function(e) {
            e.preventDefault();
            const checkedCbs = document.querySelectorAll('.select-item:checked');
            const selectedIds = Array.from(checkedCbs).map(cb => cb.value);
            const count = selectedIds.length;
            const message = "Apakah Anda yakin ingin menghapus " + count + " data CCTV terpilih?";
            
            showBalloon(this, message, 'bulk', function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= site_url('cctv/bulkDelete') ?>';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'bulk_ids';
                input.value = selectedIds.join(',');
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            });
        });
    }
});

function confirmDelete(id, ip) {
    if (confirm('Apakah Anda yakin ingin menghapus CCTV dengan IP: ' + ip + '?')) {
        window.location.href = '<?= site_url('cctv/delete') ?>/' + id;
    }
}

// =================== TABLE SORT ===================
(function() {
    const sortSelect = document.getElementById('sort-col');
    const sortDirBtn = document.getElementById('btn-sort-dir');
    const sortInfo   = document.getElementById('sort-info');
    const tbody      = document.querySelector('table tbody');
    const hasPerm    = <?= ($page_perm !== 'R') ? 'true' : 'false' ?>;
    const colOffset  = hasPerm ? 1 : 0; // checkbox col offset

    // Column index map (offset applied in sortTable)
    const colMap = { no: 0, nvr: 1, ip: 2, nama: 3, posisi: 4, channel: 5 };

    let sortDir = 'asc'; // default ascending

    function getCellVal(tr, colKey) {
        const idx = colMap[colKey] + colOffset;
        const td  = tr.querySelectorAll('td')[idx];
        return td ? td.innerText.trim().toLowerCase() : '';
    }

    function naturalVal(s) {
        // Extract leading number for natural sort (e.g. "ch 3" → 3)
        const m = s.match(/(\d+)/);
        return m ? parseInt(m[1]) : s;
    }

    function sortTable() {
        const colKey = sortSelect.value;
        const rows   = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            let va = getCellVal(a, colKey);
            let vb = getCellVal(b, colKey);

            // Natural sort for numbers
            const na = naturalVal(va);
            const nb = naturalVal(vb);
            const isNum = typeof na === 'number' && typeof nb === 'number';

            let cmp = isNum ? na - nb : va.localeCompare(vb, 'id');
            return sortDir === 'asc' ? cmp : -cmp;
        });

        rows.forEach(tr => tbody.appendChild(tr));

        // Renumber No column
        rows.forEach((tr, i) => {
            const noTd = tr.querySelectorAll('td')[colOffset];
            if (noTd) noTd.textContent = i + 1;
        });

        const dirLabel = sortDir === 'asc' ? '▲ ASC' : '▼ DESC';
        sortDirBtn.textContent = dirLabel;
        sortInfo.textContent   = 'Urut: ' + sortSelect.options[sortSelect.selectedIndex].text + ' ' + dirLabel;
    }

    sortSelect.addEventListener('change', sortTable);

    sortDirBtn.addEventListener('click', function() {
        sortDir = sortDir === 'asc' ? 'desc' : 'asc';
        sortTable();
    });

    // Default: sort by Channel ASC on page load
    sortTable();
})();
</script>

<?= $this->endSection() ?>
