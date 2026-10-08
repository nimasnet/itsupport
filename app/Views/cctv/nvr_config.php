<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    .nvr-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .nvr-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
    .nvr-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
</style>

<div class="right-frame">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3>⚙️ NVR Configuration (Streaming)</h3>
        <span style="background: #e9ecef; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: bold;">Total NVR Tersedia: <?= count($nvrList) ?></span>
    </div>

    <?php if ($editData): ?>
        <!-- Form Edit NVR -->
        <div class="nvr-card" style="border-top: 4px solid #17a2b8;">
            <div class="nvr-header">
                <div class="nvr-title">Edit Konfigurasi: <?= esc($editData['nama_nvr']) ?></div>
                <a href="<?= site_url('cctv/nvr-config') ?>" style="color: #dc3545; text-decoration: none; font-weight: bold;">Tutup ✕</a>
            </div>
            <form action="<?= site_url('cctv/nvr-config-update') ?>" method="POST">
                <input type="hidden" name="id_nvr" value="<?= $editData['id'] ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label>Nama NVR</label>
                        <input type="text" name="nama_nvr" value="<?= esc($editData['nama_nvr']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>IP Address NVR</label>
                        <input type="text" name="ip_address" value="<?= esc($editData['ip_address'] ?? '') ?>" placeholder="192.168.1.10">
                    </div>
                </div>

                <div class="form-group">
                    <label>Tipe Stream NVR</label>
                    <select name="stream_type">
                        <option value="mjpeg" <?= (isset($editData['stream_type']) && $editData['stream_type'] == 'mjpeg') ? 'selected' : '' ?>>HTTP MJPEG (Format Gambar Bergerak Langsung)</option>
                        <option value="rtsp" <?= (isset($editData['stream_type']) && $editData['stream_type'] == 'rtsp') ? 'selected' : '' ?>>RTSP (Real Time Streaming Protocol)</option>
                        <option value="hls" <?= (isset($editData['stream_type']) && $editData['stream_type'] == 'hls') ? 'selected' : '' ?>>HLS (HTTP Live Streaming)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Stream URL (Zero Channel / Dinamis)</label>
                    <input type="text" name="stream_url" value="<?= esc($editData['stream_url'] ?? '') ?>" placeholder="Kosongkan untuk Default Hikvision">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Kosongkan untuk menggunakan format bawaan Hikvision. <br>
                        Gunakan placeholder <b>{channel}</b> pada URL kustom Anda agar nomor channel yang dipilih di layar dikirim ke NVR.<br>
                        <b>Contoh Dahua:</b> <code>http://IP_NVR/cgi-bin/mjpg/video.cgi?channel={channel}&subtype=1</code>
                    </small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label>Username NVR</label>
                        <input type="text" name="stream_user" value="<?= esc($editData['stream_user'] ?? '') ?>" placeholder="admin">
                    </div>
                    <div class="form-group">
                        <label>Password NVR</label>
                        <input type="password" name="stream_pass" value="<?= esc($editData['stream_pass'] ?? '') ?>" placeholder="password">
                    </div>
                </div>

                <button type="submit" style="background: #17a2b8; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Simpan Konfigurasi</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Daftar NVR -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
        <?php foreach($nvrList as $nvr): ?>
            <div class="nvr-card" <?= ($editData && $editData['id'] == $nvr['id']) ? 'style="border-color: #17a2b8; background: #f8f9fa;"' : '' ?>>
                <div class="nvr-header">
                    <div class="nvr-title">🖥️ <?= esc($nvr['nama_nvr']) ?></div>
                    <a href="<?= site_url('cctv/nvr-config?edit=' . $nvr['id']) ?>" style="background: #e2e8f0; color: #333; padding: 4px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold;">Ubah Config</a>
                </div>
                <div style="font-size: 13px; color: #555; line-height: 1.6;">
                    <div><b>IP:</b> <span style="color: #007bff;"><?= esc($nvr['ip_address'] ?? 'Belum Diatur') ?></span></div>
                    <div><b>Tipe:</b> <span style="background: #d4edda; color: #155724; padding: 1px 5px; border-radius: 3px;"><?= strtoupper(esc($nvr['stream_type'] ?? 'MJPEG')) ?></span></div>
                    <div><b>Auth:</b> <?= !empty($nvr['stream_user']) ? '✅ Terkonfigurasi' : '❌ Belum Diatur' ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($nvrList)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 30px; color: #888;">
                Belum ada data NVR di sistem. Anda dapat menambahkannya melalui menu <b>Master NVR</b>.
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
