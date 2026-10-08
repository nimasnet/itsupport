<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="right-frame">
    <h3>Edit Data CCTV</h3><br>
    
    <div style="max-width: 600px;">
        <?php 
            $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('cctv');
            if (strpos($ref, 'cctv/edit') !== false || strpos($ref, 'cctv/create') !== false) {
                $ref = site_url('cctv');
            }
        ?>
        <form action="<?= site_url('cctv/update/'.$cctv['id']) ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="return_url" value="<?= esc($ref) ?>">
            
            <?php
            // Memecah IP Address untuk mengisi Form Prefix dan Suffix
            $ip_parts = explode('.', $cctv['ip_address']);
            if (count($ip_parts) == 4) {
                $saved_prefix = $ip_parts[0] . '.' . $ip_parts[1] . '.' . $ip_parts[2];
                $saved_suffix = $ip_parts[3];
            } else {
                $saved_prefix = "";
                $saved_suffix = "";
            }
            ?>
            
            <div class="form-group">
                <label>Ubah Network VLAN (Jika perlu)</label>
                <select id="vlan-select" required>
                    <?php foreach ($vlanList as $v): ?>
                        <?php $sel = ($saved_prefix == $v['network_ip']) ? 'selected' : ''; ?>
                        <option value="<?= esc($v['network_ip']) ?>" <?= $sel ?>><?= esc($v['nama_vlan']) ?> (<?= esc($v['network_ip']) ?>.xxx)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>IP Address Lengkap</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" id="ip_prefix" value="<?= esc($saved_prefix) ?>" style="width: 120px; background-color: #e9ecef; cursor: not-allowed;" readonly>
                    <span style="font-weight: bold; font-size: 20px;">.</span>
                    <input type="number" id="ip_suffix" value="<?= esc($saved_suffix) ?>" min="1" max="254" required style="width: 100px;">
                </div>
                <input type="hidden" name="ip_address" id="full_ip" value="<?= esc($cctv['ip_address']) ?>" required>
                <small style="color: #dc3545; display: none;" id="ip-error">Format IP tidak valid!</small>
            </div>

            <div class="form-group">
                <label>Channel NVR</label>
                <input type="number" name="channel" value="<?= esc($cctv['channel']) ?>" min="1" max="64" required>
            </div>
            
            <div class="form-group">
                <label>Pilih NVR Induk</label>
                <select name="nvr" required>
                    <option value="">-- Pilih NVR --</option>
                    <?php foreach ($nvrList as $n): ?>
                        <?php $sel = ($cctv['nvr'] == $n['nama_nvr']) ? 'selected' : ''; ?>
                        <option value="<?= esc($n['nama_nvr']) ?>" <?= $sel ?>><?= esc($n['nama_nvr']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Nama CCTV</label>
                <input type="text" name="nama_cctv" value="<?= esc($cctv['nama_cctv']) ?>" placeholder="Contoh: Hikvision 2MP" required>
            </div>
            
            <div class="form-group">
                <label>Posisi / Lokasi Fisik</label>
                <input type="text" name="posisi" value="<?= esc($cctv['posisi']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Keterangan Tambahan</label>
                <input type="text" name="keterangan" value="<?= esc($cctv['keterangan']) ?>">
            </div>
            
            <hr style="margin: 20px 0; border: 1px solid #ddd;">
            <h4 style="margin-bottom: 15px; color: #0077b6;">🎥 Konfigurasi Streaming CCTV (Opsional)</h4>
            
            <div class="form-group">
                <label>Tipe Stream</label>
                <select name="stream_type">
                    <option value="mjpeg" <?= (isset($cctv['stream_type']) && $cctv['stream_type'] == 'mjpeg') ? 'selected' : '' ?>>HTTP MJPEG (Format Gambar Bergerak Langsung)</option>
                    <option value="rtsp" <?= (isset($cctv['stream_type']) && $cctv['stream_type'] == 'rtsp') ? 'selected' : '' ?>>RTSP (Real Time Streaming Protocol)</option>
                    <option value="hls" <?= (isset($cctv['stream_type']) && $cctv['stream_type'] == 'hls') ? 'selected' : '' ?>>HLS (HTTP Live Streaming)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Stream URL</label>
                <input type="text" name="stream_url" value="<?= esc($cctv['stream_url'] ?? '') ?>" placeholder="Contoh: rtsp://192.168.1.10:554/Streaming/Channels/101">
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Username Stream</label>
                    <input type="text" name="stream_user" value="<?= esc($cctv['stream_user'] ?? '') ?>" placeholder="Contoh: admin">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Password Stream</label>
                    <input type="password" name="stream_pass" value="<?= esc($cctv['stream_pass'] ?? '') ?>" placeholder="Contoh: pass123">
                </div>
            </div>
            
            <div class="form-group">
                <label>Gambar Ilustrasi View Pointing (Opsional)</label>
                <?php if(!empty($cctv['view_pointing_image'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="<?= base_url('uploads/cctv_pointing/' . $cctv['view_pointing_image']) ?>" alt="View Pointing" style="max-height:100px; border-radius:4px; border:1px solid #ddd;">
                        <br><small style="color:#666;">Gambar saat ini</small>
                    </div>
                <?php endif; ?>
                <input type="file" name="pointing_image" accept="image/*" style="background:#fff;">
                <small style="color:#777; display:block; margin-top:4px;">Pilih file baru untuk mengganti gambar lama. Format: JPG, PNG. Maks: 2MB.</small>
            </div>
            
            <button type="submit" id="btn-submit">Simpan Perubahan</button>
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
<?= $this->endSection() ?>
