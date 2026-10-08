<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    /* ===== UPS Checklist Premium Design ===== */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    .ups-page { font-family: 'Inter', Arial, sans-serif; }

    .ups-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 24px;
    }
    .ups-header-icon {
        width: 50px; height: 50px;
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px;
        box-shadow: 0 4px 15px rgba(59,130,246,0.35);
    }
    .ups-header h2 { margin: 0; font-size: 22px; font-weight: 700; color: #1e293b; }
    .ups-header p { margin: 2px 0 0; font-size: 13px; color: #64748b; }

    /* Card wrapper */
    .ups-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 24px rgba(30,64,175,0.08);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 28px;
    }
    .ups-card-header {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        padding: 18px 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ups-card-header h3 { margin: 0; color: #fff; font-size: 15px; font-weight: 600; }
    .ups-card-header span { font-size: 18px; }
    .ups-card-body { padding: 24px; }

    /* Form layout */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }
    .form-grid.full { grid-template-columns: 1fr; }

    .form-field { display: flex; flex-direction: column; gap: 6px; }
    .form-field label {
        font-size: 13px; font-weight: 600; color: #374151;
        display: flex; align-items: center; gap: 6px;
    }
    .form-field label .badge {
        font-size: 10px; font-weight: 500;
        background: #eff6ff; color: #3b82f6;
        border: 1px solid #bfdbfe;
        border-radius: 4px; padding: 1px 5px;
    }

    /* Tabs (option toggles) */
    .option-tabs {
        display: flex;
        gap: 0;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        width: fit-content;
        margin-bottom: 10px;
    }
    .option-tab {
        padding: 7px 16px;
        font-size: 12px; font-weight: 600;
        cursor: pointer;
        border: none;
        background: #f8fafc;
        color: #64748b;
        transition: all 0.2s;
        display: flex; align-items: center; gap: 5px;
    }
    .option-tab.active {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        color: #fff;
    }
    .option-tab:not(.active):hover { background: #e2e8f0; }

    /* Input fields */
    .ups-input, .ups-select {
        padding: 10px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Inter', Arial, sans-serif;
        color: #1e293b;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        width: 100%;
        background: #fff;
        box-sizing: border-box;
    }
    .ups-input:focus, .ups-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
    }

    /* Dropdown condition colors */
    .ups-select option[value="On-Normal"] { color: #16a34a; font-weight: 600; }
    .ups-select option[value="On-Droop"]  { color: #d97706; font-weight: 600; }
    .ups-select option[value="Off-Broken"]{ color: #dc2626; font-weight: 600; }
    .ups-select option[value="IT-Remove"] { color: #7c3aed; font-weight: 600; }

    /* Condition badge display */
    .condition-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
        margin-top: 4px;
    }
    .badge-normal  { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-droop   { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .badge-broken  { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-remove  { background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe; }

    /* Camera panel */
    .camera-panel {
        border: 1.5px dashed #93c5fd;
        border-radius: 12px;
        background: #eff6ff;
        padding: 16px;
        text-align: center;
    }
    #camera-preview-location, #camera-preview-image {
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 8px;
        display: none;
        border: 2px solid #3b82f6;
    }
    .capture-canvas { display: none; }

    /* Date field with balloon calendar */
    .date-field-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }
    .date-field-wrapper .ups-input {
        cursor: pointer;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%233b82f6' viewBox='0 0 16 16'%3E%3Cpath d='M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z'/%3E%3C/svg%3E") no-repeat right 12px center;
        padding-right: 38px;
    }

    /* Custom balloon calendar */
    .balloon-cal {
        display: none;
        position: absolute;
        z-index: 9999;
        top: calc(100% + 8px);
        left: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 8px 32px rgba(30,64,175,0.18);
        padding: 16px;
        min-width: 300px;
        animation: calFadeIn 0.18s ease;
    }
    .balloon-cal.show { display: block; }
    @keyframes calFadeIn {
        from { opacity: 0; transform: translateY(-8px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .cal-arrow {
        position: absolute;
        top: -8px; left: 24px;
        width: 0; height: 0;
        border-left: 9px solid transparent;
        border-right: 9px solid transparent;
        border-bottom: 9px solid #e2e8f0;
    }
    .cal-arrow::after {
        content: '';
        position: absolute;
        top: 2px; left: -8px;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid #fff;
    }
    .cal-nav {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 12px;
    }
    .cal-nav button {
        background: #eff6ff; color: #1e40af;
        border: none; border-radius: 6px;
        width: 28px; height: 28px;
        cursor: pointer; font-size: 15px;
        display: flex; align-items: center; justify-content: center;
        padding: 0; min-width: unset;
    }
    .cal-nav button:hover { background: #dbeafe; }
    .cal-title { font-weight: 700; color: #1e293b; font-size: 14px; }
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
    }
    .cal-day-header {
        text-align: center; font-size: 11px; font-weight: 600;
        color: #94a3b8; padding: 2px 0;
    }
    .cal-day {
        text-align: center; font-size: 13px;
        padding: 5px; border-radius: 6px;
        cursor: pointer; color: #374151;
        transition: background 0.15s, color 0.15s;
    }
    .cal-day:hover { background: #dbeafe; color: #1e40af; }
    .cal-day.today { background: #eff6ff; color: #3b82f6; font-weight: 700; border: 1px solid #bfdbfe; }
    .cal-day.selected { background: linear-gradient(135deg, #1e40af, #3b82f6); color: #fff; font-weight: 700; }
    .cal-day.empty { cursor: default; }
    .cal-today-btn {
        margin-top: 10px;
        width: 100%;
        padding: 6px 0;
        background: #eff6ff; color: #1e40af;
        border: 1px solid #bfdbfe;
        border-radius: 7px; font-size: 12px;
        font-weight: 600; cursor: pointer;
        transition: background 0.15s;
    }
    .cal-today-btn:hover { background: #dbeafe; }

    /* Image upload area */
    .img-upload-area {
        border: 2px dashed #93c5fd;
        border-radius: 12px;
        background: #f0f9ff;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }
    .img-upload-area:hover { border-color: #3b82f6; background: #eff6ff; }
    .img-upload-area input[type="file"] {
        position: absolute; inset: 0;
        opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .img-upload-icon { font-size: 36px; margin-bottom: 8px; }
    .img-upload-area p { margin: 0; font-size: 13px; color: #64748b; }
    .img-upload-area strong { color: #3b82f6; }
    .img-preview-box {
        margin-top: 12px;
        border-radius: 10px;
        overflow: hidden;
        display: none;
        position: relative;
    }
    .img-preview-box img {
        width: 100%;
        max-height: 200px;
        object-fit: cover;
        display: block;
    }
    .img-remove-btn {
        position: absolute; top: 8px; right: 8px;
        background: rgba(220,38,38,0.85); color: #fff;
        border: none; border-radius: 20px;
        padding: 4px 10px; font-size: 12px; cursor: pointer;
        font-weight: 600;
    }

    /* Camera capture for image */
    .cam-img-panel {
        border: 1.5px dashed #93c5fd;
        border-radius: 12px;
        background: #eff6ff;
        padding: 16px;
    }
    #video-image {
        width: 100%; max-height: 220px;
        object-fit: cover; border-radius: 8px;
        display: none; border: 2px solid #3b82f6;
    }
    #canvas-image { display: none; }
    #photo-image-preview {
        width: 100%; max-height: 200px;
        object-fit: cover; border-radius: 8px;
        display: none; border: 2px solid #16a34a; margin-top: 8px;
    }

    /* Cam control buttons */
    .cam-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 14px; border-radius: 7px;
        font-size: 12px; font-weight: 600;
        cursor: pointer; border: none;
        transition: all 0.2s;
        margin: 4px 2px;
    }
    .cam-btn-blue  { background: linear-gradient(135deg,#1e40af,#3b82f6); color:#fff; }
    .cam-btn-blue:hover  { opacity: 0.88; }
    .cam-btn-green { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
    .cam-btn-green:hover { background: #bbf7d0; }
    .cam-btn-red   { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .cam-btn-red:hover   { background: #fecaca; }
    .cam-btn-gray  { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
    .cam-btn-gray:hover  { background: #e2e8f0; }

    /* Submit button */
    .btn-submit-ups {
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Inter', Arial, sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 4px 15px rgba(59,130,246,0.3);
        margin-top: 8px;
    }
    .btn-submit-ups:hover {
        box-shadow: 0 6px 20px rgba(59,130,246,0.4);
        transform: translateY(-1px);
    }

    /* Table */
    .ups-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .ups-table thead tr { background: linear-gradient(135deg,#1e40af,#3b82f6); }
    .ups-table th { background: transparent; color: #fff; padding: 11px 14px; font-weight: 600; text-align: left; border-bottom: none; }
    .ups-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; color: #374151; }
    .ups-table tbody tr:hover { background: #f8fafc; }
    .ups-table .img-thumb { width: 60px; height: 48px; object-fit: cover; border-radius: 6px; cursor: pointer; }
    .ups-empty { text-align: center; padding: 40px; color: #94a3b8; }
    .ups-empty-icon { font-size: 40px; margin-bottom: 8px; }

    /* Lightbox */
    .lightbox-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.82); z-index: 99999;
        align-items: center; justify-content: center;
        flex-direction: column; gap: 14px;
    }
    .lightbox-overlay.show { display: flex; }
    .lightbox-overlay img {
        max-width: 90vw; max-height: 80vh;
        border-radius: 12px; box-shadow: 0 8px 40px rgba(0,0,0,0.6);
    }
    .lightbox-close {
        position: fixed; top: 18px; right: 22px;
        background: #fff; color: #1e293b;
        border: none; border-radius: 50%;
        width: 38px; height: 38px;
        font-size: 20px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    /* Location camera overlay */
    #cam-location-overlay { display: none; }
    #cam-location-overlay.show { display: block; }
    #cam-location-result {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; background: #eff6ff;
        border: 1px solid #bfdbfe; border-radius: 7px;
        font-size: 13px; color: #1e40af; font-weight: 600;
        margin-top: 8px; max-width: 100%; word-break: break-all;
    }

    .ups-layout {
        display: flex;
        gap: 24px;
        align-items: flex-start;
    }
    .form-section {
        flex: 0 0 350px; /* Fixed width for form so table gets maximum space */
        min-width: 350px;
    }
    .table-section {
        flex: 1;
        min-width: 0; /* Prevent flex overflow */
    }

    @media(max-width: 1024px) {
        .ups-layout {
            flex-direction: column;
        }
        .form-section, .table-section {
            flex: auto;
            width: 100%;
        }
    }
    
    @media(max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
    }

    /* Export bar */
    .export-bar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 16px; gap: 8px;
        border-bottom: 1px solid #e2e8f0; background: #f8fafc;
    }
    .export-bar .export-label { font-size: 12px; color: #64748b; font-weight: 500; }
    .export-bar .export-btns  { display: flex; gap: 8px; }
    .btn-export {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 14px; border: none; border-radius: 7px;
        font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;
    }
    .btn-export-csv   { background: #16a34a; color: #fff; }
    .btn-export-csv:hover { background: #15803d; }
    .btn-export-print { background: #0ea5e9; color: #fff; }
    .btn-export-print:hover { background: #0284c7; }

    @media print {
        body * { visibility: hidden !important; }
        #ups-print-area, #ups-print-area * { visibility: visible !important; }
        #ups-print-area { position: absolute; left: 0; top: 0; width: 100%; }
        #ups-print-area table th { background: #1e40af !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>

<div class="ups-page right-frame">

    <!-- Header -->
    <div class="ups-header">
        <div class="ups-header-icon">⚡</div>
        <div>
            <h2>UPS Checklist</h2>
            <p>Monitoring Support — Pencatatan kondisi UPS</p>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="ups-layout">
        <!-- Form Card -->
        <div class="ups-card form-section">
        <div class="ups-card-header">
            <span>📋</span>
            <h3>Form Input UPS Checklist</h3>
        </div>
        <div class="ups-card-body">
            <form method="POST" action="<?= site_url('monitoring-support/ups-checklist/store') ?>" enctype="multipart/form-data" id="ups-form">
                <?= csrf_field() ?>

                <!-- Hidden camera captured images -->
                <input type="hidden" name="camera_image" id="camera_image_data">
                <input type="hidden" name="camera_location_text" id="camera_location_text">

                <div class="form-grid">

                    <!-- ===== UPS LOCATION ===== -->
                    <div class="form-field">
                        <label>⚡ UPS Location <span class="badge">Wajib</span></label>

                        <!-- Toggle tabs -->
                        <div class="option-tabs">
                            <button type="button" class="option-tab active" id="tab-loc-manual" onclick="switchLocationTab('manual')">
                                ✏️ Manual
                            </button>
                            <button type="button" class="option-tab" id="tab-loc-camera" onclick="switchLocationTab('camera')">
                                📷 Camera
                            </button>
                        </div>

                        <!-- Option 1: Manual text -->
                        <div id="loc-manual-panel">
                            <select class="ups-input" id="ups_location_manual">
                                <option value="" disabled selected>-- Pilih Lokasi Switch --</option>
                                <?php if (!empty($switchNetworks)): ?>
                                    <?php foreach($switchNetworks as $switch): ?>
                                        <option value="<?= esc($switch['name_switch']) ?>"><?= esc($switch['name_switch']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Option 2: Camera scan (OCR-style) -->
                        <div id="loc-camera-panel" style="display:none;">
                            <div class="camera-panel">
                                <video id="video-location" playsinline autoplay
                                       style="width:100%;max-height:200px;object-fit:cover;border-radius:8px;display:none;border:2px solid #3b82f6;"></video>
                                <canvas id="canvas-location" class="capture-canvas"></canvas>
                                <img id="photo-location-preview" alt="Hasil foto lokasi"
                                     style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;display:none;border:2px solid #16a34a;">

                                <div id="cam-location-placeholder" style="padding:20px 10px;">
                                    <div style="font-size:32px;margin-bottom:8px;">📷</div>
                                    <p style="font-size:12px;color:#64748b;margin:0;">Gunakan kamera untuk mengambil foto lokasi UPS<br>lalu ketikkan nama lokasi berdasarkan foto.</p>
                                </div>

                                <div style="margin-top:10px;display:flex;flex-wrap:wrap;justify-content:center;gap:4px;">
                                    <button type="button" class="cam-btn cam-btn-blue" onclick="startCamera('location')">📷 Buka Kamera</button>
                                    <button type="button" class="cam-btn cam-btn-green" id="capture-loc-btn" onclick="capturePhoto('location')" style="display:none;">📸 Ambil Foto</button>
                                    <button type="button" class="cam-btn cam-btn-red"  id="stop-loc-btn" onclick="stopCamera('location')" style="display:none;">⏹ Stop</button>
                                    <button type="button" class="cam-btn cam-btn-gray" id="retake-loc-btn" onclick="retakePhoto('location')" style="display:none;">🔄 Ulangi</button>
                                </div>
                            </div>

                            <div id="cam-location-result-wrapper" style="display:none; margin-top:10px;">
                                <label style="font-size:12px;color:#64748b;font-weight:600;">Nama Lokasi (dari foto):</label>
                                <input type="text" class="ups-input" id="ups_location_camera_text"
                                       placeholder="Ketik nama lokasi berdasarkan foto yang diambil..."
                                       style="margin-top:5px;">
                            </div>
                        </div>

                        <!-- Hidden final field -->
                        <input type="hidden" name="ups_location" id="ups_location_final">
                    </div>

                    <!-- ===== UPS CONDITIONS ===== -->
                    <div class="form-field">
                        <label>🔋 UPS Conditions <span class="badge">Wajib</span></label>
                        <select class="ups-select" name="ups_conditions" id="ups_conditions" onchange="updateConditionBadge(this.value)" required>
                            <option value="">— Pilih Kondisi UPS —</option>
                            <option value="On-Normal">✅ On-Normal</option>
                            <option value="On-Droop">⚠️ On-Droop</option>
                            <option value="Off-Broken">❌ Off-Broken</option>
                            <option value="IT-Remove">🔧 IT Remove</option>
                        </select>
                        <div id="condition-badge-display"></div>
                    </div>

                    <!-- ===== DATE UPDATE ===== -->
                    <div class="form-field">
                        <label>📅 Date Update <span class="badge">Wajib</span></label>
                        <div class="date-field-wrapper">
                            <input type="text" class="ups-input" id="date_display"
                                   placeholder="Klik untuk memilih tanggal..."
                                   readonly>
                            <input type="hidden" name="date_update" id="date_update">
                            <!-- Balloon Calendar -->
                            <div class="balloon-cal" id="balloon-calendar">
                                <div class="cal-arrow"></div>
                                <div class="cal-nav">
                                    <button type="button" onclick="calPrevMonth()">‹</button>
                                    <span class="cal-title" id="cal-title"></span>
                                    <button type="button" onclick="calNextMonth()">›</button>
                                </div>
                                <div class="cal-grid" id="cal-day-headers"></div>
                                <div class="cal-grid" id="cal-days"></div>
                                <button type="button" class="cal-today-btn" onclick="selectToday()">📅 Hari Ini</button>
                            </div>
                        </div>
                    </div>

                    <!-- ===== IMAGE ===== -->
                    <div class="form-field">
                        <label>🖼️ Image <span class="badge">Opsional</span></label>

                        <div class="option-tabs">
                            <button type="button" class="option-tab active" id="tab-img-file" onclick="switchImageTab('file')">
                                📁 Pilih File
                            </button>
                            <button type="button" class="option-tab" id="tab-img-camera" onclick="switchImageTab('camera')">
                                📷 Camera
                            </button>
                        </div>

                        <!-- Option 1: File -->
                        <div id="img-file-panel">
                            <div class="img-upload-area" id="file-drop-area">
                                <input type="file" name="image" id="image-file-input"
                                       accept="image/*"
                                       onchange="previewImageFile(this)">
                                <div class="img-upload-icon">📂</div>
                                <p><strong>Klik atau drag & drop</strong> gambar ke sini</p>
                                <p style="font-size:11px;color:#94a3b8;margin-top:4px;">JPG, PNG, WEBP — Maks. 5MB</p>
                            </div>
                            <div class="img-preview-box" id="file-preview-box">
                                <img id="file-preview-img" src="" alt="Preview">
                                <button type="button" class="img-remove-btn" onclick="removeFilePreview()">✕ Hapus</button>
                            </div>
                        </div>

                        <!-- Option 2: Camera -->
                        <div id="img-camera-panel" style="display:none;">
                            <div class="cam-img-panel">
                                <video id="video-image" playsinline autoplay></video>
                                <canvas id="canvas-image"></canvas>
                                <img id="photo-image-preview" alt="Foto UPS">

                                <div id="cam-img-placeholder" style="text-align:center;padding:20px 10px;">
                                    <div style="font-size:32px;margin-bottom:8px;">📷</div>
                                    <p style="font-size:12px;color:#64748b;margin:0;">Ambil foto UPS menggunakan kamera perangkat Anda</p>
                                </div>

                                <div style="margin-top:10px;display:flex;flex-wrap:wrap;justify-content:center;gap:4px;">
                                    <button type="button" class="cam-btn cam-btn-blue" onclick="startCamera('image')">📷 Buka Kamera</button>
                                    <button type="button" class="cam-btn cam-btn-green" id="capture-img-btn" onclick="capturePhoto('image')" style="display:none;">📸 Ambil Foto</button>
                                    <button type="button" class="cam-btn cam-btn-red"  id="stop-img-btn" onclick="stopCamera('image')" style="display:none;">⏹ Stop</button>
                                    <button type="button" class="cam-btn cam-btn-gray" id="retake-img-btn" onclick="retakePhoto('image')" style="display:none;">🔄 Ulangi</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn-submit-ups" id="btn-save-ups">
                        <span>💾</span> Simpan UPS Checklist
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="ups-card table-section">
        <div class="ups-card-header">
            <span>📊</span>
            <h3>Data UPS Checklist</h3>
        </div>
        <!-- Export Bar -->
        <div class="export-bar">
            <span class="export-label">📋 Total data: <strong><?= count($checklistData) ?></strong> record</span>
            <div class="export-btns">
                <button class="btn-export btn-export-csv" onclick="exportCSV()">📥 Export CSV</button>
                <button class="btn-export btn-export-print" onclick="printTable()">🖨️ Print</button>
            </div>
        </div>
        <div class="ups-card-body" style="padding: 0;">
            <?php if (!empty($checklistData) && count($checklistData) > 0): ?>
            <div id="ups-print-area" style="overflow-x:auto;">
                <table class="ups-table" id="ups-data-table">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>UPS Location</th>
                            <th>UPS Conditions</th>
                            <th>Date Update</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($checklistData as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['ups_location']) ?></td>
                            <td><?php
                                $cond = $row['ups_conditions'];
                                $cls = match($cond) {
                                    'On-Normal'  => 'badge-normal',
                                    'On-Droop'   => 'badge-droop',
                                    'Off-Broken' => 'badge-broken',
                                    'IT-Remove'  => 'badge-remove',
                                    default      => ''
                                };
                                $icon = match($cond) {
                                    'On-Normal'  => '✅',
                                    'On-Droop'   => '⚠️',
                                    'Off-Broken' => '❌',
                                    'IT-Remove'  => '🔧',
                                    default      => '—'
                                };
                                echo "<span class='condition-badge $cls'>$icon " . esc($cond) . "</span>";
                            ?></td>
                            <td><?= date('d M Y', strtotime($row['date_update'])) ?></td>
                            <td>
                                <?php if (!empty($row['image'])): ?>
                                <img src="<?= base_url('uploads/ups/' . $row['image']) ?>"
                                     class="img-thumb"
                                     onclick="openLightbox('<?= base_url('uploads/ups/' . $row['image']) ?>')"
                                     alt="UPS Image">
                                <?php else: ?>
                                <span style="color:#94a3b8;font-size:12px;">— Tidak ada —</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="ups-empty">
                <div class="ups-empty-icon">⚡</div>
                <p>Belum ada data UPS Checklist.</p>
                <p style="font-size:12px;">Isi form di atas untuk menambahkan data pertama.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    </div> <!-- End Main Layout -->

</div>

<!-- Lightbox -->
<div class="lightbox-overlay" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">✕</button>
    <img id="lightbox-img" src="" alt="UPS Image Preview">
</div>

<script>
// =================== LOCATION TAB ===================
let locationMode = 'manual';

function switchLocationTab(mode) {
    locationMode = mode;
    document.getElementById('tab-loc-manual').classList.toggle('active', mode === 'manual');
    document.getElementById('tab-loc-camera').classList.toggle('active', mode === 'camera');
    document.getElementById('loc-manual-panel').style.display = mode === 'manual' ? '' : 'none';
    document.getElementById('loc-camera-panel').style.display  = mode === 'camera' ? '' : 'none';
    if (mode !== 'camera') stopCamera('location');
}

// =================== IMAGE TAB ===================
let imageMode = 'file';

function switchImageTab(mode) {
    imageMode = mode;
    document.getElementById('tab-img-file').classList.toggle('active', mode === 'file');
    document.getElementById('tab-img-camera').classList.toggle('active', mode === 'camera');
    document.getElementById('img-file-panel').style.display   = mode === 'file' ? '' : 'none';
    document.getElementById('img-camera-panel').style.display = mode === 'camera' ? '' : 'none';
    if (mode !== 'camera') stopCamera('image');
}

// =================== CAMERA ===================
let streams = { location: null, image: null };

// Camera status indicator
function setCamStatus(type, msg, color) {
    const id = type === 'location' ? 'cam-loc-status' : 'cam-img-status';
    let el = document.getElementById(id);
    if (!el) {
        el = document.createElement('div');
        el.id = id;
        el.style.cssText = 'font-size:12px;padding:6px 10px;border-radius:6px;margin-top:6px;text-align:center;font-weight:600;';
        const placeholder = document.getElementById(type === 'location' ? 'cam-location-placeholder' : 'cam-img-placeholder');
        placeholder.parentNode.insertBefore(el, placeholder.nextSibling);
    }
    el.textContent = msg;
    el.style.background = color === 'red' ? '#fee2e2' : color === 'green' ? '#dcfce7' : '#eff6ff';
    el.style.color = color === 'red' ? '#dc2626' : color === 'green' ? '#16a34a' : '#1e40af';
    el.style.display = msg ? 'block' : 'none';
}

async function tryGetCamera(constraints) {
    return navigator.mediaDevices.getUserMedia(constraints);
}

async function startCamera(type) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        setCamStatus(type, '❌ Browser Anda tidak mendukung akses kamera.', 'red');
        return;
    }

    if (streams[type]) {
        streams[type].getTracks().forEach(t => t.stop());
        streams[type] = null;
    }

    setCamStatus(type, '⏳ Membuka kamera...', 'blue');

    // Fallback chain: rear → front → any
    const constraintsList = [
        { video: { facingMode: { ideal: 'environment' } }, audio: false },  // rear (preferred)
        { video: { facingMode: { ideal: 'user' } },        audio: false },  // front fallback
        { video: true,                                     audio: false }   // any camera
    ];

    let stream = null;
    let lastError = null;

    for (const constraints of constraintsList) {
        try {
            stream = await tryGetCamera(constraints);
            break; // success
        } catch (err) {
            lastError = err;
        }
    }

    if (!stream) {
        let errMsg = '❌ Tidak dapat mengakses kamera.';
        if (lastError) {
            if (lastError.name === 'NotFoundError' || lastError.name === 'DevicesNotFoundError') {
                errMsg = '❌ Kamera tidak ditemukan pada perangkat ini.';
            } else if (lastError.name === 'NotAllowedError' || lastError.name === 'PermissionDeniedError') {
                errMsg = '❌ Akses kamera ditolak. Izinkan browser menggunakan kamera lalu coba lagi.';
            } else if (lastError.name === 'NotReadableError') {
                errMsg = '❌ Kamera sedang digunakan aplikasi lain. Tutup aplikasi lain dan coba lagi.';
            } else if (lastError.name === 'OverconstrainedError') {
                errMsg = '❌ Konfigurasi kamera tidak didukung perangkat ini.';
            } else {
                errMsg = '❌ Error: ' + lastError.message;
            }
        }
        setCamStatus(type, errMsg, 'red');
        return;
    }

    streams[type] = stream;
    setCamStatus(type, '✅ Kamera aktif', 'green');

    if (type === 'location') {
        const video = document.getElementById('video-location');
        video.srcObject = stream;
        video.style.display = 'block';
        document.getElementById('cam-location-placeholder').style.display = 'none';
        document.getElementById('photo-location-preview').style.display = 'none';
        document.getElementById('cam-location-result-wrapper').style.display = 'none';
        document.getElementById('capture-loc-btn').style.display = 'inline-flex';
        document.getElementById('stop-loc-btn').style.display    = 'inline-flex';
        document.getElementById('retake-loc-btn').style.display  = 'none';
    } else {
        const video = document.getElementById('video-image');
        video.srcObject = stream;
        video.style.display = 'block';
        document.getElementById('cam-img-placeholder').style.display = 'none';
        document.getElementById('photo-image-preview').style.display = 'none';
        document.getElementById('capture-img-btn').style.display = 'inline-flex';
        document.getElementById('stop-img-btn').style.display    = 'inline-flex';
        document.getElementById('retake-img-btn').style.display  = 'none';
    }
}


function capturePhoto(type) {
    const video  = document.getElementById('video-' + type);
    const canvas = document.getElementById('canvas-' + type);
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const dataUrl = canvas.toDataURL('image/png');

    stopCamera(type);

    if (type === 'location') {
        const preview = document.getElementById('photo-location-preview');
        preview.src = dataUrl; preview.style.display = 'block';
        document.getElementById('cam-location-result-wrapper').style.display = 'block';
        document.getElementById('retake-loc-btn').style.display = 'inline-flex';
        document.getElementById('capture-loc-btn').style.display = 'none';
        // Store photo as hidden field for reference (not sent as image; user types location)
    } else {
        const preview = document.getElementById('photo-image-preview');
        preview.src = dataUrl; preview.style.display = 'block';
        document.getElementById('retake-img-btn').style.display = 'inline-flex';
        document.getElementById('capture-img-btn').style.display = 'none';
        // Store base64 for submission
        document.getElementById('camera_image_data').value = dataUrl;
    }
}

function stopCamera(type) {
    if (streams[type]) {
        streams[type].getTracks().forEach(t => t.stop());
        streams[type] = null;
    }
    const video = document.getElementById('video-' + type);
    if (video) { video.srcObject = null; video.style.display = 'none'; }
    if (type === 'location') {
        document.getElementById('capture-loc-btn').style.display = 'none';
        document.getElementById('stop-loc-btn').style.display    = 'none';
    } else {
        document.getElementById('capture-img-btn').style.display = 'none';
        document.getElementById('stop-img-btn').style.display    = 'none';
    }
}

function retakePhoto(type) {
    if (type === 'location') {
        document.getElementById('photo-location-preview').style.display = 'none';
        document.getElementById('cam-location-result-wrapper').style.display = 'none';
        document.getElementById('cam-location-placeholder').style.display = 'block';
        document.getElementById('retake-loc-btn').style.display = 'none';
    } else {
        document.getElementById('photo-image-preview').style.display = 'none';
        document.getElementById('retake-img-btn').style.display = 'none';
        document.getElementById('cam-img-placeholder').style.display = 'block';
        document.getElementById('camera_image_data').value = '';
    }
    startCamera(type);
}

// =================== UPS CONDITION BADGE ===================
function updateConditionBadge(val) {
    const el = document.getElementById('condition-badge-display');
    const map = {
        'On-Normal':  { cls: 'badge-normal',  icon: '✅', text: 'UPS berjalan normal' },
        'On-Droop':   { cls: 'badge-droop',   icon: '⚠️', text: 'UPS dalam kondisi drop/melemah' },
        'Off-Broken': { cls: 'badge-broken',  icon: '❌', text: 'UPS mati / rusak' },
        'IT-Remove':  { cls: 'badge-remove',  icon: '🔧', text: 'UPS dilepas oleh tim IT' },
    };
    if (map[val]) {
        const m = map[val];
        el.innerHTML = `<span class="condition-badge ${m.cls}">${m.icon} ${val} — ${m.text}</span>`;
    } else {
        el.innerHTML = '';
    }
}

// =================== BALLOON CALENDAR ===================
let calDate = new Date();
let selectedDate = null;

const MONTHS = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const DAYS = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

function renderCalendar() {
    document.getElementById('cal-title').textContent = MONTHS[calDate.getMonth()] + ' ' + calDate.getFullYear();

    const headersEl = document.getElementById('cal-day-headers');
    headersEl.innerHTML = DAYS.map(d => `<div class="cal-day-header">${d}</div>`).join('');

    const year = calDate.getFullYear(), month = calDate.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    
    let html = '';
    for (let i = 0; i < firstDay; i++) html += `<div class="cal-day empty"></div>`;
    for (let d = 1; d <= daysInMonth; d++) {
        const isToday = (d === today.getDate() && month === today.getMonth() && year === today.getFullYear());
        const isSel   = selectedDate && (d === selectedDate.getDate() && month === selectedDate.getMonth() && year === selectedDate.getFullYear());
        const cls = isSel ? 'selected' : (isToday ? 'today' : '');
        html += `<div class="cal-day ${cls}" onclick="selectDay(${d})">${d}</div>`;
    }
    document.getElementById('cal-days').innerHTML = html;
}

function calPrevMonth() { calDate.setMonth(calDate.getMonth() - 1); renderCalendar(); }
function calNextMonth() { calDate.setMonth(calDate.getMonth() + 1); renderCalendar(); }

function selectDay(d) {
    selectedDate = new Date(calDate.getFullYear(), calDate.getMonth(), d);
    const dd = String(d).padStart(2,'0');
    const mm = String(calDate.getMonth() + 1).padStart(2,'0');
    const yyyy = calDate.getFullYear();
    document.getElementById('date_display').value = `${dd} ${MONTHS[calDate.getMonth()]} ${yyyy}`;
    document.getElementById('date_update').value  = `${yyyy}-${mm}-${dd}`;
    closeBalloonCal();
}

function selectToday() {
    calDate = new Date();
    selectDay(calDate.getDate());
}

function openBalloonCal() {
    renderCalendar();
    document.getElementById('balloon-calendar').classList.add('show');
}

function closeBalloonCal() {
    document.getElementById('balloon-calendar').classList.remove('show');
}

document.getElementById('date_display').addEventListener('click', function(e) {
    e.stopPropagation();
    openBalloonCal();
});

document.addEventListener('click', function(e) {
    const cal = document.getElementById('balloon-calendar');
    const field = document.getElementById('date_display');
    if (!cal.contains(e.target) && e.target !== field) closeBalloonCal();
});

// =================== IMAGE FILE PREVIEW ===================
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

// =================== LIGHTBOX ===================
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('show');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('show');
}
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});

// =================== FORM SUBMIT ===================
document.getElementById('ups-form').addEventListener('submit', function(e) {
    // Resolve UPS Location depending on mode
    const locFinal = document.getElementById('ups_location_final');
    if (locationMode === 'manual') {
        locFinal.value = document.getElementById('ups_location_manual').value.trim();
    } else {
        // Camera mode: use the text typed after taking photo
        const camText = document.getElementById('ups_location_camera_text');
        locFinal.value = camText ? camText.value.trim() : '';
    }

    // Remove the duplicate manual input name to avoid sending double
    document.getElementById('ups_location_manual').removeAttribute('name');

    // Validate
    if (!locFinal.value) {
        e.preventDefault();
        alert('UPS Location wajib diisi!');
        return;
    }
    if (!document.getElementById('ups_conditions').value) {
        e.preventDefault();
        alert('UPS Conditions wajib dipilih!');
        return;
    }
    if (!document.getElementById('date_update').value) {
        e.preventDefault();
        alert('Date Update wajib dipilih!');
        return;
    }
});

// Initialize calendar today
selectToday();

// =================== EXPORT CSV ===================
function exportCSV() {
    const table = document.getElementById('ups-data-table');
    if (!table) { alert('Tidak ada data untuk diekspor.'); return; }

    let csv = [];
    // Header
    const headers = [];
    table.querySelectorAll('thead tr th').forEach(th => {
        const txt = th.innerText.trim();
        if (txt.toLowerCase() !== 'image') headers.push('"' + txt + '"');
    });
    csv.push(headers.join(','));

    // Rows
    table.querySelectorAll('tbody tr').forEach(tr => {
        const cells = tr.querySelectorAll('td');
        const row = [];
        cells.forEach((td, idx) => {
            // Skip the image column (last column)
            if (idx === cells.length - 1) return;
            // Get text only (strip badge icons/HTML)
            const text = td.innerText.replace(/[\r\n]+/g, ' ').trim();
            row.push('"' + text.replace(/"/g, '""') + '"');
        });
        csv.push(row.join(','));
    });

    const csvContent = '\uFEFF' + csv.join('\r\n'); // BOM for Excel UTF-8
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const link = document.createElement('a');
    const now  = new Date();
    const ts   = now.getFullYear() + ('0'+(now.getMonth()+1)).slice(-2) + ('0'+now.getDate()).slice(-2) +
                 '_' + ('0'+now.getHours()).slice(-2) + ('0'+now.getMinutes()).slice(-2);
    link.setAttribute('href', url);
    link.setAttribute('download', 'UPS_Checklist_' + ts + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// =================== PRINT ===================
function printTable() {
    window.print();
}
</script>

<?= $this->endSection() ?>
