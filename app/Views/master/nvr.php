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
</style>

<div class="right-frame">
    <h3><?= isset($editData) ? "Edit Master NVR" : "Kelola Master NVR" ?></h3><br>
    
    <?php if ($page_perm !== 'R'): ?>
    <form action="<?= site_url('master/nvr/store') ?>" method="POST">
        <input type="hidden" name="id_nvr" value="<?= isset($editData) ? $editData['id'] : '' ?>">
        
        <div class="form-group">
            <label>Nama NVR</label>
            <input type="text" name="nama_nvr" value="<?= isset($editData) ? esc($editData['nama_nvr']) : '' ?>" placeholder="Contoh: NVR-A" required>
        </div>

        <div class="form-group">
            <label>IP Address NVR</label>
            <input type="text" name="ip_address" value="<?= isset($editData) ? esc($editData['ip_address'] ?? '') : '' ?>" placeholder="Contoh: 192.168.1.10">
        </div>
        
        <hr style="margin: 20px 0; border: 1px solid #ddd;">
        <h4 style="margin-bottom: 15px; color: #0077b6;">🎥 Konfigurasi Streaming NVR (Opsional)</h4>
        
        <div class="form-group">
            <label>Tipe Stream NVR</label>
            <select name="stream_type">
                <option value="mjpeg" <?= (isset($editData['stream_type']) && $editData['stream_type'] == 'mjpeg') ? 'selected' : '' ?>>HTTP MJPEG (Format Gambar Bergerak Langsung)</option>
                <option value="rtsp" <?= (isset($editData['stream_type']) && $editData['stream_type'] == 'rtsp') ? 'selected' : '' ?>>RTSP (Real Time Streaming Protocol)</option>
                <option value="hls" <?= (isset($editData['stream_type']) && $editData['stream_type'] == 'hls') ? 'selected' : '' ?>>HLS (HTTP Live Streaming)</option>
            </select>
            <small style="color: #666;">Pilih MJPEG untuk hasil terbaik di browser.</small>
        </div>
        
        <div class="form-group">
            <label>Stream URL (Zero Channel)</label>
            <input type="text" name="stream_url" value="<?= isset($editData) ? esc($editData['stream_url'] ?? '') : '' ?>" placeholder="Contoh: Kosongkan untuk Auto (jika MJPEG)">
        </div>
        
        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div class="form-group" style="flex: 1;">
                <label>Username NVR</label>
                <input type="text" name="stream_user" value="<?= isset($editData) ? esc($editData['stream_user'] ?? '') : '' ?>" placeholder="Contoh: admin">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Password NVR</label>
                <input type="password" name="stream_pass" value="<?= isset($editData) ? esc($editData['stream_pass'] ?? '') : '' ?>" placeholder="Contoh: pass123">
            </div>
        </div>
        
        <button type="submit"><?= isset($editData) ? "Update Data NVR" : "Tambahkan NVR Baru" ?></button>
        
        <?php if (isset($editData)): ?>
            <a href="<?= site_url('master/nvr') ?>" style="margin-left: 10px; color: #dc3545; text-decoration: none; font-weight: bold;">Batal Edit</a>
        <?php endif; ?>
    </form>
    <?php else: ?>
    <div style="background:#fff3cd; color:#856404; padding:10px; border-radius:4px; border:1px solid #ffeeba;">
        Melihat dalam mode <b>Read-Only</b>. Anda tidak memiliki izin untuk menambah atau mengedit NVR.
    </div>
    <?php endif; ?>

    <hr style="margin: 30px 0;">

    <h3>Daftar Button NVR</h3>
    
    <div style="margin-top: 15px; margin-bottom: 30px;">
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <?php if (!empty($nvrList)): ?>
                <?php foreach($nvrList as $row): ?>
                    <a href="#" class="btn-nvr"><?= esc($row['nama_nvr']) ?></a>
                <?php endforeach; ?>
            <?php else: ?>
                <span style="color:#888;">Belum ada data NVR.</span>
            <?php endif; ?>
        </div>
    </div>

    <h3>Tabel NVR Terdaftar</h3>
    <table style="max-width: 500px;">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th>Nama NVR</th>
                <th width="25%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($nvrList)): ?>
                <?php $no = 1; foreach($nvrList as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><b><?= esc($row['nama_nvr']) ?></b></td>
                        <td>
                        <?php if ($page_perm !== 'R'): ?>
                            <a href="<?= site_url('master/nvr?edit_nvr='.$row['id']) ?>" class="btn-edit">Edit</a> | 
                            <a href="<?= site_url('master/nvr/delete/'.$row['id']) ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus NVR ini?')">Hapus</a>
                        <?php else: ?>
                            <span style='color:#999; font-size:12px; font-style:italic;'>Akses Baca</span>
                        <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='3' style='text-align:center;'>Belum ada data NVR.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
