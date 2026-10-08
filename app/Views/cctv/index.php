<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .header-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 15px 0 20px 0;
    }
    .vlan-buttons {
        display: flex;
        flex-wrap: wrap; 
        gap: 10px;
    }
    .btn-vlan {
        padding: 8px 20px;
        background-color: #f0f2f5;
        color: #333;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        border: 1px solid #ccc;
        transition: all 0.2s;
    }
    .btn-vlan:hover { background-color: #d8dadf; }
    .btn-vlan.active { background-color: #0077b6; color: white; border-color: #005f8d; }

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
</style>

<div class="right-frame">
    <h3>Daftar CCTV Terpasang (<?= $activeVlanName ? esc($activeVlanName) : 'Semua VLAN' ?>)</h3>
    
    <div class="header-action">
        <div class="vlan-buttons">
            <?php foreach ($vlanList as $v): ?>
                <?php $isActive = ($activeNetwork == $v['network_ip']) ? 'active' : ''; ?>
                <a href="<?= site_url('cctv?vlan='.$v['network_ip']) ?>" class="btn-vlan <?= $isActive ?>"><?= esc($v['nama_vlan']) ?></a>
            <?php endforeach; ?>
        </div>
        
        <div style="display: flex; gap: 10px; align-items: center;">
            <?php if ($page_perm !== 'R'): ?>
            <button id="btn-bulk-delete" class="btn-bulk-delete">🗑️ Hapus (<span id="selected-count">0</span>)</button>
            <button type="button" class="btn-vlan" style="background:#17a2b8; color:white; padding:10px 15px;" onclick="openImportModal()">📥 Import Excel</button>
            <a href="<?= site_url('cctv/create') ?>"><button>+ Tambah Data</button></a>
            <?php endif; ?>
        </div>
    </div>
    
    <table>
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
                <th>No</th>
                <th>IP Address</th>
                <th>Channel</th>
                <th>NVR</th>
                <th>Nama CCTV</th>
                <th>Posisi</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($cctvData)): ?>
                <?php $no = 1; foreach($cctvData as $row): ?>
                    <tr>
                        <?php if ($page_perm !== 'R'): ?>
                        <td style='text-align: center;'>
                            <label class='checkbox-container'>
                                <input type='checkbox' class='select-item' value='<?= $row['id'] ?>'>
                                <span class='checkmark'></span>
                            </label>
                        </td>
                        <?php endif; ?>
                        <td><?= $no++ ?></td>
                        <td><b><?= esc($row['ip_address']) ?></b></td>
                        <td>Ch <?= esc($row['channel']) ?></td>
                        <td><?= esc($row['nvr']) ?></td>
                        <td><?= esc($row['nama_cctv']) ?></td>
                        <td><?= esc($row['posisi']) ?></td>
                        <td><?= esc($row['keterangan']) ?></td>
                        <td>
                        <?php if ($page_perm !== 'R'): ?>
                            <a href='<?= site_url('cctv/edit/'.$row['id']) ?>' class='btn-edit'>Edit</a> | 
                            <a href='javascript:void(0)' class='btn-hapus' data-id='<?= $row['id'] ?>' data-ip='<?= esc($row['ip_address']) ?>'>Hapus</a>
                        <?php else: ?>
                            <span style='color:#999; font-size:12px; font-style:italic;'>Akses Baca</span>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='9' style='text-align:center; padding: 20px;'>Belum ada data CCTV terdaftar untuk jaringan <b><?= $activeNetwork ?>.XXX</b>.</td></tr>
            <?php endif; ?>
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

    <!-- Modal Import Excel -->
    <div id="importModalCctv" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
        <div style="background-color:#fff; margin:5% auto; padding:20px; border-radius:8px; width:450px; position:relative; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <span style="position:absolute; right:15px; top:10px; font-size:24px; cursor:pointer;" onclick="closeImportModal()">&times;</span>
            <h3 style="margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">Import CCTV dari Excel</h3>
            <form action="<?= site_url('cctv/importExcel') ?>" method="POST" enctype="multipart/form-data">
                <div style="margin-bottom:15px;">
                    <label style="font-weight:bold; display:block; margin-bottom:5px;">Pilih File Excel (.xls, .xlsx, .csv)</label>
                    <input type="file" name="file_excel" accept=".xls,.xlsx,.csv" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
                </div>
                <div style="background-color:#e2e3e5; color:#383d41; padding:12px; border-radius:4px; font-size:12px; margin-bottom:15px; line-height:1.5;">
                    <b>Format Kolom Excel (Mulai Baris 2):</b><br>
                    A: IP Address<br>
                    B: Channel (Misal: 1, 2, dll)<br>
                    C: NVR<br>
                    D: Nama CCTV<br>
                    E: Posisi<br>
                    F: Keterangan
                </div>
                <div style="text-align:right;">
                    <button type="button" style="background:#6c757d; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; margin-right:5px;" onclick="closeImportModal()">Batal</button>
                    <button type="submit" style="background:#17a2b8; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer; font-weight:bold;">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openImportModal() {
    document.getElementById('importModalCctv').style.display = 'block';
}
function closeImportModal() {
    document.getElementById('importModalCctv').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function() {
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
</script>

<?= $this->endSection() ?>
