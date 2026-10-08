<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>Tambah Data CCTV Baru</h3>
        <div>
            <a href="<?= site_url('cctv/downloadTemplate') ?>" class="btn-vlan" style="background-color:#28a745; color:white; padding:8px 15px; margin-right:10px; text-decoration:none; display:inline-block; border-radius:4px; font-weight:bold; font-size:14px;">⬇️ Download Template</a>
            <button type="button" class="btn-vlan" style="background:#17a2b8; color:white; padding:8px 15px; border:none; cursor:pointer; border-radius:4px; font-weight:bold; font-size:14px;" onclick="openImportModal()">📥 Import Excel</button>
        </div>
    </div>
    
    <div style="max-width: 600px;">
        <?php 
            $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('cctv');
            if (strpos($ref, 'cctv/edit') !== false || strpos($ref, 'cctv/create') !== false) {
                $ref = site_url('cctv');
            }
        ?>
        <form action="<?= site_url('cctv/store') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="return_url" value="<?= esc($ref) ?>">
            
            <div class="form-group">
                <label>Pilih Network VLAN</label>
                <select id="vlan-select" required>
                    <option value="">-- Pilih VLAN --</option>
                    <?php foreach ($vlanList as $v): ?>
                        <option value="<?= esc($v['network_ip']) ?>"><?= esc($v['nama_vlan']) ?> (<?= esc($v['network_ip']) ?>.xxx)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>IP Address Lengkap</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="ip_prefix" style="width: 120px; background-color: #e9ecef; cursor: not-allowed;" readonly>
                    <span style="font-weight: bold; font-size: 20px;">.</span>
                    <input type="number" id="ip_suffix" min="1" max="254" placeholder="1-254" required style="width: 100px;">
                </div>
                <input type="hidden" name="ip_address" id="full_ip" required>
                <small style="color: #dc3545; display: none;" id="ip-error">Format IP tidak valid!</small>
            </div>

            <div class="form-group">
                <label>Channel NVR</label>
                <input type="number" name="channel" min="1" max="64" placeholder="Contoh: 1, 2, 3..." required>
            </div>
            
            <div class="form-group">
                <label>Pilih NVR Induk</label>
                <select name="nvr" required>
                    <option value="">-- Pilih NVR --</option>
                    <?php foreach ($nvrList as $n): ?>
                        <option value="<?= esc($n['nama_nvr']) ?>"><?= esc($n['nama_nvr']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nama CCTV</label>
                <input type="text" name="nama_cctv" placeholder="Contoh: Hikvision 2MP" required>
            </div>
            
            <div class="form-group">
                <label>Posisi / Lokasi Fisik</label>
                <input type="text" name="posisi" placeholder="Contoh: Depan Lobby Utara" required>
            </div>
            
            <div class="form-group">
                <label>Keterangan Tambahan</label>
                <input type="text" name="keterangan" placeholder="Contoh: Menghadap ke Jalan">
            </div>
            
            <div class="form-group">
                <label>Gambar Ilustrasi View Pointing (Opsional)</label>
                <input type="file" name="pointing_image" accept="image/*" style="background:#fff;">
                <small style="color:#777; display:block; margin-top:4px;">Format: JPG, PNG. Maks: 2MB.</small>
            </div>
            
            <hr style="margin: 20px 0; border: 1px solid #ddd;">
            <h4 style="margin-bottom: 15px; color: #0077b6;">🎥 Konfigurasi Streaming CCTV (Opsional)</h4>
            
            <div class="form-group">
                <label>Tipe Stream</label>
                <select name="stream_type">
                    <option value="mjpeg">HTTP MJPEG (Format Gambar Bergerak Langsung)</option>
                    <option value="rtsp">RTSP (Real Time Streaming Protocol)</option>
                    <option value="hls">HLS (HTTP Live Streaming)</option>
                </select>
                <small style="color: #666;">Jika Anda tidak yakin, gunakan RTSP atau kosongi saja.</small>
            </div>
            
            <div class="form-group">
                <label>Stream URL</label>
                <input type="text" name="stream_url" placeholder="Contoh: rtsp://192.168.1.10:554/Streaming/Channels/101">
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Username Stream</label>
                    <input type="text" name="stream_user" placeholder="Contoh: admin">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Password Stream</label>
                    <input type="password" name="stream_pass" placeholder="Contoh: pass123">
                </div>
            </div>
            
            <button type="submit" id="btn-submit" disabled style="background-color: #6c757d; cursor: not-allowed;">Simpan Data</button>
            <a href="<?= esc($ref) ?>" style="margin-left: 10px; color: #dc3545; text-decoration: none; font-weight: bold;">Batal</a>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const vlanSelect = document.getElementById('vlan-select');
        const ipPrefix = document.getElementById('ip_prefix');
        const ipSuffix = document.getElementById('ip_suffix');
        const fullIp = document.getElementById('full_ip');
        const btnSubmit = document.getElementById('btn-submit');
        const ipError = document.getElementById('ip-error');
        
        function validateIp() {
            let prefix = ipPrefix.value;
            let suffix = parseInt(ipSuffix.value);
            
            if (prefix && suffix > 0 && suffix < 255) {
                fullIp.value = prefix + '.' + suffix;
                btnSubmit.disabled = false;
                btnSubmit.style.backgroundColor = '#0077b6';
                btnSubmit.style.cursor = 'pointer';
                ipError.style.display = 'none';
            } else {
                fullIp.value = '';
                btnSubmit.disabled = true;
                btnSubmit.style.backgroundColor = '#6c757d';
                btnSubmit.style.cursor = 'not-allowed';
                
                if (ipSuffix.value !== '') {
                    ipError.style.display = 'block';
                }
            }
        }
        
        vlanSelect.addEventListener('change', function() {
            ipPrefix.value = this.value;
            validateIp();
        });
        
        ipSuffix.addEventListener('input', validateIp);
    });
</script>

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

<script>
function openImportModal() {
    document.getElementById('importModalCctv').style.display = 'block';
}
function closeImportModal() {
    document.getElementById('importModalCctv').style.display = 'none';
}
</script>
<?= $this->endSection() ?>
