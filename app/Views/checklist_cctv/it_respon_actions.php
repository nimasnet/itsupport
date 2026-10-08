<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<style>
    .ira-header-action { display:flex; justify-content:space-between; align-items:center; margin:15px 0 20px 0; flex-wrap:wrap; gap:10px; }
    .ira-stat-cards { display:flex; gap:12px; margin-bottom:18px; flex-wrap:wrap; }
    .ira-card { flex:1; min-width:140px; border-radius:10px; padding:14px 18px; color:#fff; display:flex; align-items:center; gap:12px; box-shadow:0 4px 12px rgba(0,0,0,0.12); }
    .ira-card .ira-card-icon { font-size:28px; }
    .ira-card .ira-card-info h4 { margin:0; font-size:20px; font-weight:800; }
    .ira-card .ira-card-info p  { margin:0; font-size:12px; opacity:.85; }
    .ira-badge { padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; white-space:nowrap; display:inline-block; }
    .ira-btn-nvr { display:inline-block; padding:7px 14px; background:#e2e8f0; color:#333; text-decoration:none; border-radius:5px; font-weight:bold; border:1px solid #ccc; font-size:13px; transition:background 0.2s; }
    .ira-btn-nvr:hover { background:#cbd5e1; }
    .ira-btn-nvr.active { background:<?= $top_color ?? '#0077b6' ?>; color:#fff; border-color:<?= $top_color ?? '#0077b6' ?>; }
    table { width:100%; border-collapse:collapse; margin-top:10px; font-size:13px; }
    table, th, td { border:1px solid #e2e8f0; }
    th { background:#f8fafc; padding:10px 12px; text-align:left; color:#374151; font-weight:700; position:sticky; top:0; }
    td { padding:10px 12px; vertical-align:middle; }
    tr:hover td { background:#f0f7ff; }
    .ir-action-btn { background:linear-gradient(135deg,#4e73df,#224abe); color:#fff; border:none; border-radius:6px; padding:5px 11px; font-size:11px; font-weight:700; cursor:pointer; white-space:nowrap; }
    .filter-nvr-bar { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px; }

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
</style>

<div class="right-frame">
    <h3 style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
        🔧 IT Respon Actions
        <span style="font-size:13px; font-weight:400; color:#888; margin-left:6px;">— CCTV Bermasalah</span>
    </h3>
    <p style="color:#6b7280; font-size:13px; margin-bottom:18px;">Menampilkan CCTV dengan status bermasalah. Gunakan dropdown untuk mencatat tindakan penanganan.</p>

    <!-- Stat Cards -->
    <?php
    $total  = count($checklists);
    $solved = count(array_filter($checklists, fn($c) => $c['it_respon'] === 'Solved'));
    $onplan = count(array_filter($checklists, fn($c) => $c['it_respon'] === 'On Plan'));
    $unhandled = count(array_filter($checklists, fn($c) => empty($c['it_respon'])));
    ?>
    <div class="ira-stat-cards">
        <div class="ira-card" style="background:linear-gradient(135deg,#dc3545,#a71d2a);">
            <span class="ira-card-icon">📡</span>
            <div class="ira-card-info"><h4><?= $total ?></h4><p>Total Bermasalah</p></div>
        </div>
        <div class="ira-card" style="background:linear-gradient(135deg,#28a745,#1a7431);">
            <span class="ira-card-icon">✅</span>
            <div class="ira-card-info"><h4><?= $solved ?></h4><p>Solved</p></div>
        </div>
        <div class="ira-card" style="background:linear-gradient(135deg,#17a2b8,#0d7a8a);">
            <span class="ira-card-icon">📋</span>
            <div class="ira-card-info"><h4><?= $onplan ?></h4><p>On Plan</p></div>
        </div>
        <div class="ira-card" style="background:linear-gradient(135deg,#6c757d,#464e55);">
            <span class="ira-card-icon">⚠️</span>
            <div class="ira-card-info"><h4><?= $unhandled ?></h4><p>Belum Ditangani</p></div>
        </div>
    </div>

    <!-- NVR Filter Bar -->
    <div class="filter-nvr-bar">
        <?php
        $is_all = ($active_nvr == '') ? 'active' : '';
        echo '<a href="'.site_url('cctv/it-respon-actions').'" class="ira-btn-nvr '.$is_all.'">Semua NVR</a>';
        foreach($nvrList as $nvr_item) {
            $n = esc($nvr_item['nama_nvr']);
            $act = ($active_nvr == $nvr_item['nama_nvr']) ? 'active' : '';
            echo '<a href="'.site_url('cctv/it-respon-actions').'?nvr='.urlencode($nvr_item['nama_nvr']).'" class="ira-btn-nvr '.$act.'">'.$n.'</a>';
        }
        ?>
    </div>

    <!-- Table -->
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>PIC Check</th>
                    <th>NVR</th>
                    <th>Channel</th>
                    <th>Nama CCTV</th>
                    <th>Status</th>
                    <th>IT Respon</th>
                    <th>Notes</th>
                    <th>Documentations</th>
                    <?php if($is_admin): ?><th>Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($checklists)): ?>
                <tr><td colspan="<?= $is_admin ? 11 : 10 ?>" style="text-align:center; padding:30px; color:#888;">🎉 Tidak ada CCTV bermasalah saat ini.</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach($checklists as $c): ?>
                <?php
                    $statusColor = '#6c757d';
                    if(strpos($c['status'],'Offline') !== false) $statusColor = '#dc3545';
                    elseif(strpos($c['status'],'Maintenance') !== false) $statusColor = '#ffc107';

                    $ir = $c['it_respon'] ?? '';
                    $irColor = '#6c757d';
                    if($ir === 'Solved') $irColor = '#28a745';
                    elseif($ir === 'On Plan') $irColor = '#17a2b8';
                    elseif($ir === 'Waiting Device') $irColor = '#ffc107';
                    elseif($ir === 'Hampered by Equipment') $irColor = '#dc3545';
                    elseif($ir === 'Authentication Password') $irColor = '#6f42c1';
                    $irTextColor = $irColor === '#ffc107' ? '#000' : '#fff';

                    $rowBg = '';
                    if($ir === 'Solved') $rowBg = 'background:#f0fff4;';
                    elseif(empty($ir)) $rowBg = 'background:#fff8f8;';
                ?>
                <tr style="<?= $rowBg ?>">
                    <td><?= $no++ ?></td>
                    <td><?= esc($c['tanggal']) ?></td>
                    <td><?= esc($c['jam']) ?></td>
                    <td><?= esc($c['pic_check']) ?></td>
                    <td><?= esc($c['nvr']) ?></td>
                    <td><?= esc($c['channel']) ?></td>
                    <td><strong><?= esc($c['nama_cctv']) ?></strong></td>
                    <td>
                        <span class="ira-badge" style="background:<?= $statusColor ?>; color:<?= $statusColor==='#ffc107'?'#000':'#fff' ?>;">
                            <?= esc($c['status']) ?>
                        </span>
                    </td>
                    <td class="it-respon-cell" data-id="<?= $c['id'] ?>">
                        <?php if($is_admin): ?>
                        <div style="display:flex; align-items:center; gap:5px; flex-wrap:wrap;">
                            <?php if($ir): ?>
                            <span class="ira-badge" style="background:<?= $irColor ?>; color:<?= $irTextColor ?>;"><?= esc($ir) ?></span>
                            <?php endif; ?>
                            <button class="ir-action-btn" onclick="openItResponModal(<?= $c['id'] ?>, '<?= esc($ir) ?>')">
                                <?= $ir ? '✏️ Ubah' : '➕ Respon' ?>
                            </button>
                        </div>
                        <?php else: ?>
                            <?php if($ir): ?>
                            <span class="ira-badge" style="background:<?= $irColor ?>; color:<?= $irTextColor ?>;"><?= esc($ir) ?></span>
                            <?php else: ?>
                            <?php if(strpos($c['status'], 'Aktif & Record') !== false): ?>
                            <span style="color:#aaa; font-style:italic; font-size:12px;">Not Required</span>
                            <?php else: ?>
                            <span style="color:#dc3545; font-size:12px; font-weight:bold;">Waiting IT Respon</span>
                            <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td id="ket-<?= $c['id'] ?>" class="notes-cell" data-id="<?= $c['id'] ?>" style="max-width:220px; font-size:12px;"><?php
                        $rawNote = $c['keterangan'] ?? '';
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
                    <?php if($is_admin): ?>
                    <td>
                        <a href="<?= site_url('checklist-cctv/edit/'.$c['id']) ?>" style="background:#ffc107; color:#000; padding:5px 10px; text-decoration:none; border-radius:4px; font-size:12px; font-weight:bold;">Edit</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal IT Respon Actions -->
<div id="ir-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:10000; align-items:center; justify-content:center; backdrop-filter:blur(3px);">
    <div style="background:#fff; border-radius:12px; width:460px; max-width:95vw; box-shadow:0 8px 40px rgba(0,0,0,0.25); overflow:hidden;">
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
                <select id="ir-select" style="width:100%; padding:10px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:14px; outline:none;">
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
                <button id="ir-save-btn" style="padding:10px 24px; background:linear-gradient(135deg,#4e73df,#224abe); color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer;">💾 Simpan</button>
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
    let currentItResponId = null;
    const AJAX_URL = '<?= site_url('cctv/it-respon-actions/update') ?>';

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

        if (!it_respon) { Swal.fire({ icon:'warning', title:'Pilih status IT Respon terlebih dahulu!' }); return; }
        if (!respon_note) { Swal.fire({ icon:'warning', title:'Komentar perbaikan tidak boleh kosong!' }); return; }

        const fd = new FormData();
        fd.append('id', currentItResponId);
        fd.append('it_respon', it_respon);
        fd.append('respon_date', respon_date);
        fd.append('respon_time', respon_time);
        fd.append('respon_note', respon_note);

        if (imageMode === 'file') {
            const fileInput = document.getElementById('image-file-input');
            if (fileInput.files.length > 0) {
                fd.append('image_file', fileInput.files[0]);
            }
        } else if (imageMode === 'camera') {
            const camData = document.getElementById('camera_image_data').value;
            if (camData) {
                fd.append('camera_image', camData);
            }
        }

        fetch(AJAX_URL, { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                closeItResponModal();
                Swal.fire({ icon:'success', title:'Berhasil!', text:res.message, timer:1500, showConfirmButton:false });

                const colorMap = { 'Solved':'#28a745','On Plan':'#17a2b8','Waiting Device':'#ffc107','Hampered by Equipment':'#dc3545','Authentication Password':'#6f42c1' };
                const c = colorMap[it_respon] || '#6c757d';
                const tc = c === '#ffc107' ? '#000' : '#fff';

                const irCell = document.querySelector(`.it-respon-cell[data-id='${currentItResponId}']`);
                if (irCell) {
                    irCell.querySelector('div').innerHTML =
                        `<span class="ira-badge" style="background:${c};color:${tc};">${it_respon}</span>
                        <button class="ir-action-btn" onclick="openItResponModal(${currentItResponId},'${it_respon}')">✏️ Ubah</button>`;
                }

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
                Swal.fire({ icon:'error', title:'Gagal', text:res.message });
            }
        })
        .catch(() => Swal.fire({ icon:'error', title:'Terjadi kesalahan jaringan.' }));
    });

    document.getElementById('ir-modal').addEventListener('click', e => { if(e.target === document.getElementById('ir-modal')) closeItResponModal(); });

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
